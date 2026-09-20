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
        'product_type',
        'sku',
        'name',
        'slug',
        'price',
        'old_price',
        'fabric',
        'short_desc',
        'description',
        'specifications',
        'warranty_info',
        'image',
        'alt_image',
        'badge',
        'badge_type',
        'rating',
        'reviews_count',
        'in_stock',
        'stock_quantity',
        'is_active',
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
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_new_arrival' => 'boolean',
            'specifications' => 'array',
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
     * Purchasable options with their own SKU, attributes, price and stock.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('id');
    }

    /**
     * Options currently selectable on the public storefront.
     */
    public function activeVariants(): HasMany
    {
        return $this->variants()->where('is_active', true);
    }

    /**
     * Limit a public catalog query to legacy unassigned products and products
     * whose assigned vendor is currently published.
     */
    public function scopePubliclyAvailable(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function (Builder $query) {
                $query->whereNull('vendor_id')
                    ->orWhereHas('vendor', fn (Builder $vendorQuery) => $vendorQuery->where('is_active', true));
            });
    }

    /**
     * Determine whether this product may be added from the public storefront.
     */
    public function isPubliclyAvailable(): bool
    {
        return $this->is_active
            && ($this->vendor_id === null
                || $this->vendor()->where('is_active', true)->exists());
    }
}
