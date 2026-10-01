<?php

namespace App\Services;

use App\Models\Order;
use App\Models\SupportInquiry;
use Illuminate\Support\Facades\Log;

class CustomerCommunications
{
    public function order(Order $order, string $event, ?string $detail = null): void
    {
        $number = $order->order_number;
        [$subject, $body] = match ($event) {
            'placed' => ['Order received', "We received order #{$number}. You can review its details in your Earthquick account or checkout session."],
            'status' => ['Order status updated', "Order #{$number} is now {$detail}."],
            'cod_paid' => ['COD payment recorded', "Earthquick recorded receipt of payment for COD order #{$number}."],
            'cancellation_requested' => ['Cancellation request received', "We received your cancellation request for order #{$number}. The order is not cancelled until Earthquick approves it."],
            'cancelled' => ['Order cancelled', "Order #{$number} has been cancelled by Earthquick."],
            'cancellation_rejected' => ['Cancellation request declined', "The cancellation request for order #{$number} was declined. Please contact support if you need help."],
            'return_requested' => ['Return request received', "We received an item return request for order #{$number}. Please wait for authorization before sending the item."],
            'return_authorized' => ['Return authorized', "An item return for order #{$number} was authorized. Review the order page for the confirmed return-courier payer before sending the item."],
            'return_rejected' => ['Return request declined', "An item return request for order #{$number} was declined. Please contact support if you need help."],
            'return_received' => ['Returned item received', "Earthquick recorded physical receipt of a returned item for order #{$number}. Inspection and any refund are separate steps."],
            'return_inspected' => ['Return inspection completed', "A returned item for order #{$number} passed inspection. Any refund will be reviewed separately."],
            'return_inspection_rejected' => ['Return declined after inspection', "A returned item for order #{$number} did not pass inspection. Contact support if you have a question."],
            'refund_approved' => ['Refund approved', "A refund amount for order #{$number} was approved. Payment has not yet been confirmed."],
            'refund_no_due' => ['Refund review completed', "The refund review for order #{$number} found no amount due after discount allocation. Contact support if you have a question."],
            'refund_completed' => ['Refund recorded as paid', "Earthquick recorded a refund payment for order #{$number}. Contact support if you have not received it."],
            default => throw new \InvalidArgumentException('Unsupported order communication event.'),
        };

        $email = $order->customer_email ?: $order->user?->email;
        $this->send($email, "Earthquick: {$subject}", $body, ['order_id' => $order->id, 'event' => $event]);
    }

    public function inquiry(SupportInquiry $inquiry): void
    {
        $this->send(config('communications.support_email'), 'Earthquick: New support inquiry',
            "A new support inquiry #{$inquiry->id} is waiting in the admin inbox.",
            ['inquiry_id' => $inquiry->id, 'event' => 'support_admin']);
        $this->send($inquiry->email, 'Earthquick: Inquiry received',
            "We received your inquiry #{$inquiry->id}. Our team will review it and contact you using the details you provided.",
            ['inquiry_id' => $inquiry->id, 'event' => 'support_customer']);
    }

    private function send(?string $email, string $subject, string $body, array $context): void
    {
        if (! config('communications.email_enabled') || ! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        if (in_array(config('mail.default'), ['log', 'array', 'failover'], true)) {
            Log::warning('Earthquick email skipped: configure a real mail transport before enabling notifications', $context);
            return;
        }

        app(OutboundMailService::class)->queueAndSend($email, $subject, $body, $context);
    }
}
