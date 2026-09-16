<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/* =========================================================================
 * ORDER MODEL
 * Represents customer purchases, order status lifecycles, shipping delivery
 * calculations, and associated order line-items.
 * ========================================================================= */

class Order extends Model
{
    /**
     * Mass assignable attributes.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'delivery_zone',
        'district',
        'area',
        'address',
        'order_notes',
        'payment_method',
        'coupon_code',
        'discount_amount',
        'subtotal',
        'delivery_fee',
        'total',
        'status',
        'courier_name',
        'tracking_number',
        'admin_notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subtotal'        => 'float',
            'discount_amount' => 'float',
            'delivery_fee'    => 'float',
            'total'           => 'float',
        ];
    }

    /* =========================================================================
     * RELATIONSHIPS
     * ========================================================================= */

    /**
     * Associated customer account (nullable for guest checkout).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Individual line items purchased within this order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
