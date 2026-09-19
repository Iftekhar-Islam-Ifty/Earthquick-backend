<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/* =========================================================================
 * ORDER ITEM MODEL
 * Represents a specific product line item persisted in a completed order,
 * including historical price, variant size, and quantity.
 * ========================================================================= */

class OrderItem extends Model
{
    /**
     * Mass assignable attributes.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'vendor_id',
        'product_name',
        'product_image',
        'unit_price',
        'quantity',
        'total_price',
    ];

    /* =========================================================================
     * RELATIONSHIPS
     * ========================================================================= */

    /**
     * Vendor/brand fulfilling this order line item.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Parent order to which this line item belongs.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Associated catalog product model.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
