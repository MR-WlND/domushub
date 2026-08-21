<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\TemporaryRegistration;

class NewTemporaryRegistrationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $temporaryRegistration;

    /**
     * Create a new notification instance.
     */
    public function __construct(TemporaryRegistration $temporaryRegistration)
    {
        $this->temporaryRegistration = $temporaryRegistration;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $type = $this->temporaryRegistration->type === 'residence' ? 'Tạm trú' : 'Tạm vắng';
        $apartment = $this->temporaryRegistration->apartment->name ?? '';
        $url = route('admin.temporary-registrations.edit', $this->temporaryRegistration->id);
        
        return (new MailMessage)
                    ->subject("Yêu cầu đăng ký $type mới - Phòng $apartment")
                    ->greeting("Chào Ban quản lý,")
                    ->line("Có một yêu cầu đăng ký $type mới từ phòng $apartment cần được phê duyệt.")
                    ->line("Loại: $type")
                    ->line("Ngày bắt đầu: " . $this->temporaryRegistration->start_date->format('d/m/Y'))
                    ->action('Xem chi tiết', $url)
                    ->line('Vui lòng kiểm tra và xử lý trên hệ thống.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $type = $this->temporaryRegistration->type === 'residence' ? 'Tạm trú' : 'Tạm vắng';
        return [
            'temporary_registration_id' => $this->temporaryRegistration->id,
            'title' => "Yêu cầu đăng ký $type mới",
            'message' => "Phòng {$this->temporaryRegistration->apartment->name} vừa gửi yêu cầu đăng ký $type.",
            'type' => 'temporary_registration',
        ];
    }
}
