<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Services\AdminImageUpload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/* =========================================================================
 * ADMIN VENDOR CONTROLLER
 * Super-Admin management for vendor entities, brand onboardings,
 * storefront branding and active/inactive toggling.
 * Protected by 'auth' and 'admin' middleware guards.
 * ========================================================================= */
class AdminVendorController extends Controller
{
    /**
     * Display portfolio of all partner vendors and stores.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = Vendor::withCount('products')->orderBy('sort_order')->orderBy('name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('vendor_code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        $vendors = $query->paginate(15)->withQueryString();

        return view('admin.vendors.index', compact('vendors', 'search', 'status'));
    }

    /**
     * Show vendor onboarding form.
     */
    public function create(): View
    {
        return view('admin.vendors.create');
    }

    /**
     * Store newly onboarded vendor in database.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:vendors,slug',
            'vendor_code' => 'required|string|max:10|unique:vendors,vendor_code',
            'tagline' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:5000',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
        $baseSlug = $slug;
        $counter = 1;
        while (Vendor::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter++;
        }

        $uploads = app(AdminImageUpload::class);
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $uploads->store($request->file('logo'), 'images/vendors', 'logo');
        }

        $bannerPath = null;
        try {
            if ($request->hasFile('banner')) {
                $bannerPath = $uploads->store($request->file('banner'), 'images/vendors', 'banner');
            }
        } catch (\Throwable $exception) {
            $uploads->remove($logoPath, 'images/vendors');
            throw $exception;
        }

        try {
            $vendor = Vendor::create([
                'name' => $request->name,
                'slug' => $slug,
                'vendor_code' => strtoupper(trim($request->vendor_code)),
                'tagline' => $request->tagline,
                'description' => $request->description,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'sort_order' => (int) $request->input('sort_order', 0),
                'is_active' => $request->boolean('is_active', true),
                'logo' => $logoPath,
                'banner' => $bannerPath,
            ]);
        } catch (\Throwable $exception) {
            $uploads->remove($logoPath, 'images/vendors');
            $uploads->remove($bannerPath, 'images/vendors');
            throw $exception;
        }

        return redirect()->route('admin.vendors.index')->with(
            'success',
            "Vendor '{$vendor->name}' ({$vendor->vendor_code}) onboarded successfully."
        );
    }

    /**
     * Show vendor edit form.
     */
    public function edit(int $id): View
    {
        $vendor = Vendor::findOrFail($id);

        return view('admin.vendors.edit', compact('vendor'));
    }

    /**
     * Update existing vendor profile.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $vendor = Vendor::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => "required|string|max:255|unique:vendors,slug,{$vendor->id}",
            'vendor_code' => "required|string|max:10|unique:vendors,vendor_code,{$vendor->id}",
            'tagline' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:5000',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $updateData = [
            'name' => $request->name,
            'slug' => Str::slug($request->slug),
            'vendor_code' => strtoupper(trim($request->vendor_code)),
            'tagline' => $request->tagline,
            'description' => $request->description,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'sort_order' => (int) $request->input('sort_order', 0),
            'is_active' => $request->boolean('is_active', false),
        ];

        $uploads = app(AdminImageUpload::class);
        $oldLogo = $vendor->logo;
        $oldBanner = $vendor->banner;
        $newLogo = null;
        $newBanner = null;
        if ($request->hasFile('logo')) {
            $newLogo = $uploads->store($request->file('logo'), 'images/vendors', 'logo');
            $updateData['logo'] = $newLogo;
        }

        try {
            if ($request->hasFile('banner')) {
                $newBanner = $uploads->store($request->file('banner'), 'images/vendors', 'banner');
                $updateData['banner'] = $newBanner;
            }
            $vendor->update($updateData);
        } catch (\Throwable $exception) {
            $uploads->remove($newLogo, 'images/vendors');
            $uploads->remove($newBanner, 'images/vendors');
            throw $exception;
        }

        if ($newLogo) {
            $uploads->remove($oldLogo, 'images/vendors');
        }
        if ($newBanner) {
            $uploads->remove($oldBanner, 'images/vendors');
        }

        return redirect()->route('admin.vendors.index')->with(
            'success',
            "Vendor '{$vendor->name}' updated successfully."
        );
    }

    /**
     * Toggle vendor active status.
     */
    public function toggleActive(int $id): JsonResponse|RedirectResponse
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->update([
            'is_active' => ! $vendor->is_active,
        ]);

        $statusText = $vendor->is_active ? 'Active' : 'Suspended / Inactive';

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $vendor->is_active,
                'message' => "Vendor '{$vendor->name}' status is now {$statusText}.",
            ]);
        }

        return redirect()->back()->with('success', "Vendor '{$vendor->name}' status is now {$statusText}.");
    }
}
