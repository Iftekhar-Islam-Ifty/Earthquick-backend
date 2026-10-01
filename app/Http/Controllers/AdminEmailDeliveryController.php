<?php

namespace App\Http\Controllers;

use App\Models\OutboundMessage;
use App\Services\OutboundMailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminEmailDeliveryController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        if (! in_array($status, ['pending', 'sending', 'failed', 'sent'], true)) {
            $status = null;
        }
        $messages = OutboundMessage::query()
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()->paginate(20)->withQueryString();
        $counts = [];
        foreach (['pending', 'sending', 'failed', 'sent'] as $name) {
            $counts[$name] = OutboundMessage::where('status', $name)->count();
        }

        return view('admin.email-deliveries', compact('messages', 'status', 'counts'));
    }

    public function retry(Request $request, int $id, OutboundMailService $outbox): RedirectResponse
    {
        $request->validate(['confirm_retry' => 'accepted']);
        $sent = $outbox->requeueFailed($id);

        return redirect()->route('admin.email-deliveries', ['status' => $sent ? 'sent' : 'pending'])
            ->with($sent ? 'success' : 'error', $sent
                ? 'Email handed off to SMTP. Inbox delivery still needs confirmation.'
                : 'SMTP handoff failed again. The message remains in the outbox for retry or staff review.');
    }
}
