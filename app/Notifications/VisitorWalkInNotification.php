<?php

namespace App\Notifications;

use App\Models\Visitor;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VisitorWalkInNotification extends Notification
{
    use Queueable;

    public function __construct(public Visitor $visitor) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $apt   = $this->visitor->apartment;
        $block = $apt?->floor?->block;
        $msg   = sprintf(
            'Bảo vệ vừa tạo đăng ký cho khách "%s" đến thăm căn hộ %s%s. Vui lòng xác nhận hoặc từ chối.',
            $this->visitor->guest_name,
            $apt?->apartment_number ?? '—',
            $block ? ' (' . $block->name . ')' : ''
        );

        return [
            'type'         => 'visitor_walk_in',
            'title'        => 'Xác nhận khách ghé thăm',
            'message'      => $msg,
            'body'         => $msg,
            'visitor_id'   => $this->visitor->id,
            'guest_name'   => $this->visitor->guest_name,
            'guest_phone'  => $this->visitor->guest_phone,
            'face_image'   => $this->visitor->face_image,
            'url'          => route('resident.visitors.show', $this->visitor->id),
        ];
    }
}
