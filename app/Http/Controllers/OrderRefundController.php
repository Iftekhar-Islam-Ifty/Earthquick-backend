<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderRefundService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderRefundController extends Controller
{
    public function approve(Request $request, int $id, int $requestId, OrderRefundService $refunds): RedirectResponse
    {
        $data = $request->validate([
            'include_delivery' => 'nullable|boolean',
            'return_shipping_amount' => 'nullable|numeric|min:0|decimal:0,2',
            'return_shipping_receipt_reference' => 'nullable|string|max:100|unique:order_refunds,return_shipping_receipt_reference',
            'approval_note' => 'required|string|min:5|max:2000',
        ]);

        $refund = $refunds->approve($id, $requestId, $request->user(), $request->boolean('include_delivery'),
            (string) ($data['return_shipping_amount'] ?? '0'),
            isset($data['return_shipping_receipt_reference']) ? trim($data['return_shipping_receipt_reference']) : null,
            trim($data['approval_note']));

        app(\App\Services\CustomerCommunications::class)->order($refund->order,
            $refund->status === 'no_refund_due' ? 'refund_no_due' : 'refund_approved');

        return redirect()->route('admin.orders.show', $id)
            ->with('success', 'Refund amount approved. No money has been sent yet.');
    }

    public function complete(Request $request, int $id, int $refundId): RedirectResponse
    {
        $data = $request->validate([
            'reference' => 'required|string|min:4|max:100|unique:order_refunds,reference',
            'confirm_sent' => 'accepted',
        ]);

        DB::transaction(function () use ($id, $refundId, $data, $request) {
            $order = Order::query()->lockForUpdate()->findOrFail($id);
            $refund = $order->refunds()->whereKey($refundId)->lockForUpdate()->firstOrFail();
            if ($refund->status !== 'approved' || $refund->completed_at !== null
                || $refund->recipient_verified_at === null || $refund->method === null) {
                throw ValidationException::withMessages(['refund' => 'Verify the recipient first; only an approved, unpaid refund can be marked completed once.']);
            }
            $refund->update([
                'status' => 'completed',
                'reference' => trim($data['reference']),
                'completed_by_user_id' => $request->user()->id,
                'completed_at' => now(),
            ]);
        });

        app(\App\Services\CustomerCommunications::class)->order(Order::findOrFail($id), 'refund_completed');

        return redirect()->route('admin.orders.show', $id)
            ->with('success', 'External refund recorded as completed.');
    }

    public function verifyRecipient(Request $request, int $id, int $refundId): RedirectResponse
    {
        $data = $request->validate([
            'method' => 'required|in:bank_transfer,mobile_transfer,cash',
            'recipient_name' => 'required|string|min:2|max:150',
            'recipient_account_last4' => 'required_unless:method,cash|nullable|digits:4',
            'recipient_verified_via' => 'required|in:order_contact,in_person',
            'recipient_verification_note' => 'required|string|min:10|max:1000',
            'confirm_recipient_verified' => 'accepted',
        ]);

        DB::transaction(function () use ($id, $refundId, $data, $request) {
            $order = Order::query()->lockForUpdate()->findOrFail($id);
            $refund = $order->refunds()->whereKey($refundId)->lockForUpdate()->firstOrFail();
            if ($refund->status !== 'approved' || $refund->completed_at !== null
                || $refund->recipient_verified_at !== null) {
                throw ValidationException::withMessages(['refund' => 'Only an approved refund with no previous recipient verification can be verified.']);
            }
            $refund->update([
                'method' => $data['method'],
                'recipient_name' => trim($data['recipient_name']),
                'recipient_account_last4' => $data['method'] === 'cash' ? null : $data['recipient_account_last4'],
                'recipient_verified_via' => $data['recipient_verified_via'],
                'recipient_verification_note' => trim($data['recipient_verification_note']),
                'recipient_verified_by_user_id' => $request->user()->id,
                'recipient_verified_at' => now(),
            ]);
        });

        return redirect()->route('admin.orders.show', $id)
            ->with('success', 'Recipient verified. No money has been sent or marked paid by this action.');
    }
}
