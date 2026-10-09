<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/** Store admin-uploaded raster images as a single WebP file, never a public original. */
class AdminImageUpload
{
    /** @param UploadedFile[] $uploads */
    public function storeMany(array $uploads, string $folder, string $field): array
    {
        $paths = [];
        try {
            foreach ($uploads as $upload) {
                $paths[] = $this->store($upload, $folder, $field);
            }
        } catch (\Throwable $exception) {
            foreach ($paths as $path) {
                $this->remove($path, $folder);
            }
            throw $exception;
        }

        return $paths;
    }

    public function store(UploadedFile $upload, string $folder, string $field): string
    {
        if (! extension_loaded('gd') || ! function_exists('imagewebp')) {
            throw ValidationException::withMessages([$field => 'WebP processing is unavailable on this server. Contact the hosting provider.']);
        }

        $source = $upload->getRealPath();
        $info = $source ? @getimagesize($source) : false;
        if (! $info || ! in_array($info['mime'], ['image/jpeg', 'image/png', 'image/webp'], true)
            || $info[0] < 1 || $info[1] < 1 || $info[0] * $info[1] > 25_000_000) {
            throw ValidationException::withMessages([$field => 'Upload a valid JPEG, PNG or WebP image (up to 25 megapixels).']);
        }

        $image = match ($info['mime']) {
            'image/jpeg' => @imagecreatefromjpeg($source),
            'image/png' => @imagecreatefrompng($source),
            'image/webp' => @imagecreatefromwebp($source),
        };
        if ($image === false) {
            throw ValidationException::withMessages([$field => 'The image could not be read. Try another file.']);
        }

        $directory = public_path($folder);
        $name = Str::uuid().'.webp';
        $temporary = $directory.DIRECTORY_SEPARATOR.$name.'.tmp';
        $final = $directory.DIRECTORY_SEPARATOR.$name;

        try {
            if (! is_dir($directory) && ! @mkdir($directory, 0755, true) && ! is_dir($directory)) {
                throw new \RuntimeException('Image directory could not be created.');
            }
            imagealphablending($image, false);
            imagesavealpha($image, true);
            if (! @imagewebp($image, $temporary, 82) || ! is_file($temporary) || filesize($temporary) === 0
                || ! @rename($temporary, $final)) {
                throw new \RuntimeException('WebP image could not be saved.');
            }
        } catch (\Throwable $exception) {
            @unlink($temporary);
            @unlink($final);
            throw ValidationException::withMessages([$field => 'The image could not be converted to WebP. Please try again.']);
        } finally {
            imagedestroy($image);
        }

        return $folder.'/'.$name;
    }

    public function remove(?string $path, string $folder): void
    {
        if ($path && str_starts_with($path, $folder.'/') && is_file(public_path($path))) {
            @unlink(public_path($path));
        }
    }
}
