<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/* =========================================================================
 * CART CONTROLLER
 * Manages customer shopping bag sessions, cart drawer AJAX synchronization,
 * line item quantities, and subtotal/free shipping calculations.
 * ========================================================================= */

class CartController extends Controller
{
    public const FREE_SHIPPING_THRESHOLD = 3000;

    /* =========================================================================
     * CART DISPLAY & DATA SYNCHRONIZATION
     * Returns full cart page view or serialized JSON for reactive drawer.
     * ========================================================================= */

    /**
     * Display the shopping cart page or return serialized JSON for AJAX drawer.
     */
    public function index(Request $request): View|JsonResponse
    {
        // Retrieve current cart array from server session
        $cart = session()->get('cart', []);
        $subtotal = 0;
        $count = 0;

        // Compute aggregated subtotal and total line-item count
        foreach ($cart as $item) {
            $subtotal += ((float) $item['price']) * ((int) $item['quantity']);
            $count += (int) $item['quantity'];
        }

        $couponInfo = $this->calculateCouponDiscount($subtotal);
        $coupon = $couponInfo['coupon'];
        $discount = $couponInfo['discount'];
        $total = $couponInfo['total'];

        // Return JSON payload if requested via AJAX
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'cart' => $cart,
                'items' => array_values($cart),
                'count' => $count,
                'subtotal' => $subtotal,
                'formatted_subtotal' => '৳'.number_format($subtotal),
                'coupon' => $coupon,
                'discount' => $discount,
                'formatted_discount' => $discount > 0 ? '-৳'.number_format($discount) : '৳0',
                'total' => $total,
                'formatted_total' => '৳'.number_format($total),
                'free_shipping_threshold' => self::FREE_SHIPPING_THRESHOLD,
                'free_shipping_unlocked' => ($subtotal >= self::FREE_SHIPPING_THRESHOLD),
                'free_shipping_remaining' => max(0, self::FREE_SHIPPING_THRESHOLD - $subtotal),
            ]);
        }

        return view('cart', compact('cart', 'subtotal', 'count', 'coupon', 'discount', 'total'));
    }

    /* =========================================================================
     * ADD ITEM TO CART
     * Appends product variant or increments quantity in the session cart.
     * ========================================================================= */

    /**
     * Add a product variant to the session shopping cart.
     */
    public function add(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
            'size' => 'nullable|string|max:100',
            'variant_id' => 'nullable|integer|exists:product_variants,id',
        ]);

        $product = Product::with(['vendor', 'variants'])->findOrFail($request->product_id);
        if (! $product->isPubliclyAvailable()) {
            $message = 'This product is no longer available.';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return redirect()->back()->with('error', $message);
        }
        $quantity = max(1, (int) $request->input('quantity', 1));
        $variantId = $request->integer('variant_id') ?: null;
        $variant = $variantId
            ? $product->variants->firstWhere('id', $variantId)
            : null;
        $hasActiveVariants = $product->variants->contains('is_active', true);

        if (($variantId && (! $variant || ! $variant->is_active)) || ($hasActiveVariants && ! $variant)) {
            $message = 'Please choose an available product option before adding this item.';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return redirect()->back()->withErrors(['variant_id' => $message]);
        }

        $size = $variant?->label ?? $request->input('size', 'Standard');
        $unitPrice = $variant?->effectivePrice() ?? (float) $product->price;
        $availableStock = $variant?->stock_quantity ?? $product->stock_quantity;

        $cart = session()->get('cart', []);
        // A database variant ID is stable; legacy products continue using their size text.
        $cartKey = $variant
            ? $product->id.'_variant_'.$variant->id
            : $product->id.'_'.Str::slug($size);
        $requestedQuantity = $quantity + (int) ($cart[$cartKey]['quantity'] ?? 0);

        if (! $product->in_stock || $availableStock < $requestedQuantity) {
            $message = 'The requested quantity is no longer available.';

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return redirect()->back()->with('error', $message);
        }

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            $cart[$cartKey] = [
                'key' => $cartKey,
                'id' => $product->id,
                'product_id' => $product->id,
                'vendor_id' => $product->vendor_id,
                'vendor_name' => $product->vendor ? $product->vendor->name : 'Earthquick',
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $unitPrice,
                'image' => $product->image,
                'size' => $size,
                'variant_id' => $variant?->id,
                'variant_sku' => $variant?->sku,
                'variant_label' => $variant?->label,
                'variant_attributes' => $variant?->attributes,
                'quantity' => $quantity,
            ];
        }

        // Commit updated cart payload into session storage
        session()->put('cart', $cart);

        // Recalculate cart totals
        $subtotal = 0;
        $count = 0;
        foreach ($cart as $item) {
            $subtotal += ((float) $item['price']) * ((int) $item['quantity']);
            $count += (int) $item['quantity'];
        }

        $couponInfo = $this->calculateCouponDiscount($subtotal);
        $coupon = $couponInfo['coupon'];
        $discount = $couponInfo['discount'];
        $total = $couponInfo['total'];

        // Instant checkout redirect when triggered via 'Buy Now' action
        if ($request->input('buy_now')) {
            return redirect()->route('checkout.index');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Item added to shopping bag successfully.',
                'cart' => $cart,
                'items' => array_values($cart),
                'count' => $count,
                'subtotal' => $subtotal,
                'formatted_subtotal' => '৳'.number_format($subtotal),
                'coupon' => $coupon,
                'discount' => $discount,
                'formatted_discount' => $discount > 0 ? '-৳'.number_format($discount) : '৳0',
                'total' => $total,
                'formatted_total' => '৳'.number_format($total),
                'free_shipping_threshold' => self::FREE_SHIPPING_THRESHOLD,
                'free_shipping_unlocked' => ($subtotal >= self::FREE_SHIPPING_THRESHOLD),
                'free_shipping_remaining' => max(0, self::FREE_SHIPPING_THRESHOLD - $subtotal),
            ]);
        }

        return redirect()->back()->with('success', 'Item added to your shopping bag successfully.');
    }

    /* =========================================================================
     * UPDATE CART QUANTITY
     * Modifies quantity or removes line item if reduced to zero.
     * ========================================================================= */

    /**
     * Update item quantity in the cart session.
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'key' => 'required|string',
            'quantity' => 'nullable|integer',
            'delta' => 'nullable|integer',
        ]);

        $cart = session()->get('cart', []);
        $key = $request->input('key');

        if (isset($cart[$key])) {
            if ($request->has('quantity')) {
                $cart[$key]['quantity'] = (int) $request->input('quantity');
            } elseif ($request->has('delta')) {
                $cart[$key]['quantity'] += (int) $request->input('delta');
            }

            // Remove line item automatically if quantity falls below 1
            if ($cart[$key]['quantity'] <= 0) {
                unset($cart[$key]);
            } else {
                $productId = $cart[$key]['product_id'] ?? $cart[$key]['id'] ?? null;
                $product = Product::with(['vendor', 'variants'])->find($productId);
                $variantId = $cart[$key]['variant_id'] ?? null;
                $variant = $variantId
                    ? ProductVariant::query()
                        ->whereKey($variantId)
                        ->where('product_id', $productId)
                        ->where('is_active', true)
                        ->first()
                    : null;
                $hasActiveVariants = $product?->variants->contains('is_active', true) ?? false;
                $availableStock = $variant?->stock_quantity ?? $product?->stock_quantity ?? 0;

                if (! $product || ! $product->isPubliclyAvailable() || ! $product->in_stock
                    || ($variantId && ! $variant) || ($hasActiveVariants && ! $variant)
                    || $availableStock < $cart[$key]['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'The requested quantity is no longer available.',
                    ], 422);
                }

                $cart[$key]['price'] = $variant?->effectivePrice() ?? (float) $product->price;
                if ($variant) {
                    $cart[$key]['size'] = $variant->label;
                    $cart[$key]['variant_sku'] = $variant->sku;
                    $cart[$key]['variant_label'] = $variant->label;
                    $cart[$key]['variant_attributes'] = $variant->attributes;
                }
            }

            session()->put('cart', $cart);
        }

        $subtotal = 0;
        $count = 0;
        foreach ($cart as $item) {
            $subtotal += ((float) $item['price']) * ((int) $item['quantity']);
            $count += (int) $item['quantity'];
        }

        $couponInfo = $this->calculateCouponDiscount($subtotal);
        $coupon = $couponInfo['coupon'];
        $discount = $couponInfo['discount'];
        $total = $couponInfo['total'];

        return response()->json([
            'success' => true,
            'message' => 'Shopping bag updated successfully.',
            'cart' => $cart,
            'items' => array_values($cart),
            'count' => $count,
            'subtotal' => $subtotal,
            'formatted_subtotal' => '৳'.number_format($subtotal),
            'coupon' => $coupon,
            'discount' => $discount,
            'formatted_discount' => $discount > 0 ? '-৳'.number_format($discount) : '৳0',
            'total' => $total,
            'formatted_total' => '৳'.number_format($total),
            'free_shipping_threshold' => self::FREE_SHIPPING_THRESHOLD,
            'free_shipping_unlocked' => ($subtotal >= self::FREE_SHIPPING_THRESHOLD),
            'free_shipping_remaining' => max(0, self::FREE_SHIPPING_THRESHOLD - $subtotal),
        ]);
    }

    /* =========================================================================
     * REMOVE ITEM FROM CART
     * Removes an individual line item identified by variant key.
     * ========================================================================= */

    /**
     * Remove an item from the session cart.
     */
    public function remove(Request $request): JsonResponse
    {
        $request->validate([
            'key' => 'required|string',
        ]);

        $cart = session()->get('cart', []);
        $key = $request->input('key');
        $removedName = '';

        if (isset($cart[$key])) {
            $removedName = $cart[$key]['name'];
            unset($cart[$key]);
            session()->put('cart', $cart);
        }

        $subtotal = 0;
        $count = 0;
        foreach ($cart as $item) {
            $subtotal += ((float) $item['price']) * ((int) $item['quantity']);
            $count += (int) $item['quantity'];
        }

        $couponInfo = $this->calculateCouponDiscount($subtotal);
        $coupon = $couponInfo['coupon'];
        $discount = $couponInfo['discount'];
        $total = $couponInfo['total'];

        return response()->json([
            'success' => true,
            'message' => $removedName ? "{$removedName} removed from bag." : 'Item removed from bag.',
            'cart' => $cart,
            'items' => array_values($cart),
            'count' => $count,
            'subtotal' => $subtotal,
            'formatted_subtotal' => '৳'.number_format($subtotal),
            'coupon' => $coupon,
            'discount' => $discount,
            'formatted_discount' => $discount > 0 ? '-৳'.number_format($discount) : '৳0',
            'total' => $total,
            'formatted_total' => '৳'.number_format($total),
            'free_shipping_threshold' => self::FREE_SHIPPING_THRESHOLD,
            'free_shipping_unlocked' => ($subtotal >= self::FREE_SHIPPING_THRESHOLD),
            'free_shipping_remaining' => max(0, self::FREE_SHIPPING_THRESHOLD - $subtotal),
        ]);
    }

    /* =========================================================================
     * CLEAR ENTIRE CART
     * Flushes the active cart session.
     * ========================================================================= */

    /**
     * Clear all items from the shopping cart session.
     */
    public function clear(Request $request): JsonResponse
    {
        // Flush cart key and applied coupon from session storage
        session()->forget('cart');
        session()->forget('coupon');

        return response()->json([
            'success' => true,
            'message' => 'Shopping bag cleared successfully.',
            'cart' => [],
            'items' => [],
            'count' => 0,
            'subtotal' => 0,
            'formatted_subtotal' => '৳0',
            'coupon' => null,
            'discount' => 0.00,
            'formatted_discount' => '৳0',
            'total' => 0,
            'formatted_total' => '৳0',
            'free_shipping_threshold' => self::FREE_SHIPPING_THRESHOLD,
            'free_shipping_unlocked' => false,
            'free_shipping_remaining' => self::FREE_SHIPPING_THRESHOLD,
        ]);
    }

    /* =========================================================================
     * PROMOTIONAL COUPON & CAMPAIGN ENGINE
     * Validates promo codes, calculates discounts, and synchronizes cart drawer.
     * ========================================================================= */

    /**
     * Apply promotional coupon code to active shopping cart session.
     */
    public function applyCoupon(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|max:50',
        ]);

        $code = strtoupper(trim($request->input('code')));
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            $msg = 'Your shopping bag is empty. Add products before applying promo codes.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            return redirect()->back()->with('error', $msg);
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += ((float) $item['price']) * ((int) $item['quantity']);
        }

        $coupon = Coupon::where('code', $code)->first();

        if (! $coupon) {
            $msg = "Invalid promo code '{$code}'. Please check and try again.";
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 404);
            }

            return redirect()->back()->with('error', $msg);
        }

        $check = $coupon->isValid($subtotal);
        if (! $check['valid']) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $check['message']], 422);
            }

            return redirect()->back()->with('error', $check['message']);
        }

        $discount = $coupon->calculateDiscount($subtotal);
        $couponData = [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'name' => $coupon->name,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'discount' => $discount,
        ];

        session()->put('coupon', $couponData);
        $total = max(0, $subtotal - $discount);

        $successMsg = "Promo code '{$coupon->code}' applied successfully!";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
                'coupon' => $couponData,
                'subtotal' => $subtotal,
                'formatted_subtotal' => '৳'.number_format($subtotal),
                'discount' => $discount,
                'formatted_discount' => '-৳'.number_format($discount),
                'total' => $total,
                'formatted_total' => '৳'.number_format($total),
            ]);
        }

        return redirect()->back()->with('success', $successMsg);
    }

    /**
     * Remove applied coupon discount from the session cart.
     */
    public function removeCoupon(Request $request): JsonResponse|RedirectResponse
    {
        session()->forget('coupon');

        $cart = session()->get('cart', []);
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += ((float) $item['price']) * ((int) $item['quantity']);
        }

        $msg = 'Promo code removed successfully.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'coupon' => null,
                'subtotal' => $subtotal,
                'formatted_subtotal' => '৳'.number_format($subtotal),
                'discount' => 0.00,
                'formatted_discount' => '৳0',
                'total' => $subtotal,
                'formatted_total' => '৳'.number_format($subtotal),
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Compute and synchronize applied coupon discount for current subtotal.
     *
     * @return array{coupon: ?array, discount: float, total: float}
     */
    protected function calculateCouponDiscount(float $subtotal): array
    {
        $sessionCoupon = session()->get('coupon');
        if (! $sessionCoupon || empty($sessionCoupon['id'])) {
            return [
                'coupon' => null,
                'discount' => 0.00,
                'total' => $subtotal,
            ];
        }

        $coupon = Coupon::find($sessionCoupon['id']);
        if (! $coupon || ! $coupon->isValid($subtotal)['valid']) {
            session()->forget('coupon');

            return [
                'coupon' => null,
                'discount' => 0.00,
                'total' => $subtotal,
            ];
        }

        $discount = $coupon->calculateDiscount($subtotal);
        $couponData = [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'name' => $coupon->name,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'discount' => $discount,
        ];

        session()->put('coupon', $couponData);

        return [
            'coupon' => $couponData,
            'discount' => $discount,
            'total' => max(0, $subtotal - $discount),
        ];
    }
}
