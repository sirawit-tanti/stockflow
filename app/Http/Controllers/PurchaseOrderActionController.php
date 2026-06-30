<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Services\PurchaseOrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PurchaseOrderActionController extends Controller
{
    public function __construct(
        private readonly PurchaseOrderService $purchaseOrderService
    ) {
    }

    public function submit(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        try { 
            $this->purchaseOrderService->submitDraft($purchaseOrder);

            return redirect()
                ->route('purchase-orders.show', $purchaseOrder)
                ->with('success', 'Purchase order submitted for approval successfully.');
        } catch (ValidationException $exception) {
            return redirect()
                ->route('purchase-orders.show', $purchaseOrder)
                ->with('error', $exception->validator->errors()->first());
        }
    }

    public function approve(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        try {
            $this->purchaseOrderService->approve($purchaseOrder, (int) Auth::id());

            return redirect()
                ->route('purchase-orders.show', $purchaseOrder)
                ->with('success', 'Purchase order approved successfully.');
        } catch (ValidationException $exception) {
            return redirect()
                ->route('purchase-orders.show', $purchaseOrder)
                ->with('error', $exception->validator->errors()->first());
        }
    }

    public function reject(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        try {
            $this->purchaseOrderService->reject($purchaseOrder);

            return redirect()
                ->route('purchase-orders.show', $purchaseOrder)
                ->with('success', 'Purchase order rejected successfully.');
        } catch (ValidationException $exception) {
            return redirect()
                ->route('purchase-orders.show', $purchaseOrder)
                ->with('error', $exception->validator->errors()->first());
        }
    }

    public function cancel(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        try {
            $this->purchaseOrderService->cancel($purchaseOrder);

            return redirect()
                ->route('purchase-orders.show', $purchaseOrder)
                ->with('success', 'Purchase order cancelled successfully.');
        } catch (ValidationException $exception) {
            return redirect()
                ->route('purchase-orders.show', $purchaseOrder)
                ->with('error', $exception->validator->errors()->first());
        }
    }
}