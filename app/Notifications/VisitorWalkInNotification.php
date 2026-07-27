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

        return [
            'type'         => 'visitor_walk_in',
            'title'        => 'Khách đến thăm',
            'body'         => sprintf(
                'Bảo vệ đã đăng ký khách "%s" vào thăm căn hộ %s%s.',
                $this->visitor->guest_name,
                $apt?->apartment_number ?? '—',
                $block ? ' (' . $block->name . ')' : ''
            ),
            'visitor_id'   => $this->visitor->id,
            'guest_name'   => $this->visitor->guest_name,
            'guest_phone'  => $this->visitor->guest_phone,
            'check_in_at'  => $this->visitor->check_in_at?->format('H:i d/m/Y'),
            'face_image'   => $this->visitor->face_image,
        ];
    }
}