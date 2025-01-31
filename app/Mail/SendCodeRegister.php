<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendCodeRegister extends Mailable
{
    use Queueable, SerializesModels;
    protected string $code;
    protected $accounts;

    /**
     * Create a new message instance.
     */
    public function __construct($code, $accounts)
    {
        $this->code = $code;
        $this->accounts = $accounts;

        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Send Code Verify Account',
        );
    }

    public function build()
    {
        return $this->with([
            'code' => $this->code,
            'name' => $this->accounts->name,
        ])->markdown('cms.Emails.sendCodeRegister');
    }
}
