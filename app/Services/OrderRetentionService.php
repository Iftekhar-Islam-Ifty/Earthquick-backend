<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderRetentionService
{
    public const TRASH_DAYS = 30;

    public function canArchive(Order $order): bool
    {
        return ! $order->trashed()
            && $order->archived_at === null
            && in_array($order->status, ['delivered', 'cancelled'], true);
    }

    public function canTrash(Order $order): bool
    {
        return ! $order->trashed()
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

    public function archive(int $id, User $admin): void
    {
        DB::transaction(function () use ($id, $admin) {
            $order = Order::query()->lockForUpdate()->findOrFail($id);
            if (! $this->canArchive($order)) {
                throw ValidationException::withMessages(['archive' => 'Only completed or cancelled active orders can be archived.']);
            }
            $order->update(['archived_at' => now(), 'archived_by_user_id' => $admin->id]);
        });
    }

    public function unarchive(int $id): void
    {
        DB::transaction(function () use ($id) {
            $order = Order::query()->lockForUpdate()->findOrFail($id);
            if ($order->archived_at === null) {
                throw ValidationException::withMessages(['archive' => 'This order is not archived.']);
            }
            $order->update(['archived_at' => null, 'archived_by_user_id' => null]);
        });
    }

    public function trash(int $id, User $admin, string $number, string $reason): void
    {
        DB::transaction(function () use ($id, $admin, $number, $reason) {
            $order = Order::query()->lockForUpdate()->findOrFail($id);
            $this->checkNumber($order, $number);
            if (! $this->canTrash($order)) {
                throw ValidationException::withMessages(['delete' => 'Only safely cancelled, unpaid and unfulfilled COD orders can go to Trash.']);
            }
            $order->deleted_by_user_id = $admin->id;
            $order->deletion_reason = trim($reason);
            $order->save();
            $order->delete();
        });
    }

    public function restore(int $id): void
    {
        DB::transaction(function () use ($id) {
            $order = Order::onlyTrashed()->lockForUpdate()->findOrFail($id);
            $order->restore();
            $order->update(['deleted_by_user_id' => null, 'deletion_reason' => null]);
        });
    }

    public function purge(int $id, User $admin, string $number): void
    {
        DB::transaction(function () use ($id, $admin, $number) {
            $order = Order::onlyTrashed()->lockForUpdate()->findOrFail($id);
            $this->checkNumber($order, $number);
            if ($order->deleted_at->gt(now()->subDays(self::TRASH_DAYS))) {
                throw ValidationException::withMessages(['delete' => 'This order must remain restorable in Trash for at least 30 days.']);
            }
            // Recheck the financial and fulfilment history even if data changed while in Trash.
            $order->deleted_at = null;
            if (! $this->canTrash($order)) {
                throw ValidationException::withMessages(['delete' => 'This order is no longer eligible for permanent deletion.']);
            }
            DB::table('order_deletion_audits')->insert([
                'original_order_id' => $order->id,
                'order_number' => $order->order_number,
                'deleted_by_user_id' => $admin->id,
                'reason' => $order->deletion_reason,
                'deleted_at' => now(),
            ]);
            $order->forceDelete();
        });
    }

    private function checkNumber(Order $order, string $number): void
    {
        if (! hash_equals($order->order_number, trim($number))) {
            throw ValidationException::withMessages(['confirm_order_number' => 'Type the exact order number to confirm.']);
        }
    }
}
