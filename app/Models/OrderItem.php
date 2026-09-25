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
        'variant_id',
        'vendor_id',
        'product_name',
        'variant_sku',
        'variant_label',
        'variant_attributes',
        'delivery_class',
        'is_returnable',
        'return_window_days',
        'return_policy_note',
        'product_image',
        'unit_price',
        'quantity',
        'total_price',
    ];

    protected function casts(): array
    {
        return [
            'variant_attributes' => 'array',
            'unit_price' => 'float',
            'total_price' => 'float',
            'quantity' => 'integer',
            'is_returnable' => 'boolean',
            'return_window_days' => 'integer',
        ];
    }

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

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
