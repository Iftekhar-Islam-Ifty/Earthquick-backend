<?php

namespace App\Http\Controllers;

use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscription;
use App\Services\NewsletterCampaignService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminNewsletterCampaignController extends Controller
{
    public function index(): View
    {
        $campaigns = NewsletterCampaign::query()->withCount('recipients')->latest()->paginate(15);
        $activeCount = NewsletterSubscription::query()->where('store_slug', 'nous-telos')
            ->whereNotNull('confirmed_at')->whereNull('unsubscribed_at')->count();

        return view('admin.newsletter-campaigns', compact('campaigns', 'activeCount'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'min:3', 'max:180'],
            'body' => ['required', 'string', 'min:10', 'max:8000'],
        ]);
        $campaign = NewsletterCampaign::create([
            'store_slug' => 'nous-telos',
            'subject' => trim($data['subject']),
            'body' => trim($data['body']),
            'status' => 'draft',
            'created_by_user_id' => $request->user()->id,
        ]);

        return redirect()->route('admin.newsletter-campaigns.show', $campaign)
            ->with('success', 'Draft saved. Review it before launching.');
    }

    public function show(NewsletterCampaign $campaign): View
    {
        $counts = [
            'targeted' => $campaign->recipients()->count(),
            'waiting' => $campaign->recipients()->whereNull('outbound_message_id')->whereNull('skipped_at')->count(),
            'skipped' => $campaign->recipients()->whereNotNull('skipped_at')->count(),
        ];
        foreach (['sent', 'pending', 'sending', 'failed', 'skipped'] as $status) {
            $counts[$status] = $campaign->recipients()->whereHas('message', fn ($query) => $query->where('status', $status))->count()
                + ($status === 'skipped' ? $counts['skipped'] : 0);
        }

        return view('admin.newsletter-campaign', compact('campaign', 'counts'));
    }

    public function update(Request $request, NewsletterCampaign $campaign): RedirectResponse
    {
        abort_unless($campaign->status === 'draft', 409);
        $data = $request->validate([
            'subject' => ['required', 'string', 'min:3', 'max:180'],
            'body' => ['required', 'string', 'min:10', 'max:8000'],
        ]);
        $campaign->update(['subject' => trim($data['subject']), 'body' => trim($data['body'])]);

        return back()->with('success', 'Draft updated.');
    }

    public function launch(Request $request, NewsletterCampaign $campaign, NewsletterCampaignService $service): RedirectResponse
    {
        $request->validate(['confirm_send' => ['accepted']]);
        $count = $service->launch($campaign);

        return back()->with('success', "Campaign queued for {$count} confirmed subscribers. Up to five messages are processed every five minutes while the scheduler runs.");
    }

    public function cancel(NewsletterCampaign $campaign, NewsletterCampaignService $service): RedirectResponse
    {
        $service->cancel($campaign);

        return back()->with('success', 'Campaign stopped. Messages already accepted by SMTP cannot be recalled.');
    }
}
