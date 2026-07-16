<?php

namespace App\Notifications;

use App\Models\Visitor;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VisitorApprovalNotification extends Notification
{
    use Queueable;

    public function __construct(protected Visitor $visitor) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type'       => 'visitor_approval',
            'visitor_id' => $this->visitor->id,
            'title'      => 'Khách muốn ghé thăm',
            'message'    => $this->visitor->guest_name . ' đang ở cổng, muốn gặp bạn. Đồng ý cho vào?',
            'url'        => route('resident.visitors.index'),
        ];
    }
}
