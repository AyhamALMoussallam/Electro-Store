<?php

namespace App\Notifications;

use App\Models\Order;
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
        $order = $this->order;
        $orderId = $order->id;
        $total = format_price($order->total_price);
        $name = $notifiable->name ?? 'عزيزي العميل';

        if ($this->status === 'paid') {
            $mail = (new MailMessage)
                ->subject("الطلب #{$orderId} — تم تأكيد الدفع")
                ->greeting("مرحباً {$name}،")
                ->line("تم تحديث حالة طلبك رقم #{$orderId} إلى مدفوع.")
                ->line("إجمالي الطلب: {$total}");

            foreach ($order->items as $item) {
                $product = $item->Product ?? $item->product ?? null;
                $productName = $product?->name ?? 'منتج';
                $mail->line("• {$productName} × {$item->quantity}");
            }

            return $mail
                ->line('سنقوم بتجهيز طلبك للشحن قريباً.')
                ->action('عرض طلباتي', url('/orders'))
                ->salutation('مع التحية، فريق إلكترو');
        }

        return (new MailMessage)
            ->subject("الطلب #{$orderId} — تم الإلغاء")
            ->greeting("مرحباً {$name}،")
            ->line("تم إلغاء طلبك رقم #{$orderId}.")
            ->line('إذا لم تطلب ذلك أو لديك استفسار، يرجى التواصل مع الدعم.')
            ->action('عرض طلباتي', url('/orders'))
            ->salutation('مع التحية، فريق إلكترو');
    }
}
