<?php

namespace App\Services;

use App\Enums\PurchaseOrderStatus;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseOrderService
{
    public function createDraft(array $data, int $userId): PurchaseOrder
    {
        return DB::transaction(function () use ($data, $userId) {
            $purchaseOrder = PurchaseOrder::create([
                'po_number' => $this->generatePoNumber(),
                'supplier_id' => $data['supplier_id'],
                'created_by' => $userId,
                'status' => PurchaseOrderStatus::DRAFT,
                'order_date' => $data['order_date'],
                'expected_date' => $data['expected_date'] ?? null,
                'note' => $data['note'] ?? null,
                'total_amount' => 0,
            ]);

            $totalAmount = $this->syncItems($purchaseOrder, $data['items']);

            $purchaseOrder->update([
                'total_amount' => $totalAmount,
            ]);

            return $purchaseOrder->load(['supplier', 'items.product']);
        });
    }

    public function updateDraft(PurchaseOrder $purchaseOrder, array $data): PurchaseOrder
    {
        $this->ensureDraft($purchaseOrder);

        return DB::transaction(function () use ($purchaseOrder, $data) {
            $purchaseOrder->update([
                'supplier_id' => $data['supplier_id'],
                'order_date' => $data['order_date'],
                'expected_date' => $data['expected_date'] ?? null,
                'note' => $data['note'] ?? null,
            ]);

            $purchaseOrder->items()->delete();

            $totalAmount = $this->syncItems($purchaseOrder, $data['items']);

            $purchaseOrder->update([
                'total_amount' => $totalAmount,
            ]);

            return $purchaseOrder->load(['supplier', 'items.product']);
        });
    }

    public function deleteDraft(PurchaseOrder $purchaseOrder): void
    {
        $this->ensureDraft($purchaseOrder);

        DB::transaction(function () use ($purchaseOrder) {
            $purchaseOrder->items()->delete();
            $purchaseOrder->delete();
        });
    }

    public function submitDraft(PurchaseOrder $purchaseOrder): PurchaseOrder
    {
        $this->ensureDraft($purchaseOrder);

        return DB::transaction(function () use ($purchaseOrder) {
            if ($purchaseOrder->items()->count() === 0) {
                throw ValidationException::withMessages([
                    'purchase_order' => 'Purchase order must have at least one item before submit.'
                ]);
            }

            $purchaseOrder->update([
                'status' => PurchaseOrderStatus::PENDING_APPROVAL,
                'submitted_at' => now(),
            ]);

            return $purchaseOrder->refresh();
        });
    }

    public function approve(PurchaseOrder $purchaseOrder, int $userId): PurchaseOrder
    {
        $this->ensurePendingApproval($purchaseOrder);

        return DB::transaction(function () use ($purchaseOrder, $userId) {
            $purchaseOrder->update([
                'status' => PurchaseOrderStatus::APPROVED,
                'approved_by' => $userId,
                'approved_at' => now(),
                'rejected_at' => null,
            ]);

            return $purchaseOrder->refresh();
        });
    }

    public function reject(PurchaseOrder $purchaseOrder): PurchaseOrder
    {
        $this->ensurePendingApproval($purchaseOrder);

        return DB::transaction(function () use ($purchaseOrder) {
            $purchaseOrder->update([
                'status' => PurchaseOrderStatus::REJECTED,
                'approved_by' => null,
                'approved_at' => null,
                'rejected_at' => now(),
            ]);

            return $purchaseOrder->refresh();
        });
    }

    public function cancel(PurchaseOrder $purchaseOrder): PurchaseOrder
    {
        if (!in_array($purchaseOrder->status, [
            PurchaseOrderStatus::DRAFT,
            PurchaseOrderStatus::PENDING_APPROVAL,
        ], true)) {
            throw ValidationException::withMessages([
                'purchase_order' => 'Only draft or pending approval purchase orders can be cancelled.'
            ]);
        }

        return DB::transaction(function () use ($purchaseOrder) {
            $purchaseOrder->update([
                'status' => PurchaseOrderStatus::CANCELLED,
            ]);

            return $purchaseOrder->refresh();
        });
    }

    private function syncItems(PurchaseOrder $purchaseOrder, array $items): float
    {
        $totalAmount = 0;

        foreach ($items as $item) {
            $quantity = (float) $item['quantity'];
            $unitPrice = (float) $item['unit_price'];
            $totalPrice = $quantity * $unitPrice;

            $purchaseOrder->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $quantity,
                'received_quantity' => 0,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
            ]);

            $totalAmount += $totalPrice;
        }

        return $totalAmount;
    }

    private function generatePoNumber(): string
    {
        $prefix = 'PO-' . now()->format('Ymd');

        $lastPoNumber = PurchaseOrder::withTrashed()
            ->where('po_number', 'like', "{$prefix}-%")
            ->lockForUpdate()
            ->orderByDesc('po_number')
            ->value('po_number');

        if (!$lastPoNumber) {
            return "{$prefix}-0001";
        }

        $lastRunningNumber = (int) substr($lastPoNumber, -4);
        $nextRunningNumber = $lastRunningNumber + 1;

        return $prefix . '-' . str_pad((string) $nextRunningNumber, 4, '0', STR_PAD_LEFT);
    }

    private function ensureDraft(PurchaseOrder $purchaseOrder): void
    {
        if ($purchaseOrder->status !== PurchaseOrderStatus::DRAFT) {
            throw ValidationException::withMessages([
                'purchase_order' => 'Only draft purchase orders can be modified.'
            ]);
        }
    }

    private function ensurePendingApproval(PurchaseOrder $purchaseOrder): void
    {
        if ($purchaseOrder->status !== PurchaseOrderStatus::PENDING_APPROVAL) {
            throw ValidationException::withMessages([
                'purchase_order' => 'Only pending approval purchase order can be approved or rejected.'
            ]);
        }
    }
}