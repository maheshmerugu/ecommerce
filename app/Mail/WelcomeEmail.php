<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Customer $customer)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('mail.from.address'),
                config('mail.from.name')
            ),
            subject: 'Welcome to ' . config('app.name') . '!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.customer.welcome',
            with: [
                'customer' => $this->customer,
                'appName'  => config('app.name'),
                'shopUrl'  => config('app.url'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
