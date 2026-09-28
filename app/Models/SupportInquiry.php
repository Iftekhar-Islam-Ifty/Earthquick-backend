<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportInquiry extends Model
{
    protected $fillable = [
        'user_id', 'name', 'phone', 'email', 'subject', 'message',
        'status', 'internal_note', 'handled_by_user_id', 'handled_at',
    ];

    protected function casts(): array
    {
        return ['handled_at' => 'datetime'];
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by_user_id');
    }
}
