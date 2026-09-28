<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderRefund extends Model
{
    protected $fillable = [
        'order_id', 'order_return_request_id', 'item_amount', 'discount_share',
        'delivery_amount', 'return_shipping_amount', 'return_shipping_receipt_reference',
        'total_amount', 'status', 'approved_by_user_id',
        'approved_at', 'approval_note', 'method', 'reference',
        'completed_by_user_id', 'completed_at', 'recipient_name', 'recipient_account_last4',
        'recipient_verified_via', 'recipient_verification_note', 'recipient_verified_by_user_id',
        'recipient_verified_at',
    ];

    protected function casts(): array
    {
        return [
            'item_amount' => 'decimal:2',
            'discount_share' => 'decimal:2',
            'delivery_amount' => 'decimal:2',
            'return_shipping_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'approved_at' => 'datetime',
            'completed_at' => 'datetime',
            'recipient_verified_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function returnRequest(): BelongsTo
    {
        return $this->belongsTo(OrderReturnRequest::class, 'order_return_request_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by_user_id');
    }

    public function recipientVerifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_verified_by_user_id');
    }
}
