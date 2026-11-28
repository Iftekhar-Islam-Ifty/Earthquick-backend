<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminNewsletterSubscriptionController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        if (! in_array($status, ['confirmed', 'pending', 'unsubscribed'], true)) {
            $status = null;
        }
        $subscriptions = NewsletterSubscription::query()
            ->where('store_slug', 'nous-telos')
            ->when($status === 'confirmed', fn ($query) => $query->whereNotNull('confirmed_at')->whereNull('unsubscribed_at'))
            ->when($status === 'pending', fn ($query) => $query->whereNull('confirmed_at')->whereNull('unsubscribed_at'))
            ->when($status === 'unsubscribed', fn ($query) => $query->whereNotNull('unsubscribed_at'))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.newsletter-subscriptions', compact('subscriptions', 'status'));
    }
}
