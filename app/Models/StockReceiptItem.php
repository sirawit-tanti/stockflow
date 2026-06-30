<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockReceiptItem extends Model
{
    protected $fillable = [
        'stock_receipt_id',
        'purchase_order_item_id',
        'product_id',
        'quantity'
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
        ];
    }

    public function stockReceipt(): BelongsTo
    {
        return $this->belongsTo(StockReceipt::class);
    }

    public function purchaseOrderItem(): BelongsTo {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function product(): BelongsTo {
        return $this->belongsTo(Product::class);
    }
}