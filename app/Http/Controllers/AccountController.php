<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/* =========================================================================
 * ACCOUNT CONTROLLER
 * Handles customer profile management, address settings, and order history.
 * Protected by authentication middleware.
 * ========================================================================= */
class AccountController extends Controller
{
    /**
     * Display customer account dashboard with profile details and order history.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        // Retrieve orders associated by user ID or customer contact credentials
        $orders = Order::with('items')
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id);
                if (!empty($user->phone)) {
                    $query->orWhere('customer_phone', $user->phone);
                }
                if (!empty($user->email)) {
                    $query->orWhere('customer_email', $user->email);
                }
            })
            ->latest()
            ->get();

        return view('account.dashboard', compact('user', 'orders'));
    }

    /**
     * Display individual order details, tracking stepper, and item breakdown.
     *
     * @param  string  $order_number
     * @return \Illuminate\View\View
     */
    public function showOrder($order_number)
    {
        $user = Auth::user();

        // Ensure order belongs to authenticated customer
        $order = Order::with('items')
            ->where('order_number', $order_number)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id);
                if (!empty($user->phone)) {
                    $query->orWhere('customer_phone', $user->phone);
                }
                if (!empty($user->email)) {
                    $query->orWhere('customer_email', $user->email);
                }
            })
            ->firstOrFail();

        return view('account.order-detail', compact('order', 'user'));
    }

    /**
     * Update customer profile details and default shipping address.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        // Validate personal and shipping address inputs
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone'   => ['required', 'string', 'regex:/^(?:\+?88)?01[3-9]\d{8}$/', 'unique:users,phone,' . $user->id],
            'city'    => 'nullable|string|max:100',
            'area'    => 'nullable|string|max:100',
            'address' => 'nullable|string|max:1000',
        ], [
            'name.required'  => 'Full name is required.',
            'email.required' => 'Email address is required.',
            'email.unique'   => 'This email address is already associated with another account.',
            'phone.required' => 'Mobile phone number is required.',
            'phone.regex'    => 'Please provide a valid 11-digit Bangladeshi phone number (e.g. 017XXXXXXXX).',
            'phone.unique'   => 'This phone number is already registered.',
        ]);

        // Persist sanitized customer credentials
        $user->update([
            'name'    => trim($request->name),
            'email'   => strtolower(trim($request->email)),
            'phone'   => trim($request->phone),
            'city'    => $request->city,
            'area'    => $request->area,
            'address' => $request->address,
        ]);

        return redirect()->route('account.dashboard')->with('success', 'Profile information updated successfully.');
    }
}
