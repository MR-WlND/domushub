<?php

namespace App\Notifications;

use App\Models\Parcel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ParcelNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public function __construct(protected Parcel $parcel, protected string $action) {}

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable): array
    {
        if ($this->action === 'arrived') {
            return [
                'type'      => 'parcel_arrived',
                'parcel_id' => $this->parcel->id,
                'title'     => 'Bưu phẩm mới đã đến',
                'message'   => 'Bạn có bưu phẩm từ "' . $this->parcel->sender_name . '" đã đến sảnh lễ tân.',
                'url'       => route('resident.dashboard'),
            ];
        }

        if ($this->action === 'received') {
            return [
                'type'      => 'parcel_received',
                'parcel_id' => $this->parcel->id,
                'title'     => 'Đã nhận bưu phẩm',
                'message'   => 'Bưu phẩm từ "' . $this->parcel->sender_name . '" đã được ký nhận thành công.',
                'url'       => route('resident.dashboard'),
            ];
        }

        return [
            'type'      => 'parcel_status',
            'parcel_id' => $this->parcel->id,
            'title'     => 'Cập nhật bưu phẩm',
            'message'   => 'Trạng thái bưu phẩm từ "' . $this->parcel->sender_name . '": ' . $this->parcel->status_label,
            'url'       => route('resident.dashboard'),
        ];
    }
}
