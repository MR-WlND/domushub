<?php

namespace App\Mail;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccusationNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Ticket $ticket;
    public User $accusedUser;

    public function __construct(Ticket $ticket, User $accusedUser)
    {
        $this->ticket = $ticket;
        $this->accusedUser = $accusedUser;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[DomusHub] Bạn có thông báo tố cáo cần phản hồi',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.accusation-notification',
        );
    }
}
