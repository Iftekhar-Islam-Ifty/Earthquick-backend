<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/* =========================================================================
 * CATEGORY MODEL
 * Primary hierarchical category division (e.g., Women, Men, Kids, Bags,
 * Ornaments, Home Decor).
 * ========================================================================= */

class Category extends Model
{
    /**
     * Mass assignable attributes.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'image',
        'description',
        'is_active',
        'sort_order',
    ];

    /**
     * Attribute typecasting configurations.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active'  => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /* =========================================================================
     * RELATIONSHIPS
     * ========================================================================= */

    /**
     * Subcategories belonging to this category, ordered by display precedence.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subcategories(): HasMany
    {
        return $this->hasMany(Subcategory::class)->orderBy('sort_order');
    }

    /**
     * Products belonging directly to this category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}