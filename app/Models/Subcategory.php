<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/* =========================================================================
 * SUBCATEGORY MODEL
 * Secondary hierarchical taxonomy (e.g., Saree, Three Piece, Two Piece under
 * Women; Kantha, Bedsheet under Home Decor).
 * ========================================================================= */

class Subcategory extends Model
{
    /**
     * Mass assignable attributes.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'image',
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
     * Parent category association.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Products assigned to this subcategory.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}