<?php

namespace App\Notifications;

use App\Support\BilingualMail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends Notification
{
    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        $name = $notifiable->name ?? '';

        $mail = (new MailMessage)
            ->subject('Verify your email | تأكيد بريدك الإلكتروني — '.BilingualMail::storeName());

        BilingualMail::greeting($mail, $name);

        BilingualMail::line(
            $mail,
            'شكراً لتسجيلك! يرجى تأكيد بريدك الإلكتروني بالضغط على الزر أدناه.',
            'Thank you for registering! Please verify your email by clicking the button below.'
        );

        return $mail
            ->action('تأكيد البريد | Verify email', $verificationUrl)
            ->line('إذا لم تقم بإنشاء حساب، يمكنك تجاهل هذه الرسالة.')
            ->line('If you did not create an account, no further action is required.')
            ->salutation(BilingualMail::salutation());
    }

    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(10),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
