<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderCancellationController extends Controller
{
    public function store(Request $request, string $order_number): RedirectResponse
    {
        $data = $request->validate(['reason' => 'required|string|min:10|max:1000']);

        $order = DB::transaction(function () use ($order_number, $data) {
            $order = Order::query()->where('order_number', $order_number)->lockForUpdate()->firstOrFail();
            $ownsOrder = $order->user_id !== null
                ? auth()->id() === $order->user_id
                : in_array($order_number, session('checkout_order_numbers', []), true);
            abort_unless($ownsOrder, 404);

            if (! in_array($order->status, ['pending', 'confirmed'], true)
                || $order->payment_method !== 'cod'
                || $order->payment_status !== 'due_on_delivery') {
                throw ValidationException::withMessages([
                    'cancellation' => 'This order cannot be cancelled online. Please contact Rthquick support.',
                ]);
            }
            if ($order->cancellationRequests()->where('status', 'pending')->exists()) {
                throw ValidationException::withMessages(['cancellation' => 'A cancellation request is already under review.']);
            }

            $order->cancellationRequests()->create([
                'requested_by_user_id' => auth()->id(),
                'source' => $order->user_id === null ? 'guest' : 'customer',
                'reason' => trim($data['reason']),
                'status' => 'pending',
            ]);
            return $order;
        });

        app(\App\Services\CustomerCommunications::class)->order($order, 'cancellation_requested');

        return redirect()->back()->with('success', 'Cancellation request sent. Your order is not cancelled until Rthquick approves it.');
    }
}
