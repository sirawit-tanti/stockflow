<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrderActionController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\StockReceiptController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WarehouseStockController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified', 'permission:dashboard.view'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('reports')
        ->name('reports.')
        ->middleware('permission:report.view')
        ->group(function () {
            Route::get('/', [ReportController::class, 'index'])
                ->name('index');

            Route::get('purchase-orders', [ReportController::class, 'purchaseOrders'])
                ->name('purchase-orders');

            Route::get('purchase-orders/export', [ReportController::class, 'exportPurchaseOrders'])
                ->name('purchase-orders.export')
                ->middleware('permission:report.export');

            Route::get('stock-balances', [ReportController::class, 'stockBalances'])
                ->name('stock-balances');

            Route::get('stock-balances/export', [ReportController::class, 'exportStockBalances'])
                ->name('stock-balances.export')
                ->middleware('permission:report.export');
        });

    Route::get('users', [UserManagementController::class, 'index'])
        ->name('users.index')
        ->middleware('permission:user.manage');

    Route::get('users/create', [UserManagementController::class, 'create'])
        ->name('users.create')
        ->middleware('permission:user.manage');

    Route::post('users', [UserManagementController::class, 'store'])
        ->name('users.store')
        ->middleware('permission:user.manage');

    Route::get('users/{user}/edit', [UserManagementController::class, 'edit'])
        ->name('users.edit')
        ->middleware('permission:user.manage');

    Route::put('users/{user}', [UserManagementController::class, 'update'])
        ->name('users.update')
        ->middleware('permission:user.manage');

    Route::get('audit-logs', [AuditLogController::class, 'index'])
        ->name('audit-logs.index')
        ->middleware('permission:audit-log.view');

    Route::get('audit-logs/{auditLog}', [AuditLogController::class, 'show'])
        ->name('audit-logs.show')
        ->middleware('permission:audit-log.view');

    Route::resource('product-categories', ProductCategoryController::class)
        ->middleware('permission:product-category.manage');

    Route::resource('products', ProductController::class)
        ->middleware('permission:product.manage');

    Route::resource('suppliers', SupplierController::class)
        ->middleware('permission:supplier.manage');

    Route::resource('warehouses', WarehouseController::class)
        ->middleware('permission:warehouse.manage');

    Route::get('warehouse-stocks', [WarehouseStockController::class, 'index'])
        ->name('warehouse-stocks.index')
        ->middleware('permission:warehouse-stock.view');

    Route::post('warehouse-stocks/sync', [WarehouseStockController::class, 'sync'])
        ->name('warehouse-stocks.sync')
        ->middleware('permission:warehouse-stock.sync');

    Route::post('purchase-orders/{purchaseOrder}/submit', [PurchaseOrderActionController::class, 'submit'])
        ->name('purchase-orders.submit')
        ->middleware('permission:purchase-order.submit');

    Route::post('purchase-orders/{purchaseOrder}/approve', [PurchaseOrderActionController::class, 'approve'])
        ->name('purchase-orders.approve')
        ->middleware('permission:purchase-order.approve');

    Route::post('purchase-orders/{purchaseOrder}/reject', [PurchaseOrderActionController::class, 'reject'])
        ->name('purchase-orders.reject')
        ->middleware('permission:purchase-order.reject');

    Route::post('purchase-orders/{purchaseOrder}/cancel', [PurchaseOrderActionController::class, 'cancel'])
        ->name('purchase-orders.cancel')
        ->middleware('permission:purchase-order.cancel');

    Route::get('purchase-orders/create', [PurchaseOrderController::class, 'create'])
        ->name('purchase-orders.create')
        ->middleware('permission:purchase-order.create');

    Route::post('purchase-orders', [PurchaseOrderController::class, 'store'])
        ->name('purchase-orders.store')
        ->middleware('permission:purchase-order.create');

    Route::get('purchase-orders/{purchaseOrder}/edit', [PurchaseOrderController::class, 'edit'])
        ->name('purchase-orders.edit')
        ->middleware('permission:purchase-order.edit');

    Route::put('purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'update'])
        ->name('purchase-orders.update')
        ->middleware('permission:purchase-order.edit');

    Route::patch('purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'update'])
        ->name('purchase-orders.update')
        ->middleware('permission:purchase-order.edit');

    Route::delete('purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])
        ->name('purchase-orders.destroy')
        ->middleware('permission:purchase-order.delete');

    Route::get('purchase-orders', [PurchaseOrderController::class, 'index'])
        ->name('purchase-orders.index')
        ->middleware('permission:purchase-order.view');

    Route::get('purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show'])
        ->name('purchase-orders.show')
        ->middleware('permission:purchase-order.view');

    Route::get('stock-receipts', [StockReceiptController::class, 'index'])
        ->name('stock-receipts.index')
        ->middleware('permission:stock-receipt.view');

    Route::get('stock-receipts/{stockReceipt}', [StockReceiptController::class, 'show'])
        ->name('stock-receipts.show')
        ->middleware('permission:stock-receipt.view');

    Route::get('purchase-orders/{purchaseOrder}/receive', [StockReceiptController::class, 'create'])
        ->name('purchase-orders.receive.create')
        ->middleware('permission:stock-receipt.create');

    Route::post('purchase-orders/{purchaseOrder}/receive', [StockReceiptController::class, 'store'])
        ->name('purchase-orders.receive.store')
        ->middleware('permission:stock-receipt.create');

    Route::get('stock-movements', [StockMovementController::class, 'index'])
        ->name('stock-movements.index')
        ->middleware('permission:stock-movement.view');

    Route::get('stock-movements/{stockMovement}', [StockMovementController::class, 'show'])
        ->name('stock-movements.show')
        ->middleware('permission:stock-movement.view');

    Route::get('stock-adjustments', [StockAdjustmentController::class, 'index'])
        ->name('stock-adjustments.index')
        ->middleware('permission:stock-adjustment.view');

    Route::get('stock-adjustments/create', [StockAdjustmentController::class, 'create'])
        ->name('stock-adjustments.create')
        ->middleware('permission:stock-adjustment.create');

    Route::post('stock-adjustments', [StockAdjustmentController::class, 'store'])
        ->name('stock-adjustments.store')
        ->middleware('permission:stock-adjustment.create');

    Route::get('stock-adjustments/{stockAdjustment}', [StockAdjustmentController::class, 'show'])
        ->name('stock-adjustments.show')
        ->middleware('permission:stock-adjustment.view');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';