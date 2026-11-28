<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewsletterCampaign extends Model
{
    protected $fillable = ['store_slug', 'subject', 'body', 'status', 'created_by_user_id', 'launched_at', 'completed_at'];

    protected function casts(): array
    {
        return ['launched_at' => 'datetime', 'completed_at' => 'datetime'];
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(NewsletterCampaignRecipient::class);
    }
}
