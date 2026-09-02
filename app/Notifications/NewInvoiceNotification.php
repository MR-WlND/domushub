<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Invoice;

class NewInvoiceNotification extends Notification
{
    use Queueable;

    protected Invoice $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function via($notifiable): array
    {
        // Gửi qua cả database (quả chuông) và email
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        // $this->invoice->billing_month là một object Carbon do có Accessor
        $month = $this->invoice->billing_month->format('m');
        $year  = $this->invoice->billing_year;
        $total = number_format($this->invoice->total_amount, 0, ',', '.');
        $dueDate = $this->invoice->due_date ? $this->invoice->due_date->format('d/m/Y') : 'N/A';
        $url = url('/resident/invoices/' . $this->invoice->id);

        return (new MailMessage)
            ->subject("Thông báo hóa đơn tháng {$month}/{$year}")
            ->greeting("Chào bạn,")
            ->line("Hệ thống DomusHub xin thông báo hóa đơn tháng {$month}/{$year} của bạn đã được phát hành.")
            ->line("**Tổng tiền thanh toán:** {$total} VNĐ")
            ->line("**Hạn thanh toán:** {$dueDate}")
            ->action('Xem chi tiết và Thanh toán', $url)
            ->line('Vui lòng thanh toán đúng hạn để tránh các phiền phức không đáng có. Cảm ơn bạn đã hợp tác!');
    }

    public function toArray($notifiable): array
    {
        $month = $this->invoice->billing_month->format('m');
        $year  = $this->invoice->billing_year;
        $total = number_format($this->invoice->total_amount, 0, ',', '.');

        return [
            'type'       => 'new_invoice',
            'title'      => "Hóa đơn tháng {$month}/{$year} đã được phát hành",
            'body'       => "Hóa đơn của bạn tháng {$month}/{$year} đã được tạo. Tổng tiền: {$total}đ. Vui lòng thanh toán trước hạn.",
            'invoice_id' => $this->invoice->id,
            'due_date'   => $this->invoice->due_date?->format('d/m/Y'),
            'url'        => '/resident/invoices/' . $this->invoice->id,
        ];
    }
}
