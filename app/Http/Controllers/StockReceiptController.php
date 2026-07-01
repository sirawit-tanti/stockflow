<?php

namespace App\Http\Controllers;

use App\Enums\PurchaseOrderStatus;
use App\Http\Requests\StockReceipt\StoreStockReceiptRequest;
use App\Models\PurchaseOrder;
use App\Models\StockReceipt;
use App\Models\Warehouse;
use App\Services\StockReceiptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class StockReceiptController extends Controller
{
    public function __construct(
        private readonly StockReceiptService $stockReceiptService
    ) {
    }

    public function index(): Response
    {
        $stockReceipts = StockReceipt::query()
            ->with(['purchaseOrder.supplier', 'warehouse', 'receiver'])
            ->latest('received_at')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('StockReceipts/Index', [
            'stockReceipts' => $stockReceipts
        ]);
    }

    public function create(PurchaseOrder $purchaseOrder): Response|RedirectResponse
    {
        if (!in_array($purchaseOrder->status, [
            PurchaseOrderStatus::APPROVED,
            PurchaseOrderStatus::PARTIALLY_RECEIVED,
        ], true)) {
            return redirect()
                ->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Only approved or partially received purchase orders can be received.');
        }

        $purchaseOrder->load(['supplier', 'items.product.category']);

        $warehouses = Warehouse::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'code', 'name']);
        
        return Inertia::render('StockReceipts/Create', [
            'purchaseOrder' => $purchaseOrder,
            'warehouses' => $warehouses,
        ]);
    }

    public function store(StoreStockReceiptRequest $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        try { 
            $stockReceipt = $this->stockReceiptService->receive(
                $purchaseOrder,
                $request->validated(),
                (int) Auth::id()
            );
            
            return redirect()
                ->route('stock-receipts.show', $stockReceipt)
                ->with('success', 'Stock received successfully.');
        } catch (ValidationException $exception) {
            return redirect()
                ->route('purchase-orders.receive.create', $purchaseOrder)
                ->with('error', $exception->validator->errors()->first());
        }
    }

    public function show(StockReceipt $stockReceipt): Response
    {
        $stockReceipt->load([
            'purchaseOrder.supplier',
            'warehouse',
            'receiver',
            'items.product.category'
        ]);

        return Inertia::render('StockReceipts/Show', [
            'stockReceipt' => $stockReceipt,
        ]);
    }
}
