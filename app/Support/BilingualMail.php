<?php

namespace App\Support;

use Illuminate\Notifications\Messages\MailMessage;

class BilingualMail
{
    public static function storeName(): string
    {
        return (string) config('mail.from.name', 'إلكترو');
    }

    public static function salutation(): string
    {
        return 'مع التحية، فريق '.self::storeName().' | Regards, '.self::storeName().' Team';
    }

    public static function line(MailMessage $mail, string $arabic, string $english): MailMessage
    {
        return $mail->line($arabic)->line($english);
    }

    public static function greeting(MailMessage $mail, string $name): MailMessage
    {
        $fallbackAr = 'عزيزي العميل';
        $fallbackEn = 'Customer';

        $displayName = $name ?: null;

        return self::line(
            $mail,
            'مرحباً '.($displayName ?: $fallbackAr).'،',
            'Hello '.($displayName ?: $fallbackEn).','
        );
    }
}
