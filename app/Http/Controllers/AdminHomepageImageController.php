<?php

namespace App\Http\Controllers;

use App\Models\HomepageImage;
use App\Services\AdminImageUpload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminHomepageImageController extends Controller
{
    public function index(): View
    {
        return view('admin.homepage-images', ['slots' => HomepageImage::resolvedSlots()]);
    }

    public function update(Request $request, string $slot, AdminImageUpload $uploads): RedirectResponse
    {
        $definition = config("homepage_media.slots.{$slot}");
        abort_unless(is_array($definition), 404);

        $dimensions = $definition['section'] === 'hero'
            ? 'dimensions:min_width=1200,min_height=675'
            : 'dimensions:min_width=600,min_height=800';

        $validated = $request->validate([
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:6144', $dimensions],
            'alt_text' => ['required', 'string', 'min:5', 'max:160'],
        ]);

        $current = HomepageImage::find($slot);
        $newPath = null;

        if ($request->hasFile('image')) {
            $newPath = $uploads->store($request->file('image'), 'images/homepage', 'image');
        }

        try {
            HomepageImage::updateOrCreate(
                ['slot' => $slot],
                [
                    'image_path' => $newPath ?? $current?->image_path,
                    'alt_text' => trim($validated['alt_text']),
                ]
            );
        } catch (\Throwable $exception) {
            $uploads->remove($newPath, 'images/homepage');
            throw $exception;
        }

        if ($newPath && $current?->image_path) {
            $uploads->remove($current->image_path, 'images/homepage');
        }

        return back()->with('success', $definition['label'].' updated.');
    }
}
