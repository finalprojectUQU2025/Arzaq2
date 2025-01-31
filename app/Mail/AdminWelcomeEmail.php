<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminWelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $email;
    public $name;
    public $password;

    /**
     * Create a new message instance.
     */
    public function __construct($email, $name, $password)
    {
        $this->email = $email;
        $this->name = $name;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(config('app.name') . " - بينات تسجيل الدخول")
            ->markdown('cms.Emails.welcome_email');
    }
}
