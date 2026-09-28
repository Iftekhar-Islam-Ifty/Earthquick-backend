<?php

namespace App\Http\Controllers;

use App\Models\SupportInquiry;
use App\Services\CustomerCommunications;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class SupportInquiryController extends Controller
{
    public function store(Request $request, CustomerCommunications $communications): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:150',
            'phone' => ['required', 'string', 'regex:/^(?:\+?88)?01[3-9]\d{8}$/'],
            'email' => 'nullable|email|max:255',
            'subject' => 'required|in:General Inquiry,Order Status & Delivery,Vendor / Artisan Partnership,Custom Tailoring & Sizing,Exchange or Return',
            'message' => 'required|string|min:10|max:5000',
            'website' => 'nullable|string|max:100',
        ]);
        if ($validator->fails()) {
            return redirect()->to(route('about').'#contact-support')
                ->withErrors($validator)->withInput($request->except('website'));
        }
        $data = $validator->validated();

        // A hidden field discourages automated spam without claiming receipt.
        if (! empty($data['website'])) {
            return redirect()->to(route('about').'#contact-support');
        }

        $inquiry = SupportInquiry::create([
            'user_id' => $request->user()?->id,
            'name' => trim($data['name']),
            'phone' => trim($data['phone']),
            'email' => isset($data['email']) ? trim($data['email']) : null,
            'subject' => $data['subject'],
            'message' => trim($data['message']),
            'status' => 'open',
        ]);
        $communications->inquiry($inquiry);

        return redirect()->to(route('about').'#contact-support')
            ->with('success', "Your inquiry #{$inquiry->id} was received. Our team will review it.");
    }

    public function index(Request $request): View
    {
        $status = $request->query('status');
        $inquiries = SupportInquiry::with('handler')
            ->when(in_array($status, ['open', 'in_progress', 'closed'], true), fn ($query) => $query->where('status', $status))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.support-inquiries', compact('inquiries', 'status'));
    }

    public function update(Request $request, SupportInquiry $inquiry): RedirectResponse
    {
        $data = $request->validate([
            'status' => 'required|in:open,in_progress,closed',
            'internal_note' => 'required|string|min:5|max:2000',
        ]);
        $inquiry->update([
            'status' => $data['status'],
            'internal_note' => trim($data['internal_note']),
            'handled_by_user_id' => $request->user()->id,
            'handled_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Support inquiry updated. No customer reply was sent.');
    }
}
