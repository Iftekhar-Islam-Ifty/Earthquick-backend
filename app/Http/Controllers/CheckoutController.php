<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

/* =========================================================================
 * CHECKOUT CONTROLLER
 * Orchestrates checkout form initialization, delivery fee calculation,
 * customer input validation, and atomic database transaction order placement.
 * ========================================================================= */

class CheckoutController extends Controller
{
    /* =========================================================================
     * CHECKOUT FORM DISPLAY
     * Prepares line items, subtotal, and delivery tiers for customer view.
     * ========================================================================= */

    /**
     * Display the checkout form with line items and delivery charges.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $cart = session()->get('cart', []);
        $subtotal = 0;
        $count = 0;

        foreach ($cart as $item) {
            $subtotal += ((float) $item['price']) * ((int) $item['quantity']);
            $count += (int) $item['quantity'];
        }

        // Validate and compute coupon discount if active in session
        $coupon = null;
        $discount = 0.00;
        if (session()->has('coupon')) {
            $sessionCoupon = session('coupon');
            $couponModel = Coupon::find($sessionCoupon['id'] ?? null);
            if ($couponModel && $couponModel->isValid($subtotal)['valid']) {
                $discount = $couponModel->calculateDiscount($subtotal);
                $coupon = [
                    'id'       => $couponModel->id,
                    'code'     => $couponModel->code,
                    'name'     => $couponModel->name,
                    'type'     => $couponModel->type,
                    'value'    => $couponModel->value,
                    'discount' => $discount,
                ];
                session()->put('coupon', $coupon);
            } else {
                session()->forget('coupon');
            }
        }

        // Chattogram delivery rates: BDT 80 inside city, BDT 150 outside city
        $deliveryFeeInside = 80;
        $deliveryFeeOutside = 150;

        return view('checkout', compact(
            'cart',
            'subtotal',
            'count',
            'deliveryFeeInside',
            'deliveryFeeOutside',
            'coupon',
            'discount'
        ));
    }

    /* =========================================================================
     * ORDER SUBMISSION & PROCESSING
     * Validates input, calculates final totals, and persists order transaction.
     * ========================================================================= */

    /**
     * Store a newly created customer order in MySQL via database transaction.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_phone' => ['required', 'string', 'regex:/^(?:\+?88)?01[3-9]\d{8}$/'],
            'customer_email' => 'nullable|email|max:255',
            'delivery_zone'  => 'required|in:inside_ctg,outside_ctg',
            'district'       => 'required|string|max:100',
            'area'           => 'required|string|max:100',
            'address'        => 'required|string|max:1000',
            'order_notes'    => 'nullable|string|max:1000',
            'payment_method' => 'nullable|string|in:cod,bkash,card',
        ], [
            'customer_name.required'  => 'Customer name is required.',
            'customer_phone.required' => 'Mobile phone number is required.',
            'customer_phone.regex'    => 'Please provide a valid 11-digit Bangladeshi mobile number (e.g., 017XXXXXXXX or 018XXXXXXXX).',
            'delivery_zone.required'  => 'Please select a delivery zone (Inside Chattogram or Outside Chattogram).',
            'district.required'       => 'District is required.',
            'area.required'           => 'Thana or area is required.',
            'address.required'        => 'Full street address is required.',
        ]);

        $cart = session()->get('cart', []);

        // Guard against empty checkout submissions
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your shopping bag is empty. Please add items before checking out.');
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += ((float) $item['price']) * ((int) $item['quantity']);
        }

        // Apply geographic delivery tariff
        $deliveryFee = ($request->delivery_zone === 'inside_ctg') ? 80.00 : 150.00;
        $paymentMethod = $request->input('payment_method', 'cod');

        // Human-readable invoice number: EQ-2026-XXXX-XXXX
        $orderNumber = 'EQ-' . date('Y') . '-' . strtoupper(Str::random(4)) . '-' . rand(1000, 9999);

        // Execute atomic order persistence transaction
        $order = DB::transaction(function () use ($request, $orderNumber, $subtotal, $deliveryFee, $paymentMethod, $cart) {
            // Re-validate and apply coupon discount safely inside transaction
            $couponCode = null;
            $discountAmount = 0.00;

            if (session()->has('coupon')) {
                $sessionCoupon = session('coupon');
                $coupon = Coupon::find($sessionCoupon['id'] ?? null);
                if ($coupon && $coupon->isValid($subtotal)['valid']) {
                    $discountAmount = $coupon->calculateDiscount($subtotal);
                    $couponCode = $coupon->code;
                    $coupon->increment('used_count');
                }
            }

            $total = max(0, $subtotal - $discountAmount) + $deliveryFee;

            $newOrder = Order::create([
                'order_number'    => $orderNumber,
                'user_id'         => auth()->id(),
                'customer_name'   => $request->customer_name,
                'customer_phone'  => $request->customer_phone,
                'customer_email'  => $request->customer_email,
                'delivery_zone'   => $request->delivery_zone,
                'district'        => $request->district,
                'area'            => $request->area,
                'address'         => $request->address,
                'order_notes'     => $request->order_notes,
                'payment_method'  => $paymentMethod,
                'coupon_code'     => $couponCode,
                'discount_amount' => $discountAmount,
                'subtotal'        => $subtotal,
                'delivery_fee'    => $deliveryFee,
                'total'           => $total,
                'status'          => 'pending',
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id'      => $newOrder->id,
                    'product_id'    => $item['product_id'] ?? $item['id'],
                    'product_name'  => $item['name'] . (isset($item['size']) && $item['size'] !== 'Standard' ? " ({$item['size']})" : ''),
                    'product_image' => $item['image'] ?? null,
                    'unit_price'    => $item['price'],
                    'quantity'      => $item['quantity'],
                    'total_price'   => $item['price'] * $item['quantity'],
                ]);
            }

            return $newOrder;
        });

        // Clear active shopping cart and applied coupon upon successful placement
        session()->forget('cart');
        session()->forget('coupon');

        return redirect()->route('checkout.success', ['order_number' => $order->order_number]);
    }

    /* =========================================================================
     * ORDER CONFIRMATION & RECEIPT
     * Displays successful placement message and detailed invoice summary.
     * ========================================================================= */

    /**
     * Display the order confirmation and receipt screen.
     *
     * @param  string  $order_number
     * @return \Illuminate\View\View
     */
    public function success(string $order_number): View
    {
        $order = Order::with('items')->where('order_number', $order_number)->firstOrFail();

        return view('order-success', compact('order'));
    }
}
