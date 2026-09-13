<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/* =========================================================================
 * PRODUCT MODEL
 * Core inventory entity representing artisanal garments, fabrics, bags,
 * and home decor items. Manages pricing, stock status, and categorization.
 * ========================================================================= */

class Product extends Model
{
    /**
     * Mass assignable attributes.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'subcategory_id',
        'sku',
        'name',
        'slug',
        'price',
        'old_price',
        'fabric',
        'short_desc',
        'description',
        'image',
        'alt_image',
        'badge',
        'badge_type',
        'rating',
        'reviews_count',
        'in_stock',
        'stock_quantity',
        'is_featured',
        'is_new_arrival',
    ];

    /**
     * Attribute typecasting configurations.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price'          => 'float',
            'old_price'      => 'float',
            'in_stock'       => 'boolean',
            'is_featured'    => 'boolean',
            'is_new_arrival' => 'boolean',
        ];
    }

    /* =========================================================================
     * RELATIONSHIPS
     * ========================================================================= */

    /**
     * Primary category association.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Specific subcategory association.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    /**
     * Associated product gallery images.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }
}