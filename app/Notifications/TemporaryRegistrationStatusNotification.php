<?php

namespace App\Notifications;

use App\Models\TemporaryRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TemporaryRegistrationStatusNotification extends Notification
{
    use Queueable;

    public function __construct(protected TemporaryRegistration $registration) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $statusMap = [
            'approved' => 'đã được phê duyệt',
            'rejected' => 'đã bị từ chối',
        ];

        $statusText = $statusMap[$this->registration->status] ?? 'đã được cập nhật trạng thái';
        $typeText = $this->registration->type === 'residence' ? 'Tạm trú' : 'Tạm vắng';

        $message = "Đơn đăng ký {$typeText} của bạn {$statusText}.";
        
        if ($this->registration->status === 'rejected' && $this->registration->rejection_reason) {
            $message .= " Lý do: " . $this->registration->rejection_reason;
        }

        return [
            'type'       => 'temporary_registration_status',
            'registration_id' => $this->registration->id,
            'title'      => "Cập nhật Đơn {$typeText}",
            'message'    => $message,
            'url'        => route('resident.temporary-registrations.show', $this->registration->id),
        ];
    }
}
