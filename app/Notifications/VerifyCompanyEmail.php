<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyCompanyEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = route('companies.verification.verify', [
            'id' => $notifiable->id,
        ]);

        return (new MailMessage)
            ->subject('Verify Company Email')
            ->line('Thanks for registering your company.')
            ->line('Please verify your email address to activate your company account.')
            ->action('Verify Company Email', $verificationUrl)
            ->line('If you did not create this company account, no further action is required.');
    }
}
