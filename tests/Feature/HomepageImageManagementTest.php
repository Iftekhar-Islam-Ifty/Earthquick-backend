<?php

namespace Tests\Feature;

use App\Models\HomepageImage;
use App\Models\User;
use App\Services\AdminImageUpload;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Tests\TestCase;

class HomepageImageManagementTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        return User::create([
            'name' => 'Homepage Editor',
            'email' => 'homepage-editor-'.Str::random(10).'@example.com',
            'password' => bcrypt('password123'),
            'is_admin' => true,
        ]);
    }

    public function test_only_admin_can_manage_homepage_images(): void
    {
        $this->get(route('admin.homepage-images.index'))->assertRedirect('/login');

        $customer = User::create([
            'name' => 'Customer',
            'email' => 'customer-'.Str::random(10).'@example.com',
            'password' => bcrypt('password123'),
            'is_admin' => false,
        ]);
        $this->actingAs($customer)->get(route('admin.homepage-images.index'))->assertForbidden();
        $this->actingAs($customer)->post(route('admin.homepage-images.update', 'hero-1'), [
            'alt_text' => 'Changed without permission',
        ])->assertForbidden();
    }

    public function test_original_images_remain_until_admin_changes_a_slot(): void
    {
        $this->actingAs($this->admin())->get(route('admin.homepage-images.index'))
            ->assertOk()
            ->assertSee('1920 × 1080')
            ->assertSee('900 × 1280')
            ->assertSee('Look 7');

        $home = $this->get('/')->assertOk();
        $home->assertSee(asset('images/hero/hero-main-saree-2.webp'), false)
            ->assertSee(asset('images/saree/saree-01.jpg'), false);
        $this->assertNull(HomepageImage::find('hero-1'));
    }

    public function test_admin_uploads_replaces_and_describes_a_hero_image(): void
    {
        $this->actingAs($this->admin());
        $editor = route('admin.homepage-images.index');
        $uploads = app(AdminImageUpload::class);
        $first = null;
        $second = null;

        try {
            $this->from($editor)->post(route('admin.homepage-images.update', 'hero-1'), [
                'image' => UploadedFile::fake()->image('campaign.png', 1600, 900),
                'alt_text' => 'New handloom campaign photograph',
            ])->assertRedirect($editor)->assertSessionHasNoErrors();

            $first = HomepageImage::findOrFail('hero-1')->image_path;
            $this->assertStringStartsWith('images/homepage/', $first);
            $this->assertStringEndsWith('.webp', $first);
            $this->assertSame('image/webp', getimagesize(public_path($first))['mime']);
            $this->get('/')->assertOk()
                ->assertSee('rel="preload" as="image" href="'.asset($first).'"', false)
                ->assertSee('alt="New handloom campaign photograph"', false)
                ->assertSee('property="og:image" content="'.asset($first).'"', false);

            $this->from($editor)->post(route('admin.homepage-images.update', 'hero-1'), [
                'image' => UploadedFile::fake()->image('replacement.jpg', 1600, 900),
                'alt_text' => 'Replacement handloom campaign image',
            ])->assertRedirect($editor)->assertSessionHasNoErrors();

            $second = HomepageImage::findOrFail('hero-1')->image_path;
            $this->assertNotSame($first, $second);
            $this->assertFileDoesNotExist(public_path($first));
            $this->assertFileExists(public_path($second));

            $this->from($editor)->post(route('admin.homepage-images.update', 'hero-1'), [
                'alt_text' => 'Updated description only',
            ])->assertRedirect($editor)->assertSessionHasNoErrors();
            $this->assertSame($second, HomepageImage::findOrFail('hero-1')->image_path);
        } finally {
            $uploads->remove($first, 'images/homepage');
            $uploads->remove($second, 'images/homepage');
        }
    }

    public function test_styled_image_uses_its_existing_filter_and_rejects_invalid_uploads(): void
    {
        $this->actingAs($this->admin());
        $path = null;
        try {
            $this->post(route('admin.homepage-images.update', 'look-3'), [
                'image' => UploadedFile::fake()->image('styled-look.png', 900, 1280),
                'alt_text' => 'Everyday look with a handcrafted bag',
            ])->assertRedirect()->assertSessionHasNoErrors();

            $path = HomepageImage::findOrFail('look-3')->image_path;
            $this->assertFileExists(public_path($path));
            $this->get('/')->assertOk()
                ->assertSee('data-category="details everyday" id="coverflow-card-3"', false)
                ->assertSee(asset($path), false)
                ->assertSee('alt="Everyday look with a handcrafted bag"', false);

            $this->post(route('admin.homepage-images.update', 'look-3'), [
                'image' => UploadedFile::fake()->image('too-small.png', 200, 300),
                'alt_text' => 'Too small to show properly',
            ])->assertSessionHasErrors('image');
            $this->assertSame($path, HomepageImage::findOrFail('look-3')->image_path);
            $this->post(route('admin.homepage-images.update', 'unknown-slot'), [
                'alt_text' => 'Invalid image position',
            ])->assertNotFound();
        } finally {
            app(AdminImageUpload::class)->remove($path, 'images/homepage');
        }
    }
}
