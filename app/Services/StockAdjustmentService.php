<?php

namespace App\Services;

use App\Enums\StockMovementType;
use App\Models\StockAdjustment;
use App\Models\StockMovement;
use App\Models\WarehouseStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockAdjustmentService
{
    public function create(array $data, int $userId): StockAdjustment
    {
        return DB::transaction(function () use ($data, $userId) {
            $items = collect($data['items'])
                ->filter(fn ($item) => (float) $item['quantity'] > 0)
                ->values();

            if ($items->isEmpty()) {
                throw ValidationException::withMessages([
                    'items' => 'Please add at least one adjustment item.'
                 ]);
            }

            $stockAdjustment = StockAdjustment::create([
                'adjustment_number' => $this->generateAdjustmentNumber(),
                'warehouse_id' => $data['warehouse_id'],
                'adjustment_type' => $data['adjustment_type'],
                'adjusted_by' => $userId,
                'adjusted_at' => $data['adjusted_at'],
                'reason' => $data['reason'],
                'note' => $data['note'] ?? null,
            ]);

            foreach ($items as $item) {
                $quantity = (float) $item['quantity'];

                $stockAdjustment->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $quantity,
                ]);

                $this->applyStockAdjustment(
                    warehouseId: (int) $data['warehouse_id'],
                    productId: (int) $item['product_id'],
                    adjustmentType: $data['adjustment_type'],
                    quantity: $quantity,
                    stockAdjustment: $stockAdjustment,
                    userId: $userId,
                );
            }

            return $stockAdjustment->load([
                'warehouse',
                'adjuster',
                'items.product.category',
            ]);
        });
    }

    private function applyStockAdjustment(int $warehouseId, int $productId, string $adjustmentType, float $quantity, StockAdjustment $stockAdjustment, int $userId): void
    {
        $warehouseStock = WarehouseStock::query()
            ->where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->lockForUpdate()
            ->first();

        if (!$warehouseStock) {
            if ($adjustmentType === StockMovementType::ADJUST_OUT) {
                throw ValidationException::withMessages([
                    'items' => 'Cannot adjust out because stock balance does not exists.',
                ]);
            }

            $warehouseStock = WarehouseStock::create([
                'warehouse_id' => $warehouseId,
                'product_id' => $productId,
                'quantity' => 0,
            ]);
        }

        $quantityBefore = (float) $warehouseStock->quantity;

        $quantityChanged = $adjustmentType === StockMovementType::ADJUST_OUT ? -1 * $quantity : $quantity;

        $quantityAfter = $quantityBefore + $quantityChanged;

        if ($quantityAfter < 0) {
            throw ValidationException::withMessages([
                'items' => 'Cannot adjust out more than current stock quantity.'
            ]);
        }

        $warehouseStock->update([
            'quantity' => $quantityAfter,
        ]);

        StockMovement::create([
            'warehouse_id' => $warehouseId,
            'product_id' => $productId,
            'movement_type' => $adjustmentType,
            'reference_type' => StockAdjustment::class,
            'reference_id' => $stockAdjustment->id,
            'quantity_before' => $quantityBefore,
            'quantity_changed' => $quantityChanged,
            'quantity_after' => $quantityAfter,
            'created_by' => $userId,
            'movement_date' => $stockAdjustment->adjusted_at,
            'note' => $stockAdjustment->reason,
        ]);
    }

    private function generateAdjustmentNumber(): string
    {
        $prefix = 'SA-'.now()->format('Ymd');

        $lastAdjustmentNumber = StockAdjustment::query()
            ->where('adjustment_number', 'like', "{$prefix}-%")
            ->lockForUpdate()
            ->orderByDesc('adjustment_number')
            ->value('adjustment_number');

        if (!$lastAdjustmentNumber) {
            return "{$prefix}-0001";
        }

        $lastRunningNumber = (int) substr($lastAdjustmentNumber, -4);
        $nextRunningNumber = $lastRunningNumber + 1;

        return $prefix.'-'.str_pad((string) $nextRunningNumber, 4, '0', STR_PAD_LEFT);
    }
}