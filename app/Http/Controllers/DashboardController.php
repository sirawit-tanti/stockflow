<?php

namespace App\Http\Controllers;

use App\Enums\PurchaseOrderStatus;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\StockMovement;
use App\Models\StockReceipt;
use App\Models\WarehouseStock;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $lowStockQuery = WarehouseStock::query()
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('products')
                    ->whereColumn('products.id', 'warehouse_stocks.product_id')
                    ->whereColumn('warehouse_stocks.quantity', '<=', 'products.minimum_stock')
                    ->where('products.minimum_stock', '>', 0);
            });

        $summary = [
            'total_products' => Product::query()
                ->where('is_active', true)
                ->count(),

            'pending_purchase_orders' => PurchaseOrder::query()
                ->where('status', PurchaseOrderStatus::PENDING_APPROVAL)
                ->count(),

            'approved_purchase_orders' => PurchaseOrder::query()
                ->where('status', PurchaseOrderStatus::APPROVED)
                ->count(),

            'partially_received_purchase_orders' => PurchaseOrder::query()
                ->where('status', PurchaseOrderStatus::PARTIALLY_RECEIVED)
                ->count(),

            'low_stock_items' => (clone $lowStockQuery)->count(),

            'out_of_stock_items' => WarehouseStock::query()
                ->where('quantity', '<=', 0)
                ->count(),

            'total_stock_quantity' => WarehouseStock::query()
                ->sum('quantity'),

            'stock_receipts_today' => StockReceipt::query()
                ->whereDate('received_at', now()->toDateString())
                ->count(),
        ];

        $lowStockItems = WarehouseStock::query()
            ->with(['warehouse', 'product.category'])
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('products')
                    ->whereColumn('products.id', 'warehouse_stocks.product_id')
                    ->whereColumn('warehouse_stocks.quantity', '<=', 'products.minimum_stock')
                    ->where('products.minimum_stock', '>', 0);
            })
            ->orderBy('quantity')
            ->limit(5)
            ->get();

        $recentPurchaseOrders = PurchaseOrder::query()
            ->with(['supplier', 'creator'])
            ->latest()
            ->limit(5)
            ->get();

        $recentStockMovements = StockMovement::query()
            ->with(['warehouse', 'product', 'creator'])
            ->latest('movement_date')
            ->limit(5)
            ->get();

        return Inertia::render('Dashboard', [
            'summary' => $summary,
            'lowStockItems' => $lowStockItems,
            'recentPurchaseOrders' => $recentPurchaseOrders,
            'recentStockMovements' => $recentStockMovements,
        ]);
    }
}
