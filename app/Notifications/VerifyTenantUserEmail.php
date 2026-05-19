<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyTenantUserEmail extends VerifyEmail
{
    public function __construct(protected bool $emailChanged = false) {}

    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject(__('Verify your email address'))
            ->line(__('Please click the button below to verify your email address.'))
            ->action(__('Verify Email Address'), $verificationUrl)
            ->line($this->emailChanged
                ? __('If you did not request this email change, no further action is required.')
                : __('If you did not create an account, no further action is required.')
            );
    }
}
