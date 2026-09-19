<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
        'vendor_id',
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
            'price' => 'float',
            'old_price' => 'float',
            'in_stock' => 'boolean',
            'stock_quantity' => 'integer',
            'is_featured' => 'boolean',
            'is_new_arrival' => 'boolean',
        ];
    }

    /* =========================================================================
     * RELATIONSHIPS
     * ========================================================================= */

    /**
     * Primary vendor/brand supplying this product.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Primary category association.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Specific subcategory association.
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    /**
     * Associated product gallery images.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * Limit a public catalog query to legacy unassigned products and products
     * whose assigned vendor is currently published.
     */
    public function scopePubliclyAvailable(Builder $query): Builder
    {
        return $query->where(function (Builder $query) {
            $query->whereNull('vendor_id')
                ->orWhereHas('vendor', fn (Builder $vendorQuery) => $vendorQuery->where('is_active', true));
        });
    }

    /**
     * Determine whether this product may be added from the public storefront.
     */
    public function isPubliclyAvailable(): bool
    {
        return $this->vendor_id === null
            || $this->vendor()->where('is_active', true)->exists();
    }
}
