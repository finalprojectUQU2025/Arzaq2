<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendCodeVerifiy extends Mailable
{
    use Queueable, SerializesModels;
    protected string $code;
    protected $user;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $code)
    {
        $this->code = $code;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Send Code Verifiy',
        );
    }

    public function build()
    {
        return $this->with([
            'code' => $this->code,
            'name' => $this->user->name,
            'email' => $this->user->email,
        ])->markdown('cms.Emails.SendCodeVerifiy');
    }
}
