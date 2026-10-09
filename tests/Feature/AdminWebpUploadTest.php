<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use App\Services\AdminImageUpload;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminWebpUploadTest extends TestCase
{
    use DatabaseTransactions;

    public function test_png_upload_keeps_transparency_and_only_saves_webp(): void
    {
        $upload = UploadedFile::fake()->image('transparent.png', 2, 2);
        $source = imagecreatefrompng($upload->getRealPath());
        imagealphablending($source, false);
        imagesavealpha($source, true);
        imagesetpixel($source, 0, 0, imagecolorallocatealpha($source, 0, 0, 0, 127));
        imagepng($source, $upload->getRealPath());
        imagedestroy($source);

        $images = app(AdminImageUpload::class);
        $path = $images->store($upload, 'images/products', 'image');

        try {
            $this->assertStringEndsWith('.webp', $path);
            $this->assertSame('image/webp', getimagesize(public_path($path))['mime']);
            $this->assertFileDoesNotExist(public_path(substr($path, 0, -5).'.png'));
            $webp = imagecreatefromwebp(public_path($path));
            $this->assertGreaterThan(0, imagecolorsforindex($webp, imagecolorat($webp, 0, 0))['alpha']);
            imagedestroy($webp);
        } finally {
            $images->remove($path, 'images/products');
        }
    }

    public function test_vendor_logo_and_banner_upload_as_webp_only(): void
    {
        $admin = User::create([
            'name' => 'WebP Vendor Admin',
            'email' => 'webp-vendor-'.Str::random(10).'@example.com',
            'password' => bcrypt('password123'),
            'is_admin' => true,
        ]);
        $code = 'W'.strtoupper(Str::random(7));

        $this->actingAs($admin)->post('/admin/vendors', [
            'name' => 'WebP Vendor '.Str::random(8),
            'vendor_code' => $code,
            'logo' => UploadedFile::fake()->image('brand.png', 20, 20),
            'banner' => UploadedFile::fake()->image('cover.jpg', 60, 20),
        ])->assertRedirect(route('admin.vendors.index'));

        $vendor = Vendor::where('vendor_code', $code)->firstOrFail();
        try {
            foreach ([$vendor->logo, $vendor->banner] as $path) {
                $this->assertStringEndsWith('.webp', $path);
                $this->assertSame('image/webp', getimagesize(public_path($path))['mime']);
            }
        } finally {
            $images = app(AdminImageUpload::class);
            $images->remove($vendor->logo, 'images/vendors');
            $images->remove($vendor->banner, 'images/vendors');
        }
    }

    public function test_replacing_product_image_removes_old_upload_after_webp_is_saved(): void
    {
        $admin = User::create([
            'name' => 'WebP Product Admin',
            'email' => 'webp-product-'.Str::random(10).'@example.com',
            'password' => bcrypt('password123'),
            'is_admin' => true,
        ]);
        $images = app(AdminImageUpload::class);
        $oldPath = $images->store(UploadedFile::fake()->image('before.png'), 'images/products', 'image');
        $product = Product::create([
            'name' => 'WebP Replacement Product',
            'slug' => 'webp-replacement-'.Str::random(10),
            'sku' => 'WEBP-'.strtoupper(Str::random(8)),
            'category_id' => Category::firstOrFail()->id,
            'price' => 1200,
            'image' => $oldPath,
            'stock_quantity' => 2,
            'in_stock' => true,
        ]);

        $newPath = null;
        try {
            $this->actingAs($admin)->put('/admin/products/'.$product->id, [
                'name' => $product->name,
                'category_id' => $product->category_id,
                'price' => 1200,
                'stock_quantity' => 2,
                'in_stock' => 1,
                'image' => UploadedFile::fake()->image('after.jpg'),
            ])->assertRedirect(route('admin.products'));

            $newPath = $product->fresh()->image;
            $this->assertNotSame($oldPath, $newPath);
            $this->assertFileDoesNotExist(public_path($oldPath));
            $this->assertStringEndsWith('.webp', $newPath);
            $this->assertSame('image/webp', getimagesize(public_path($newPath))['mime']);
        } finally {
            $images->remove($oldPath, 'images/products');
            $images->remove($newPath, 'images/products');
        }
    }

    public function test_svg_is_rejected_for_new_vendor_upload(): void
    {
        $admin = User::create([
            'name' => 'SVG Guard Admin',
            'email' => 'svg-guard-'.Str::random(10).'@example.com',
            'password' => bcrypt('password123'),
            'is_admin' => true,
        ]);
        $code = 'S'.strtoupper(Str::random(7));

        $this->actingAs($admin)->post('/admin/vendors', [
            'name' => 'SVG Guard Vendor',
            'vendor_code' => $code,
            'logo' => UploadedFile::fake()->createWithContent('logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"/>'),
        ])->assertSessionHasErrors('logo');

        $this->assertDatabaseMissing('vendors', ['vendor_code' => $code]);
    }
}
