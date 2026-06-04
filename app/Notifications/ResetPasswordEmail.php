<?php

namespace App\Notifications;

use App\Support\BilingualMail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordEmail extends Notification
{
    public function __construct(#[\SensitiveParameter] public string $token) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $resetUrl = url('/reset-password?'.http_build_query([
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]));

        $expireMinutes = config('auth.passwords.users.expire', 2);
        $name = $notifiable->name ?? '';

        $mail = (new MailMessage)
            ->subject('Reset your password | إعادة تعيين كلمة المرور — '.BilingualMail::storeName());

        BilingualMail::greeting($mail, $name);

        BilingualMail::line(
            $mail,
            'لقد طلبت إعادة تعيين كلمة المرور. اضغط الزر أدناه لتعيين كلمة مرور جديدة.',
            'You requested a password reset. Click the button below to set a new password.'
        );

        return $mail
            ->action('إعادة التعيين | Reset password', $resetUrl)
            ->line("ينتهي صلاحية هذا الرابط خلال {$expireMinutes} دقيقة.")
            ->line("This link will expire in {$expireMinutes} minutes.")
            ->line('إذا لم تطلب إعادة تعيين كلمة المرور، يمكنك تجاهل هذه الرسالة.')
            ->line('If you did not request a password reset, you can ignore this email.')
            ->salutation(BilingualMail::salutation());
    }
}
