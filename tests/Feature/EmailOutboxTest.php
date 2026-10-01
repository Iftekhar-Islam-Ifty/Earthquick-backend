<?php

namespace Tests\Feature;

use App\Mail\EarthquickNotice;
use App\Models\OutboundMessage;
use App\Models\SupportInquiry;
use App\Models\User;
use App\Services\OutboundMailService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\MailManager;
use RuntimeException;
use Tests\TestCase;

class EmailOutboxTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config()->set('communications.email_enabled', true);
        config()->set('mail.default', 'smtp');
    }

    public function test_successful_handoff_keeps_only_masked_address_and_metadata(): void
    {
        Mail::fake();
        app(OutboundMailService::class)->queueAndSend('customer@example.test', 'Order received', 'Private order details', ['event' => 'placed']);

        $message = OutboundMessage::query()->sole();
        $this->assertSame('sent', $message->status);
        $this->assertSame(1, $message->attempts);
        $this->assertSame('c***@example.test', $message->recipient_hint);
        $this->assertNull($message->recipient);
        $this->assertNull($message->subject);
        $this->assertNull($message->body);
        Mail::assertSent(EarthquickNotice::class, 1);
    }

    public function test_failure_is_encrypted_and_retried_only_when_due(): void
    {
        Mail::shouldReceive('to')->once()->andThrow(new RuntimeException('Simulated transport failure'));
        app(OutboundMailService::class)->queueAndSend('customer@example.test', 'Order received', 'Private order details', ['event' => 'placed']);

        $message = OutboundMessage::query()->sole();
        $this->assertSame('pending', $message->status);
        $this->assertSame(1, $message->attempts);
        $this->assertSame('customer@example.test', $message->recipient);
        $this->assertSame('Private order details', $message->body);
        $raw = DB::table('outbound_messages')->where('id', $message->id)->first();
        $this->assertStringNotContainsString('customer@example.test', $raw->recipient);
        $this->assertStringNotContainsString('Private order details', $raw->body);
        $this->assertSame(0, app(OutboundMailService::class)->retryDue()['processed']);

        $this->travel(6)->minutes();
        Mail::swap(new MailManager(app()));
        Mail::fake();
        $this->artisan('earthquick:mail-retry')->assertExitCode(0);
        Mail::assertSent(EarthquickNotice::class, 1);
        $this->assertSame('sent', $message->fresh()->status);
        $this->assertSame(2, $message->fresh()->attempts);
        $this->assertNull($message->fresh()->recipient);
    }

    public function test_stale_final_attempt_is_marked_failed_and_admin_can_retry_it(): void
    {
        $message = OutboundMessage::create([
            'recipient_hint' => 'c***@example.test',
            'recipient' => 'customer@example.test',
            'subject' => 'Notice', 'body' => 'Private details',
            'context' => ['event' => 'placed'], 'status' => 'sending',
            'attempts' => OutboundMailService::MAX_ATTEMPTS,
            'last_attempt_at' => now()->subMinutes(11),
        ]);
        Mail::fake();
        $this->assertSame(0, app(OutboundMailService::class)->retryDue()['processed']);
        $this->assertSame('failed', $message->fresh()->status);
        Mail::assertNothingSent();

        $this->get(route('admin.email-deliveries'))->assertRedirect('/login');
        $customer = User::factory()->create(['is_admin' => false]);
        $this->actingAs($customer)->get(route('admin.email-deliveries'))->assertForbidden();
        $this->post(route('admin.email-deliveries.retry', $message->id), ['confirm_retry' => '1'])->assertForbidden();

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get(route('admin.email-deliveries', ['status' => 'failed']))
            ->assertOk()->assertSee('c***@example.test')->assertDontSee('Private details');
        $this->post(route('admin.email-deliveries.retry', $message->id), [])->assertSessionHasErrors('confirm_retry');
        $this->post(route('admin.email-deliveries.retry', $message->id), ['confirm_retry' => '1'])
            ->assertRedirect(route('admin.email-deliveries', ['status' => 'sent']));
        Mail::assertSent(EarthquickNotice::class, 1);
        $this->assertSame('sent', $message->fresh()->status);
        $this->assertNull($message->fresh()->body);
    }

    public function test_health_is_read_only_and_disabled_transport_does_not_send(): void
    {
        Mail::fake();
        config()->set('mail.default', 'log');
        app(OutboundMailService::class)->queueAndSend('customer@example.test', 'Notice', 'Body', ['event' => 'placed']);
        $this->assertSame('pending', OutboundMessage::query()->sole()->status);
        $this->artisan('earthquick:mail-health')->assertExitCode(0);
        $this->artisan('earthquick:mail-retry')->assertExitCode(1);
        Mail::assertNothingSent();
    }

    public function test_admin_support_and_email_times_display_dhaka_time_without_changing_stored_utc(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->travelTo(CarbonImmutable::parse('2026-10-01 12:34:00', 'UTC'));

        $inquiry = SupportInquiry::create([
            'name' => 'QA Customer', 'phone' => '01712345678', 'email' => 'customer@example.test',
            'subject' => 'Time check', 'message' => 'Checking timestamps', 'status' => 'in_progress',
            'handled_by_user_id' => $admin->id, 'handled_at' => now()->addMinutes(5),
            'internal_note' => 'Followed up with the customer',
        ]);
        $message = OutboundMessage::create([
            'recipient_hint' => 'c***@example.test', 'context' => ['event' => 'support_customer'],
            'status' => 'pending', 'next_attempt_at' => now()->addHour(),
        ]);
        OutboundMessage::create([
            'recipient_hint' => 'c***@example.test', 'context' => ['event' => 'support_admin'],
            'status' => 'sent', 'sent_at' => now()->addMinutes(5),
        ]);

        $this->actingAs($admin)->get(route('admin.support.index'))->assertOk()
            ->assertSee('Received 01 Oct 2026, 06:34 PM BDT')
            ->assertSee('01 Oct 2026, 06:39 PM BDT');
        $this->get(route('admin.email-deliveries'))->assertOk()
            ->assertSee('01 Oct 2026, 06:34 PM BDT')
            ->assertSee('Next retry: 01 Oct 2026, 07:34 PM BDT')
            ->assertSee('SMTP handoff: 01 Oct 2026, 06:39 PM BDT');
        $this->assertSame('2026-10-01 12:34:00', $inquiry->fresh()->created_at->format('Y-m-d H:i:s'));
        $this->assertSame('2026-10-01 12:34:00', $message->fresh()->created_at->format('Y-m-d H:i:s'));
    }
}
