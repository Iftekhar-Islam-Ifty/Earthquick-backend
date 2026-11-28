<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class NewsletterSubscription extends Model
{
    protected $fillable = [
        'store_slug', 'email', 'token_hash', 'confirmation_expires_at',
        'confirmed_at', 'unsubscribed_at',
    ];

    protected function casts(): array
    {
        return [
            'confirmation_expires_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }

    public function unsubscribeUrl(): string
    {
        return URL::signedRoute('newsletter.unsubscribe', [
            'subscription' => $this->id,
            'version' => $this->confirmed_at->timestamp,
        ]);
    }
}
