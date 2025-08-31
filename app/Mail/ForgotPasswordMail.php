<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $email;

    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    public function build()
    {
        $resetUrl = url('/reset-password?token=' . $this->token . '&email=' . urlencode($this->email));

        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('Reset Your Password')
                    ->view('emails.forgot-password')
                    ->with([
                        'resetUrl' => $resetUrl,
                    ]);
    }
}
