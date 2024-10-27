<?php

namespace App\Notifications;

use App\Mail\passwordResetMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPassword extends Notification implements ShouldQueue
{
    use Queueable;

    private string $resetUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct($resetUrl)
    {
        $this->resetUrl = $resetUrl;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): passwordResetMail
    {
        return (new PasswordResetMail($this->resetUrl))
            ->to($notifiable->email);

//        return (new MailMessage)
//                    ->line('You requested a password reset. Click the button below to reset your password:.')
//                    ->action('Reset Password', $this->resetUrl)
//                    ->line('If you did not request this reset, please ignore this email.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
//        return [
//            'message' => " Requested a password reset.",
//        ];
    }
}
