<?php

namespace App\Notifications;

use App\Models\Order;
use App\Support\BilingualMail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        public Order $order,
        /** @var 'paid'|'canceled' */
        public string $status
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $order = $this->order->loadMissing(['items.Product']);
        $orderNumber = $order->userOrderNumber();
        $total = format_price($order->total_price);
        $name = $notifiable->name ?? '';

        if ($this->status === 'paid') {
            $mail = (new MailMessage)
                ->subject("Order #{$orderNumber} — Payment confirmed | الطلب رقم {$orderNumber} — تم تأكيد الدفع");

            BilingualMail::greeting($mail, $name);

            BilingualMail::line(
                $mail,
                "تم تحديث حالة طلبك رقم {$orderNumber} إلى مدفوع.",
                "Your order #{$orderNumber} has been marked as paid."
            );

            BilingualMail::line(
                $mail,
                "إجمالي الطلب: {$total}",
                "Order total: {$total}"
            );

            foreach ($order->items as $item) {
                $product = $item->Product ?? null;
                $productName = $product?->name ?? 'Product';
                $mail->line("• {$productName} × {$item->quantity}");
            }

            BilingualMail::line(
                $mail,
                'سنقوم بتجهيز طلبك للشحن قريباً.',
                'We will prepare your order for shipping soon.'
            );

            return $mail
                ->action('عرض طلباتي | View my orders', url('/orders'))
                ->salutation(BilingualMail::salutation());
        }

        $mail = (new MailMessage)
            ->subject("Order #{$orderNumber} — Canceled | الطلب رقم {$orderNumber} — تم الإلغاء");

        BilingualMail::greeting($mail, $name);

        BilingualMail::line(
            $mail,
            "تم إلغاء طلبك رقم {$orderNumber}.",
            "Your order #{$orderNumber} has been canceled."
        );

        BilingualMail::line(
            $mail,
            'إذا لم تطلب ذلك أو لديك استفسار، يرجى التواصل مع الدعم.',
            'If you did not request this or have questions, please contact support.'
        );

        return $mail
            ->action('عرض طلباتي | View my orders', url('/orders'))
            ->salutation(BilingualMail::salutation());
    }
}
