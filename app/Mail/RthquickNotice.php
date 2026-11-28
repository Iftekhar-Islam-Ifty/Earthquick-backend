<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RthquickNotice extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $noticeSubject, public string $noticeBody, public ?string $noticeEvent = null)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->noticeSubject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.notice');
    }
}
