<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderReturnController extends Controller
{
    public function store(Request $request, string $order_number, int $itemId): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1',
            'reported_issue_type' => 'required|in:wrong_item,damaged_defective,other',
            'reason' => 'required|string|min:10|max:1000',
        ]);

        $order = DB::transaction(function () use ($order_number, $itemId, $data) {
            $order = Order::query()->where('order_number', $order_number)->lockForUpdate()->firstOrFail();
            $ownsOrder = $order->user_id !== null
                ? auth()->id() === $order->user_id
                : in_array($order_number, session('checkout_order_numbers', []), true);
            abort_unless($ownsOrder, 404);
            $item = $order->items()->whereKey($itemId)->lockForUpdate()->firstOrFail();

            $delivered = $order->statusEvents()->where('to_status', 'delivered')->first();
            if ($order->status !== 'delivered' || $item->is_returnable !== true
                || ! $item->return_window_days || ! $delivered
                || now()->greaterThan($delivered->created_at->copy()->addDays($item->return_window_days))) {
                throw ValidationException::withMessages([
                    'return' => 'This item is not eligible for an online return. Please contact Earthquick support.',
                ]);
            }

            $reserved = (int) $item->returnRequests()
                ->whereIn('status', ['pending', 'authorized', 'received'])
                ->sum('quantity');
            if ($data['quantity'] > $item->quantity - $reserved) {
                throw ValidationException::withMessages([
                    'quantity' => 'The requested quantity exceeds the remaining returnable quantity.',
                ]);
            }

            $order->returnRequests()->create([
                'order_item_id' => $item->id,
                'requested_by_user_id' => auth()->id(),
                'source' => $order->user_id === null ? 'guest' : 'customer',
                'quantity' => $data['quantity'],
                'reason' => trim($data['reason']),
                'reported_issue_type' => $data['reported_issue_type'],
                'status' => 'pending',
            ]);
            return $order;
        });

        app(\App\Services\CustomerCommunications::class)->order($order, 'return_requested');

        return redirect()->back()->with('success', 'Return request sent. Please wait for Earthquick authorization before sending the item.');
    }

    public function decide(Request $request, int $id, int $requestId): RedirectResponse
    {
        $data = $request->validate([
            'decision' => 'required|in:authorize,reject',
            'decision_note' => 'required|string|min:5|max:2000',
            'verified_issue_type' => 'required_if:decision,authorize|nullable|in:wrong_item,damaged_defective,other',
        ]);

        $order = DB::transaction(function () use ($id, $requestId, $data, $request) {
            $order = Order::query()->lockForUpdate()->findOrFail($id);
            $return = $order->returnRequests()->whereKey($requestId)->lockForUpdate()->firstOrFail();
            if ($return->status !== 'pending' || $order->status !== 'delivered') {
                throw ValidationException::withMessages(['return' => 'This return request can no longer be decided here.']);
            }
            $return->update([
                'status' => $data['decision'] === 'authorize' ? 'authorized' : 'rejected',
                'decided_by_user_id' => $request->user()->id,
                'decision_note' => trim($data['decision_note']),
                'decided_at' => now(),
                'verified_issue_type' => $data['decision'] === 'authorize' ? $data['verified_issue_type'] : null,
                'return_shipping_payer' => $data['decision'] === 'authorize'
                    ? (in_array($data['verified_issue_type'], ['wrong_item', 'damaged_defective'], true) ? 'earthquick' : 'customer')
                    : null,
            ]);
            return $order;
        });

        app(\App\Services\CustomerCommunications::class)->order($order,
            $data['decision'] === 'authorize' ? 'return_authorized' : 'return_rejected');

        return redirect()->route('admin.orders.show', $id)
            ->with('success', $data['decision'] === 'authorize' ? 'Return authorized; no refund or restock has occurred.' : 'Return request declined.');
    }

    public function receive(Request $request, int $id, int $requestId): RedirectResponse
    {
        $data = $request->validate([
            'confirm_received' => 'accepted',
            'receipt_note' => 'required|string|min:5|max:2000',
        ]);

        $order = DB::transaction(function () use ($id, $requestId, $data, $request) {
            $order = Order::query()->lockForUpdate()->findOrFail($id);
            $return = $order->returnRequests()->whereKey($requestId)->lockForUpdate()->firstOrFail();
            if ($return->status !== 'authorized') {
                throw ValidationException::withMessages(['return' => 'Only an authorized return can be recorded as received.']);
            }
            $return->update([
                'status' => 'received',
                'received_by_user_id' => $request->user()->id,
                'receipt_note' => trim($data['receipt_note']),
                'received_at' => now(),
            ]);
            return $order;
        });

        app(\App\Services\CustomerCommunications::class)->order($order, 'return_received');

        return redirect()->route('admin.orders.show', $id)
            ->with('success', 'Returned item receipt recorded. Inventory and refund remain unchanged pending inspection and reconciliation.');
    }

    public function inspect(Request $request, int $id, int $requestId): RedirectResponse
    {
        $data = $request->validate([
            'inspection_outcome' => 'required|in:resellable,not_resellable,rejected',
            'inspection_note' => 'required|string|min:5|max:2000',
        ]);

        $order = DB::transaction(function () use ($id, $requestId, $data, $request) {
            $order = Order::query()->lockForUpdate()->findOrFail($id);
            $return = $order->returnRequests()->whereKey($requestId)->lockForUpdate()->firstOrFail();
            if ($return->status !== 'received' || $return->inspection_outcome !== null) {
                throw ValidationException::withMessages(['return' => 'Only a received, uninspected return can be inspected.']);
            }
            $return->update([
                'inspection_outcome' => $data['inspection_outcome'],
                'inspection_note' => trim($data['inspection_note']),
                'inspected_by_user_id' => $request->user()->id,
                'inspected_at' => now(),
            ]);
            return $order;
        });

        app(\App\Services\CustomerCommunications::class)->order($order,
            $data['inspection_outcome'] === 'rejected' ? 'return_inspection_rejected' : 'return_inspected');

        return redirect()->route('admin.orders.show', $id)
            ->with('success', 'Inspection recorded. Refund and inventory remain separate actions.');
    }

    public function restock(Request $request, int $id, int $requestId): RedirectResponse
    {
        $request->validate(['confirm_restock' => 'accepted']);

        DB::transaction(function () use ($id, $requestId, $request) {
            $order = Order::query()->lockForUpdate()->findOrFail($id);
            $return = $order->returnRequests()->whereKey($requestId)->lockForUpdate()->firstOrFail();
            if ($return->status !== 'received' || $return->inspection_outcome !== 'resellable'
                || $return->restocked_at !== null) {
                throw ValidationException::withMessages(['return' => 'Only an inspected, resellable return can be restocked once.']);
            }
            $item = $order->items()->whereKey($return->order_item_id)->firstOrFail();
            $product = Product::query()->lockForUpdate()->find($item->product_id);
            if (! $product) {
                throw ValidationException::withMessages(['return' => 'The original product is unavailable; reconcile inventory manually.']);
            }

            if ($item->variant_id !== null) {
                $variant = ProductVariant::query()->where('product_id', $product->id)
                    ->lockForUpdate()->find($item->variant_id);
                if (! $variant || ($item->variant_sku && $variant->sku !== $item->variant_sku)) {
                    throw ValidationException::withMessages(['return' => 'The original variant is unavailable; reconcile inventory manually.']);
                }
                $variant->increment('stock_quantity', $return->quantity);
                $remaining = (int) $product->variants()->where('is_active', true)->sum('stock_quantity');
            } else {
                if ($item->variant_sku || $product->variants()->exists()) {
                    throw ValidationException::withMessages(['return' => 'The original inventory option is unclear; reconcile inventory manually.']);
                }
                $remaining = $product->stock_quantity + $return->quantity;
            }

            $product->update(['stock_quantity' => $remaining, 'in_stock' => $remaining > 0]);
            $return->update(['restocked_at' => now(), 'restocked_by_user_id' => $request->user()->id]);
        });

        return redirect()->route('admin.orders.show', $id)->with('success', 'Inspected item restocked once. Refund remains separate.');
    }
}
