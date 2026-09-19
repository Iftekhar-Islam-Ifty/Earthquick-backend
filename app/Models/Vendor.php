<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/* =========================================================================
 * VENDOR MODEL
 * Represents an independent brand or creator store selling on Earthquick.
 * Manages brand profile, active status, vendor code for SKU generation,
 * and associations to supplied products and order line items.
 * ========================================================================= */

class Vendor extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'vendor_code',
        'logo',
        'banner',
        'tagline',
        'description',
        'email',
        'phone',
        'address',
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
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /* =========================================================================
     * MUTATORS & ACCESSORS
     * ========================================================================= */

    /**
     * Always format vendor code as uppercase alphanumeric string.
     */
    public function setVendorCodeAttribute(string $value): void
    {
        $this->attributes['vendor_code'] = strtoupper(trim($value));
    }

    /* =========================================================================
     * RELATIONSHIPS
     * ========================================================================= */

    /**
     * Catalog products supplied by this vendor.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Line items ordered from this vendor.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
