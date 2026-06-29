<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class WarehouseStockController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $warehouseId = $request->input('warehouse_id');
        $stockStatus = $request->input('stock_status');

        $warehouseStocks = WarehouseStock::query()
            ->with([
                'warehouse',
                'product.category'
            ])
            ->when($search, function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->whereHas('product', function ($query) use ($search) {
                        $query->where('sku', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    })->orWhereHas('warehouse', function ($query) use ($search) {
                        $query->where('code', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    });
                });
            })
            ->when($warehouseId, function ($query, string $warehouseId) {
                $query->where('warehouse_id', $warehouseId);
            })
            ->when($stockStatus === 'out_of_stock', function ($query) {
                $query->where('quantity', '<=', 0);
            })
            ->when($stockStatus === 'available', function ($query) {
                $query->where('quantity', '>', 0)
                    ->whereNotExists(function ($query) {
                        $query->selectRaw('1')
                            ->from('products')
                            ->whereColumn('products.id', 'warehouse_stocks.product_id')
                            ->whereColumn('warehouse_stocks.quantity', '<=', 'products.minimum_stock')
                            ->where('products.minimum_stock', '>', 0);
                    });
            })
            ->when($stockStatus === 'low_stock', function ($query) {
                $query->whereExists(function ($query) {
                    $query->selectRaw('1')
                        ->from('products')
                        ->whereColumn('product_id', 'warehouse_stocks.product_id')
                        ->whereColumn('warehouse_stocks.quantity', '<=', 'products.minimum_stock')
                        ->where('products.minimum_stock', '>', 0);
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $warehouses = Warehouse::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'code', 'name']);

        $summary = [
            'total_records' => WarehouseStock::count(),
            'total_quantity' => WarehouseStock::sum('quantity'),
            'out_of_stock_count' => WarehouseStock::where('quantity', '<=', 0)->count(),
            'low_stock_count' => WarehouseStock::query()
                ->whereExists(function ($query) {
                    $query->selectRaw('1')
                        ->from('products')
                        ->whereColumn('products.id', 'warehouse_stocks.product_id')
                        ->whereColumn('warehouse_stocks.quantity', '<=', 'products.minimum_stock')
                        ->where('products.minimum_stock', '>', 0);
                })
                ->count(),
        ];

        return Inertia::render('WarehouseStocks/Index', [
            'warehouseStocks' => $warehouseStocks,
            'warehouses' => $warehouses,
            'filters' => [
                'search' => $search,
                'warehouse_id' => $warehouseId,
                'stock_status' => $stockStatus,
            ],
            'summary' => $summary
        ]);
    }

    public function sync(): RedirectResponse
    {
        $warehouses = Warehouse::query()
            ->where('is_active', true)
            ->get(['id']);

        $products = Product::query()
            ->where('is_active', true)
            ->get(['id']);

        $createdCount = DB::transaction(function () use ($warehouses, $products) {
            $createdCount = 0;

            foreach ($warehouses as $warehouse) {
                foreach ($products as  $product) {
                    $warehouseStock = WarehouseStock::firstOrCreate(
                        [
                            'warehouse_id' => $warehouse->id,
                            'product_id' => $product->id,
                        ],
                        [
                            'quantity' => 0,
                        ]
                    );

                    if ($warehouseStock->wasRecentlyCreated) {
                        $createdCount++;
                    }
                }
            }

            return $createdCount;
        });

        return redirect()
            ->route('warehouse-stocks.index')
            ->with('success', "{$createdCount} stock balance records initialized successfully.");
    }
}