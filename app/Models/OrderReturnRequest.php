<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderReturnRequest extends Model
{
    protected $fillable = [
        'order_item_id', 'requested_by_user_id', 'source', 'quantity', 'reason', 'reported_issue_type', 'status',
        'verified_issue_type', 'return_shipping_payer',
        'decided_by_user_id', 'decision_note', 'decided_at',
        'received_by_user_id', 'receipt_note', 'received_at',
        'inspection_outcome', 'inspection_note', 'inspected_by_user_id', 'inspected_at',
        'restocked_by_user_id', 'restocked_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'decided_at' => 'datetime',
            'received_at' => 'datetime',
            'inspected_at' => 'datetime',
            'restocked_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function decisionMaker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by_user_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by_user_id');
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspected_by_user_id');
    }

    public function restocker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'restocked_by_user_id');
    }

    public function refund(): HasOne
    {
        return $this->hasOne(OrderRefund::class);
    }
}
