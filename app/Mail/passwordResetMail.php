<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class passwordResetMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $resetLink;

    /**
     * Create a new message instance.
     *
     * @param string $resetLink
     */
    public function __construct(string $resetLink)
    {
        $this->resetLink = $resetLink;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Define the content of the email without a view template
        return $this->subject('Password Reset Request')
            ->html($this->buildEmailContent());
    }

    /**
     * Build the email content with an action button.
     *
     * @return string
     */
    private function buildEmailContent()
    {
        return "
            <h1>Password Reset Request</h1>
            <p>You requested a password reset. Click the button below to reset your password:</p>
            <a href='{$this->resetLink}' style='display: inline-block; padding: 10px 20px; color: white; background-color: #3490dc; text-decoration: none; border-radius: 5px;'>Reset Password</a>
            <p>If you did not request this reset, please ignore this email.</p>
            <br>
            <p>Thanks,<br>" . config('app.name') . "</p>
        ";
    }
}
