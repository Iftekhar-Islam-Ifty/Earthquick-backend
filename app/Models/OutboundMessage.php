<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutboundMessage extends Model
{
    protected $fillable = [
        'recipient_hint', 'recipient', 'subject', 'body', 'context', 'status',
        'attempts', 'next_attempt_at', 'last_attempt_at', 'sent_at', 'last_error_class',
    ];

    protected function casts(): array
    {
        return [
            'recipient' => 'encrypted',
            'subject' => 'encrypted',
            'body' => 'encrypted',
            'context' => 'array',
            'next_attempt_at' => 'datetime',
            'last_attempt_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }
}
