<?php

namespace App\Http\Controllers;

use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockMovement;
use App\Models\StockReceipt;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StockMovementController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $warehouseId = $request->input('warehouse_id');
        $movementType = $request->input('movement_type');

        $stockMovements = StockMovement::query()
            ->with([
                'warehouse',
                'product.category',
                'creator',
            ])
            ->when($search, function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->whereHas('product', function ($query) use ($search) {
                        $query->where('sku', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    })->orWhereHas('warehouse', function ($query) use ($search) {
                        $query->where('code', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    })->orWhere('note', 'like', "%{$search}%");
                });
            })
            ->when($warehouseId, function ($query, string $warehouseId) {
                $query->where('warehouse_id', $warehouseId);
            })
            ->when($movementType, function ($query, string $movementType) {
                $query->where('movement_type', $movementType);
            })
            ->latest('movement_date')
            ->paginate(10)
            ->withQueryString();

        $warehouses = Warehouse::query()
            ->orderBy('name')
            ->get(['id', 'code', 'name']);

        return Inertia::render('StockMovements/Index', [
            'stockMovements' => $stockMovements,
            'warehouses' => $warehouses,
            'movementTypes' => StockMovementType::labels(),
            'filters' => [
                'search' => $search,
                'warehouse_id' => $warehouseId,
                'movement_type' => $movementType,
            ],
        ]);
    }

    public function show(StockMovement $stockMovement): Response
    {
        $stockMovement->load([
            'warehouse',
            'product.category',
            'creator',
        ]);

        $referenceReceipt = null;
        $referenceAdjustment = null;

        if ($stockMovement->reference_type === StockReceipt::class) {
            $referenceReceipt = StockReceipt::query()
                ->with(['purchaseOrder.supplier', 'warehouse', 'receiver'])
                ->find($stockMovement->reference_id);
        }

        if ($stockMovement->reference_type === StockAdjustment::class) {
            $referenceAdjustment = StockAdjustment::query()
                ->with(['warehouse', 'adjuster'])
                ->find($stockMovement->reference_id);
        }

        return Inertia::render('StockMovements/Show', [
            'stockMovement' => $stockMovement,
            'movementTypes' => StockMovementType::labels(),
            'referenceReceipt' => $referenceReceipt,
            'referenceAdjustment' => $referenceAdjustment,
        ]);
    }
}