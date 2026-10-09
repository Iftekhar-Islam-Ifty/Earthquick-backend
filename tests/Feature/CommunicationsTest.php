<?php

namespace Tests\Feature;

use App\Mail\EarthquickNotice;
use App\Models\Order;
use App\Models\SupportInquiry;
use App\Models\User;
use App\Services\CustomerCommunications;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CommunicationsTest extends TestCase
{
    private function inquiryData(): array
    {
        return [
            'name' => 'QA Customer', 'phone' => '01712345678',
            'email' => 'customer@example.test', 'subject' => 'General Inquiry',
            'message' => 'Please help me with my product inquiry.',
        ];
    }

    private function order(?string $email = 'customer@example.test'): Order
    {
        return Order::create([
            'order_number' => 'EQ-COMMS-'.uniqid(),
            'customer_name' => 'QA Customer', 'customer_phone' => '01712345678',
            'customer_email' => $email, 'delivery_zone' => 'inside_ctg',
            'district' => 'Chattogram', 'area' => 'QA', 'address' => 'Synthetic QA address',
            'payment_method' => 'cod', 'payment_status' => 'due_on_delivery',
            'subtotal' => 100, 'delivery_fee' => 80, 'total' => 180, 'status' => 'pending',
        ]);
    }

    public function test_contact_form_saves_inquiry_without_sender_and_admin_can_follow_up(): void
    {
        Mail::fake();
        config()->set('communications.email_enabled', false);
        $this->get(route('about'))->assertOk()->assertSee('action="'.route('contact.store').'"', false);
        $this->post(route('contact.store'), $this->inquiryData())
            ->assertRedirect(route('about').'#contact-support')->assertSessionHas('success');
        $inquiry = SupportInquiry::query()->sole();
        $this->assertSame('open', $inquiry->status);
        $this->assertSame('QA Customer', $inquiry->name);
        Mail::assertNothingSent();

        $this->get(route('admin.support.index'))->assertRedirect('/login');
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get(route('admin.support.index'))
            ->assertOk()->assertSee('QA Customer')->assertSee('Please help me')
            ->assertSeeInOrder(['Customer Orders', 'Stock &amp; Catalog', 'Vendors &amp; Stores', 'Coupons &amp; Offers', 'Customer Care', 'Support Inbox'], false)
            ->assertSee('class="eq-support-filter is-active"', false)
            ->assertSee('href="tel:01712345678"', false)
            ->assertSee('href="mailto:customer@example.test?', false);
        $this->post(route('admin.support.update', $inquiry), [
            'status' => 'in_progress', 'internal_note' => 'Called the customer for clarification',
        ])->assertRedirect();
        $this->assertSame('in_progress', $inquiry->fresh()->status);
        $this->assertSame($admin->id, $inquiry->fresh()->handled_by_user_id);
        Mail::assertNothingSent();
    }

    public function test_contact_validation_and_honeypot_prevent_false_storage(): void
    {
        $this->post(route('contact.store'), array_replace($this->inquiryData(), [
            'phone' => 'not-a-phone',
        ]))->assertSessionHasErrors('phone');
        $this->assertSame(0, SupportInquiry::count());
        $this->post(route('contact.store'), array_replace($this->inquiryData(), [
            'website' => 'spam.example',
        ]))->assertRedirect();
        $this->assertSame(0, SupportInquiry::count());
    }

    public function test_enabling_email_sends_support_acknowledgement_and_admin_alert(): void
    {
        Mail::fake();
        config()->set('communications.email_enabled', true);
        config()->set('mail.default', 'smtp');
        config()->set('communications.support_email', 'support@example.test');
        $this->post(route('contact.store'), $this->inquiryData())->assertRedirect();
        Mail::assertSent(EarthquickNotice::class, 2);
        Mail::assertSent(EarthquickNotice::class, fn ($mail) => $mail->hasTo('customer@example.test')
            && $mail->noticeSubject === 'Rthquick: Inquiry received');
        Mail::assertSent(EarthquickNotice::class, fn ($mail) => $mail->hasTo('support@example.test')
            && $mail->noticeSubject === 'Rthquick: New support inquiry');
    }

    public function test_log_mailer_does_not_pretend_to_send_customer_email(): void
    {
        Mail::fake();
        config()->set('communications.email_enabled', true);
        config()->set('mail.default', 'log');
        app(CustomerCommunications::class)->order($this->order(), 'placed');
        Mail::assertNothingSent();
    }

    public function test_order_email_requires_switch_and_saved_email_and_status_change(): void
    {
        Mail::fake();
        $order = $this->order();
        app(CustomerCommunications::class)->order($order, 'placed');
        Mail::assertNothingSent();

        config()->set('communications.email_enabled', true);
        config()->set('mail.default', 'smtp');
        app(CustomerCommunications::class)->order($this->order(null), 'placed');
        Mail::assertNothingSent();

        $customer = User::factory()->create(['email' => 'account-owner@example.test']);
        $linked = $this->order(null);
        $linked->update(['user_id' => $customer->id]);
        app(CustomerCommunications::class)->order($linked, 'placed');
        Mail::assertSent(EarthquickNotice::class, fn ($mail) => $mail->hasTo('account-owner@example.test'));
        Mail::fake();

        $admin = User::factory()->create(['is_admin' => true]);
        $url = route('admin.orders.update-status', $order->id);
        $this->actingAs($admin)->post($url, ['status' => 'confirmed'])->assertRedirect();
        Mail::assertSent(EarthquickNotice::class, 1);
        Mail::assertSent(EarthquickNotice::class, fn ($mail) => $mail->hasTo('customer@example.test')
            && $mail->noticeSubject === 'Rthquick: Order status updated');
        $this->post($url, ['status' => 'confirmed', 'courier_name' => 'QA Courier'])->assertRedirect();
        Mail::assertSent(EarthquickNotice::class, 1);
    }

    public function test_public_contact_links_are_real_actions_and_chat_requires_confirmed_number(): void
    {
        config()->set('communications.support_email', 'support@example.test');
        config()->set('communications.support_phone', '01805-423000');
        config()->set('communications.support_whatsapp', null);

        $this->get(route('about'))->assertOk()
            ->assertSee('href="mailto:support@example.test"', false)
            ->assertSee('href="tel:01805423000"', false)
            ->assertDontSee('wa.me/');

        config()->set('communications.support_whatsapp', '8801805423000');
        $this->get(route('about'))->assertOk()
            ->assertSee('href="https://wa.me/8801805423000"', false);
    }

    public function test_mail_smoke_command_rejects_log_mailer_and_uses_controlled_inbox(): void
    {
        Mail::fake();
        config()->set('mail.default', 'log');
        $this->artisan('earthquick:mail-test', ['to' => 'qa@example.test'])->assertExitCode(1);
        Mail::assertNothingSent();

        config()->set('mail.default', 'smtp');
        config()->set('mail.from.address', 'support@example.test');
        $this->artisan('earthquick:mail-test', ['to' => 'qa@example.test'])->assertExitCode(0);
        Mail::assertSent(EarthquickNotice::class, fn ($mail) => $mail->hasTo('qa@example.test')
            && $mail->noticeSubject === 'Rthquick: Email delivery test');
    }
}
