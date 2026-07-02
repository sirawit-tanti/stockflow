<?php

namespace App\Http\Controllers;

use App\Enums\PurchaseOrderStatus;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Reports/Index', [
            'summary' => [
                'purchase_orders' => PurchaseOrder::query()->count(),
                'pending_purchase_orders' => PurchaseOrder::query()
                    ->where('status', PurchaseOrderStatus::PENDING_APPROVAL)
                    ->count(),
                'approved_purchase_orders' => PurchaseOrder::query()
                    ->where('status', PurchaseOrderStatus::APPROVED)
                    ->count(),
                'completed_purchase_orders' => PurchaseOrder::query()
                    ->where('status', PurchaseOrderStatus::COMPLETED)
                    ->count(),

                'stock_items' => WarehouseStock::query()->count(),
                'total_stock_quantity' => WarehouseStock::query()->sum('quantity'),
                'out_of_stock_items' => WarehouseStock::query()
                    ->where('quantity', '<=', 0)
                    ->count(),
            ],
        ]);
    }

    public function purchaseOrders(Request $request): Response
    {
        $purchaseOrders = $this->purchaseOrderQuery($request)
            ->with(['supplier', 'creator'])
            ->latest('order_date')
            ->paginate(15)
            ->withQueryString();

        $suppliers = Supplier::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Reports/PurchaseOrders', [
            'purchaseOrders' => $purchaseOrders,
            'suppliers' => $suppliers,
            'statuses' => PurchaseOrderStatus::labels(),
            'filters' => [
                'search' => $request->input('search'),
                'status' => $request->input('status'),
                'supplier_id' => $request->input('supplier_id'),
                'date_from' => $request->input('date_from'),
                'date_to' => $request->input('date_to'),
            ],
        ]);
    }

    public function exportPurchaseOrders(Request $request): StreamedResponse
    {
        $purchaseOrders = $this->purchaseOrderQuery($request)
            ->with(['supplier', 'creator'])
            ->latest('order_date')
            ->get();

        $filename = 'purchase-orders-report-' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($purchaseOrders) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'PO Number',
                'Supplier',
                'Status',
                'Order Date',
                'Expected Date',
                'Total Amount',
                'Created By',
                'Submitted At',
                'Approved At',
                'Created At',
            ]);

            foreach ($purchaseOrders as $purchaseOrder) {
                fputcsv($handle, [
                    $purchaseOrder->po_number,
                    $purchaseOrder->supplier?->name,
                    $purchaseOrder->status,
                    optional($purchaseOrder->order_date)->format('Y-m-d'),
                    optional($purchaseOrder->expected_date)->format('Y-m-d'),
                    $purchaseOrder->total_amount,
                    $purchaseOrder->creator?->name,
                    optional($purchaseOrder->submitted_at)->format('Y-m-d H:i:s'),
                    optional($purchaseOrder->approved_at)->format('Y-m-d H:i:s'),
                    optional($purchaseOrder->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function purchaseOrderQuery(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $supplierId = $request->input('supplier_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        return PurchaseOrder::query()
            ->when($search, function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('po_number', 'like', "%{$search}%")
                        ->orWhereHas('supplier', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status, function ($query, string $status) {
                $query->where('status', $status);
            })
            ->when($supplierId, function ($query, string $supplierId) {
                $query->where('supplier_id', $supplierId);
            })
            ->when($dateFrom, function ($query, string $dateFrom) {
                $query->whereDate('order_date', '>=', $dateFrom);
            })
            ->when($dateTo, function ($query, string $dateTo) {
                $query->whereDate('order_date', '<=', $dateTo);
            });
    }

    public function stockBalances(Request $request): Response
    {
        $warehouseStocks = $this->stockBalanceQuery($request)
            ->with(['warehouse', 'product.category'])
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        $warehouses = Warehouse::query()
            ->orderBy('name')
            ->get(['id', 'code', 'name']);

        return Inertia::render('Reports/StockBalances', [
            'warehouseStocks' => $warehouseStocks,
            'warehouses' => $warehouses,
            'stockStatuses' => [
                'available' => 'Available',
                'low_stock' => 'Low Stock',
                'out_of_stock' => 'Out of Stock',
            ],
            'filters' => [
                'search' => $request->input('search'),
                'warehouse_id' => $request->input('warehouse_id'),
                'stock_status' => $request->input('stock_status'),
            ],
        ]);
    }

    public function exportStockBalances(Request $request): StreamedResponse
    {
        $warehouseStocks = $this->stockBalanceQuery($request)
            ->with(['warehouse', 'product.category'])
            ->latest('updated_at')
            ->get();

        $filename = 'stock-balance-report-' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($warehouseStocks) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'Warehouse Code',
                'Warehouse Name',
                'SKU',
                'Product Name',
                'Category',
                'Quantity',
                'Minimum Stock',
                'Stock Status',
                'Last Updated',
            ]);

            foreach ($warehouseStocks as $warehouseStock) {
                fputcsv($handle, [
                    $warehouseStock->warehouse?->code,
                    $warehouseStock->warehouse?->name,
                    $warehouseStock->product?->sku,
                    $warehouseStock->product?->name,
                    $warehouseStock->product?->category?->name,
                    $warehouseStock->quantity,
                    $warehouseStock->product?->minimum_stock,
                    $this->resolveStockStatus($warehouseStock),
                    optional($warehouseStock->updated_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function stockBalanceQuery(Request $request)
    {
        $search = $request->input('search');
        $warehouseId = $request->input('warehouse_id');
        $stockStatus = $request->input('stock_status');

        return WarehouseStock::query()
            ->when($search, function ($query, string $search) {
                $query->whereHas('product', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($warehouseId, function ($query, string $warehouseId) {
                $query->where('warehouse_id', $warehouseId);
            })
            ->when($stockStatus, function ($query, string $stockStatus) {
                if ($stockStatus === 'out_of_stock') {
                    $query->where('quantity', '<=', 0);
                }

                if ($stockStatus === 'low_stock') {
                    $query->where('quantity', '>', 0)
                        ->whereHas('product', function ($query) {
                            $query->where('minimum_stock', '>', 0)
                                ->whereColumn('warehouse_stocks.quantity', '<=', 'products.minimum_stock');
                        });
                }

                if ($stockStatus === 'available') {
                    $query->where('quantity', '>', 0)
                        ->whereHas('product', function ($query) {
                            $query->where(function ($query) {
                                $query->where('minimum_stock', '<=', 0)
                                    ->orWhereColumn('warehouse_stocks.quantity', '>', 'products.minimum_stock');
                            });
                        });
                }
            });
    }

    private function resolveStockStatus(WarehouseStock $warehouseStock): string
    {
        $quantity = (float) $warehouseStock->quantity;
        $minimumStock = (float) ($warehouseStock->product?->minimum_stock ?? 0);

        if ($quantity <= 0) {
            return 'Out of Stock';
        }

        if ($minimumStock > 0 && $quantity <= $minimumStock) {
            return 'Low Stock';
        }

        return 'Available';
    }
}