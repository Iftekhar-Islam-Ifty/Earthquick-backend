<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class SharedUiTest extends TestCase
{
    public function test_shared_layout_contracts_render_on_home_catalog_and_store_pages(): void
    {
        URL::forceRootUrl('http://localhost/earthquick/public');
        $paths = ['/', '/stores', '/stores/nous-telos', '/stores/bright', '/shop/women',
            '/search?q=Test', '/product/'.Product::firstOrFail()->slug];

        foreach ($paths as $path) {
            // Keep the simulated request path independent of the forced URL
            // generator root; real Apache supplies the subfolder SCRIPT_NAME.
            $response = $this->get('http://localhost'.$path)->assertOk();
            $document = new \DOMDocument;
            $previous = libxml_use_internal_errors(true);
            $document->loadHTML($response->getContent());
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
            $xpath = new \DOMXPath($document);
            $this->assertSame(1, $xpath->query('//*[@id="eq-toast-container"]')->length, $path);
            $this->assertSame(1, $xpath->query('//*[@id="main-content"]')->length, $path);
            $this->assertSame('http://localhost/earthquick/public',
                $xpath->evaluate('string(//meta[@name="app-url"]/@content)'), $path);
            foreach ($xpath->query('//button[contains(@class,"eq-product-card__quick-add")]') as $button) {
                $this->assertFalse($button->hasAttribute('onclick'), $path);
                $this->assertSame('quick-view', $button->getAttribute('data-action'), $path);
            }
            // Navigation-only home overlays must not advertise adding to the cart.
            if ($path === '/') {
                foreach ($xpath->query('//span[contains(@class,"eq-product-card__quick-add")]') as $overlay) {
                    $this->assertSame('View product', trim($overlay->textContent));
                }
            }
        }
    }
}
