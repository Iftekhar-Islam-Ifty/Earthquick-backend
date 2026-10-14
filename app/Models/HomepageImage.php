<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageImage extends Model
{
    protected $primaryKey = 'slot';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['slot', 'image_path', 'alt_text'];

    /** Resolve editable image slots while keeping bundled images as safe defaults. */
    public static function resolvedSlots(): array
    {
        $saved = static::query()->get()->keyBy('slot');
        $slots = [];

        foreach (config('homepage_media.slots', []) as $key => $default) {
            $override = $saved->get($key);
            $slots[$key] = [
                ...$default,
                'image' => $override?->image_path ?: $default['image'],
                'alt' => $override?->alt_text ?: $default['alt'],
                'custom' => (bool) $override?->image_path,
            ];
        }

        return $slots;
    }
}
