<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\TemporaryRegistration;

class TemporaryRegistrationEndedEarlyNotification extends Notification implements ShouldQueue
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
        $apartment = $this->temporaryRegistration->apartment->apartment_number ?? 'N/A';
        $url = route('admin.temporary-registrations.edit', $this->temporaryRegistration->id);
        
        return (new MailMessage)
                    ->subject("Thông báo kết thúc sớm $type - Căn hộ $apartment")
                    ->greeting("Chào Ban quản lý,")
                    ->line("Căn hộ $apartment vừa báo cáo kết thúc sớm đăng ký $type.")
                    ->line("Loại: $type")
                    ->line("Ngày kết thúc mới: " . now()->format('d/m/Y'))
                    ->action('Xem chi tiết', $url)
                    ->line('Vui lòng kiểm tra và xác nhận trên hệ thống nếu cần.');
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
            'title' => "Báo cáo kết thúc sớm $type",
            'message' => "Căn hộ {$this->temporaryRegistration->apartment->apartment_number} vừa báo cáo kết thúc sớm đăng ký $type.",
            'type' => 'temporary_registration_ended_early',
        ];
    }
}
