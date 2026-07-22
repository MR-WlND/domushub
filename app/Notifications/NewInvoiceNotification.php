<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
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
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $month = $this->invoice->billing_month;
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
