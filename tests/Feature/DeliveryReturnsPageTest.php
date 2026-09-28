<?php

namespace Tests\Feature;

use Tests\TestCase;

class DeliveryReturnsPageTest extends TestCase
{
    public function test_policy_page_is_public_and_describes_current_rules(): void
    {
        $this->get(route('policies.delivery-returns'))
            ->assertOk()
            ->assertSee('Delivery, Returns &amp; Refunds', false)
            ->assertSee('৳80 delivery')
            ->assertSee('৳150 delivery')
            ->assertSee('৳3,000 or more')
            ->assertSee('partial return does not refund the original delivery charge')
            ->assertSee('Refund approval records the amount but does not send money')
            ->assertSee('id="footer-link-delivery-returns"', false);
    }

    public function test_about_page_points_to_policy_and_uses_current_delivery_zones(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertSee(route('policies.delivery-returns').'#delivery', false)
            ->assertSee(route('policies.delivery-returns').'#returns', false)
            ->assertSee('৳150 outside Chattogram')
            ->assertDontSee('Chattogram and Dhaka takes 2 to 3 business days');
    }
}
