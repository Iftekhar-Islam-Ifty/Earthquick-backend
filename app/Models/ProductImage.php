<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/* =========================================================================
 * PRODUCT IMAGE MODEL
 * Represents secondary gallery assets and atelier angle shots for products.
 * ========================================================================= */

class ProductImage extends Model
{
    /**
     * Mass assignable attributes.
     *
     * @var list<string>
     */
    protected $fillable = [
        'product_id',
        'image_url',
        'sort_order',
    ];

    /* =========================================================================
     * RELATIONSHIPS
     * ========================================================================= */

    /**
     * Associated parent product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
