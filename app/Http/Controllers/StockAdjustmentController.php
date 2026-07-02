<?php

namespace App\Http\Controllers;

use App\Enums\StockMovementType;
use App\Http\Requests\StockAdjustment\StoreStockAdjustmentRequest;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Services\StockAdjustmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class StockAdjustmentController extends Controller
{
    public function __construct(
        private readonly StockAdjustmentService $stockAdjustmentService
    ) {
    }

    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $warehouseId = $request->input('warehouse_id');
        $adjustmentType = $request->input('adjustment_type');

        $stockAdjustments = StockAdjustment::query()
            ->with('warehouse', 'adjuster')
            ->withCount('items')
            ->when($search, function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('adjustment_number', 'like', "%{$search}%")
                        ->orWhere('reason', 'like', "%{$search}%")
                        ->orWhereHas('warehouse', function ($query) use ($search) {
                            $query->where('code', 'like', "%{$search}%")
                                ->orWhere('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($warehouseId, function ($query, string $warehouseId) {
                $query->where('warehouse_id', $warehouseId);
            })
            ->when($adjustmentType, function ($query, string $adjustmentType) {
                $query->where('adjustment_type', $adjustmentType);
            })
            ->latest('adjusted_at')
            ->paginate(10)
            ->withQueryString();

        $warehouses = Warehouse::query()
            ->orderBy('name')
            ->get(['id', 'code', 'name']);

        return Inertia::render('StockAdjustments/Index', [
            'stockAdjustments' => $stockAdjustments,
            'warehouses' => $warehouses,
            'adjustmentTypes' => [
                StockMovementType::ADJUST_IN => StockMovementType::labels()[StockMovementType::ADJUST_IN],
                StockMovementType::ADJUST_OUT => StockMovementType::labels()[StockMovementType::ADJUST_OUT],
            ],
            'filters' => [
                'search' => $search,
                'warehouse_id' => $warehouseId,
                'adjustment_type' => $adjustmentType,
            ]
        ]);
    }

    public function create(): Response
    {
        $warehouses = Warehouse::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'code', 'name']);

        $products = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'product_category_id', 'sku', 'name', 'unit']);

        return Inertia::render('StockAdjustments/Create', [
            'warehouses' => $warehouses,
            'products' => $products,
            'adjustmentTypes' => [
                StockMovementType::ADJUST_IN => StockMovementType::labels()[StockMovementType::ADJUST_IN],
                StockMovementType::ADJUST_OUT => StockMovementType::labels()[StockMovementType::ADJUST_OUT],
            ],
        ]);
    }

    public function store(StoreStockAdjustmentRequest $request): RedirectResponse
    {
        try {
            $stockAdjustment = $this->stockAdjustmentService->create(
                $request->validated(),
                (int) Auth::id()
            );

            return redirect()
                ->route('stock-adjustments.show', $stockAdjustment)
                ->with('success', 'Stock adjustment create successfully.');
        } catch (ValidationException $exception) {
            return redirect()
                ->route('stock-adjustments.create')
                ->withInput()
                ->with('error', $exception->validator->errors()->first());
        }
    }

    public function show(StockAdjustment $stockAdjustment): Response
    {
        $stockAdjustment->load([
            'warehouse',
            'adjuster',
            'items.product.category'
        ]);

        $stockMovements = StockMovement::query()
            ->with(['product.category', 'warehouse', 'creator'])
            ->where('reference_type', StockAdjustment::class)
            ->where('reference_id', $stockAdjustment->id)
            ->latest('movement_date')
            ->get();

        return Inertia::render('StockAdjustments/Show', [
            'stockAdjustment' => $stockAdjustment,
            'stockMovements' => $stockMovements,
            'adjustmentTypes' => [
                StockMovementType::ADJUST_IN => StockMovementType::labels()[StockMovementType::ADJUST_IN],
                StockMovementType::ADJUST_OUT => StockMovementType::labels()[StockMovementType::ADJUST_OUT],
            ],
        ]);
    }
}