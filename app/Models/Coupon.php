<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/* =========================================================================
 * COUPON MODEL
 * Manages promotional campaign codes, discount rules (percentage & flat),
 * minimum spend thresholds, usage limits, and redemption validity.
 * ========================================================================= */

class Coupon extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'coupons';

    /**
     * Mass assignable attributes.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'name',
        'type',
        'value',
        'min_order_amount',
        'max_discount',
        'usage_limit',
        'used_count',
        'expires_at',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value'            => 'float',
            'min_order_amount' => 'float',
            'max_discount'     => 'float',
            'usage_limit'      => 'integer',
            'used_count'       => 'integer',
            'expires_at'       => 'datetime',
            'is_active'        => 'boolean',
        ];
    }

    /* =========================================================================
     * MUTATORS & ACCESSORS
     * ========================================================================= */

    /**
     * Always persist uppercase, clean coupon codes.
     *
     * @param  string  $value
     * @return void
     */
    public function setCodeAttribute(string $value): void
    {
        $this->attributes['code'] = strtoupper(trim($value));
    }

    /* =========================================================================
     * VALIDATION & DISCOUNT CALCULATION LOGIC
     * ========================================================================= */

    /**
     * Validate whether this coupon can be redeemed against the given cart subtotal.
     *
     * @param  float  $subtotal
     * @return array{valid: bool, message: string}
     */
    public function isValid(float $subtotal): array
    {
        // 1. Check if coupon is active
        if (!$this->is_active) {
            return [
                'valid'   => false,
                'message' => 'This promo code is no longer active.',
            ];
        }

        // 2. Check expiry date
        if ($this->expires_at && $this->expires_at->isPast()) {
            return [
                'valid'   => false,
                'message' => 'This promo code has expired.',
            ];
        }

        // 3. Check redemption limit
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return [
                'valid'   => false,
                'message' => 'This promo code redemption limit has been reached.',
            ];
        }

        // 4. Check minimum cart subtotal requirement
        if ($this->min_order_amount !== null && $subtotal < $this->min_order_amount) {
            return [
                'valid'   => false,
                'message' => 'Minimum spend of ৳' . number_format($this->min_order_amount) . ' required to apply this coupon.',
            ];
        }

        return [
            'valid'   => true,
            'message' => 'Promo code applied successfully!',
        ];
    }

    /**
     * Calculate the exact discount amount in BDT (৳) for the given cart subtotal.
     *
     * @param  float  $subtotal
     * @return float
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal <= 0) {
            return 0.00;
        }

        $discount = 0.00;

        if ($this->type === 'percent') {
            $discount = ($subtotal * $this->value) / 100;

            // Apply maximum discount cap if defined
            if ($this->max_discount !== null && $this->max_discount > 0 && $discount > $this->max_discount) {
                $discount = (float) $this->max_discount;
            }
        } elseif ($this->type === 'fixed') {
            $discount = (float) $this->value;
        }

        // Ensure discount never exceeds total subtotal
        $discount = min($discount, $subtotal);

        return round(max(0, $discount), 2);
    }
}

