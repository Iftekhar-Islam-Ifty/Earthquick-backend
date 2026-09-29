<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TestOrderDeletionService
{
    public function isEligible(Order $order): bool
    {
        $note = Str::upper(trim((string) $order->order_notes));
        $adminNote = Str::upper(trim((string) $order->admin_notes));

        return Str::startsWith($note, ['PHASE 9 EMAIL TEST', 'EARTHQUICK QA TEST'])
            && Str::startsWith($adminNote, ['CONTROLLED EMAIL TEST', 'EARTHQUICK QA TEST'])
            && $order->status === 'cancelled'
            && $order->payment_method === 'cod'
            && $order->payment_status === 'not_due'
            && $order->paid_at === null
            && $order->paid_recorded_by === null
            && ! $order->payment_reference
            && ! $order->cod_collection_channel
            && ! $order->courier_name
            && ! $order->tracking_number
            && $order->cancellationRequests()->where('status', 'approved')->exists()
            && ! $order->statusEvents()->whereIn('to_status', ['processing', 'in_transit', 'delivered'])->exists()
            && ! $order->returnRequests()->exists()
            && ! $order->refunds()->exists();
    }

    public function delete(int $orderId, User $admin, string $confirmedNumber, string $reason): string
    {
        return DB::transaction(function () use ($orderId, $admin, $confirmedNumber, $reason) {
            $order = Order::query()->lockForUpdate()->findOrFail($orderId);
            if (! hash_equals($order->order_number, trim($confirmedNumber))) {
                throw ValidationException::withMessages([
                    'confirm_order_number' => 'Type the exact order number to confirm deletion.',
                ]);
            }
            if (! $this->isEligible($order)) {
                throw ValidationException::withMessages([
                    'delete' => 'Only a marked test order that was safely cancelled before payment or fulfilment can be permanently deleted.',
                ]);
            }

            DB::table('order_deletion_audits')->insert([
                'original_order_id' => $order->id,
                'order_number' => $order->order_number,
                'deleted_by_user_id' => $admin->id,
                'reason' => trim($reason),
                'deleted_at' => now(),
            ]);
            $number = $order->order_number;
            $order->delete();

            return $number;
        });
    }
}
