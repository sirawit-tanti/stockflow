<?php

namespace App\Services;

use App\Enums\PurchaseOrderStatus;
use App\Enums\StockMovementType;
use App\Models\PurchaseOrder;
use App\Models\StockMovement;
use App\Models\StockReceipt;
use App\Models\WarehouseStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockReceiptService
{
    public function receive(PurchaseOrder $purchaseOrder, array $data, int $userId): StockReceipt
    {
        return DB::transaction(function () use ($purchaseOrder, $data, $userId) {
            $purchaseOrder = PurchaseOrder::query()
                ->with('items')
                ->lockForUpdate()
                ->findOrFail($purchaseOrder->id);

            $this->ensureReceivable($purchaseOrder);

            $itemsToReceive = collect($data['items'])
                ->filter(fn ($item) => (float) $item['quantity'] > 0)
                ->values();

            if ($itemsToReceive->isEmpty()) {
                throw ValidationException::withMessages([
                    'items' => 'Please receive at least one item.'
                ]);
            }

            $stockReceipt = StockReceipt::create([
                'receipt_number' => $this->generateReceiptNumber(),
                'purchase_order_id' => $purchaseOrder->id,
                'warehouse_id' => $data['warehouse_id'],
                'received_by' => $userId,
                'received_at' => $data['received_at'],
                'note' => $data['note'] ?? null,
            ]);

            foreach ($itemsToReceive as $item) {
                $purchaseOrderItem = $purchaseOrder->items
                    ->firstWhere('id', (int) $item['purchase_order_item_id']);

                if (!$purchaseOrderItem) {
                    throw ValidationException::withMessages([
                        'items' => 'Invalid purchase order item.',
                    ]);
                }

                $receiveQuantity = (float) $item['quantity'];
                $orderedQuantity = (float) $purchaseOrderItem->quantity;
                $receivedQuantity = (float) $purchaseOrderItem->received_quantity;
                $remainingQuantity = $orderedQuantity - $receivedQuantity;

                if ($receiveQuantity > $remainingQuantity) {
                    throw ValidationException::withMessages([
                        'items' => "Received quantity for product ID {$purchaseOrderItem->product_id} cannot exceed remaining quantity."
                    ]);
                }

                $stockReceipt->items()->create([
                    'purchase_order_item_id' => $purchaseOrderItem->id,
                    'product_id' => $purchaseOrderItem->product_id,
                    'quantity' => $receiveQuantity,
                ]);

                $this->increaseWarehouseStock(
                    warehouseId: (int) $data['warehouse_id'],
                    productId: (int) $purchaseOrderItem->product_id,
                    quantity: $receiveQuantity,
                    stockReceipt: $stockReceipt,
                    userId: $userId,
                    note: "Receive stock from {$purchaseOrder->po_number}"
                );

                $purchaseOrderItem->update([
                    'received_quantity' => $receivedQuantity + $receiveQuantity,
                ]);
            }

            $this->updatePurchaseOrderStatus($purchaseOrder);

            return $stockReceipt->load([
                'purchaseOrder.supplier',
                'warehouse',
                'receiver',
                'items.product',
            ]);
        });
    }

    private function increaseWarehouseStock(int $warehouseId, int $productId, float $quantity, StockReceipt $stockReceipt, int $userId, ?string $note = null): void
    {
        $warehouseStock = WarehouseStock::query()
            ->where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->lockForUpdate()
            ->first();

        if (!$warehouseStock) {
            $warehouseStock = WarehouseStock::create([
                'warehouse_id' => $warehouseId,
                'product_id' => $productId,
                'quantity' => 0
            ]);
        }

        $quantityBefore = (float) $warehouseStock->quantity;
        $quantityAfter = $quantityBefore + $quantity;

        $warehouseStock->update([
            'quantity' => $quantityAfter,
        ]);

        StockMovement::create([
            'warehouse_id' => $warehouseId,
            'product_id' => $productId,
            'movement_type' => StockMovementType::RECEIVE,
            'reference_type' => StockReceipt::class,
            'reference_id' => $stockReceipt->id,
            'quantity_before' => $quantityBefore,
            'quantity_changed' => $quantity,
            'quantity_after' => $quantityAfter,
            'created_by' => $userId,
            'movement_date' => $stockReceipt->received_at,
            'note' => $note,
        ]);
    }

    private function updatePurchaseOrderStatus(PurchaseOrder $purchaseOrder): void
    {
        $items = $purchaseOrder->items()->get();

        $isCompleted = $items->every(function ($item) {
            return (float) $item->received_quantity >= (float) $item->quantity;
        });

        $purchaseOrder->update([
            'status' => $isCompleted ? PurchaseOrderStatus::COMPLETED : PurchaseOrderStatus::PARTIALLY_RECEIVED,
        ]);
    }

    private function ensureReceivable(PurchaseOrder $purchaseOrder): void
    {
        if (!in_array($purchaseOrder->status, [
            PurchaseOrderStatus::APPROVED,
            PurchaseOrderStatus::PARTIALLY_RECEIVED,
        ], true)) {
            throw ValidationException::withMessages([
                'purchase_order' => 'Only approved or partially received purchase orders can be received.'
            ]);
        }
    }

    private function generateReceiptNumber(): string
    {
        $prefix = 'SR-'.now()->format('Ymd');

        $lastReceiptNumber = StockReceipt::query()
            ->where('receipt_number', 'like', "{$prefix}-%")
            ->lockForUpdate()
            ->orderByDesc('receipt_number')
            ->value('receipt_number');
        
        if (!$lastReceiptNumber) {
            return "{$prefix}-0001";
        }

        $lastRunningNumber = (int) substr($lastReceiptNumber, -4);
        $nextRunningNumber = $lastRunningNumber + 1;

        return $prefix.'-'.str_pad((string) $nextRunningNumber, 4, '0', STR_PAD_LEFT);
    }
}