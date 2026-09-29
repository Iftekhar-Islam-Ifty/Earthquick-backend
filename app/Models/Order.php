<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/* =========================================================================
 * ORDER MODEL
 * Represents customer purchases, order status lifecycles, shipping delivery
 * calculations, and associated order line-items.
 * ========================================================================= */

class Order extends Model
{
    use SoftDeletes;

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
        'payment_status',
        'payment_reference',
        'paid_at',
        'paid_recorded_by',
        'cod_collection_channel',
        'cod_collection_note',
        'coupon_code',
        'coupon_id',
        'discount_amount',
        'subtotal',
        'delivery_fee',
        'total',
        'status',
        'courier_name',
        'tracking_number',
        'admin_notes',
        'archived_at',
        'archived_by_user_id',
        'deleted_by_user_id',
        'deletion_reason',
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
            'paid_at'         => 'datetime',
            'archived_at'     => 'datetime',
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

    public function paymentRecorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_recorded_by');
    }

    public function statusEvents(): HasMany
    {
        return $this->hasMany(OrderStatusEvent::class)->orderBy('id');
    }

    public function cancellationRequests(): HasMany
    {
        return $this->hasMany(OrderCancellationRequest::class)->orderByDesc('id');
    }

    public function returnRequests(): HasMany
    {
        return $this->hasMany(OrderReturnRequest::class)->orderByDesc('id');
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(OrderRefund::class)->orderByDesc('id');
    }
}
