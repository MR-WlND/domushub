<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\TemporaryRegistration;

class TemporaryRegistrationExpiredNotification extends Notification implements ShouldQueue
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
        $apartment = $this->temporaryRegistration->apartment->apartment_number ?? '';
        $guestName = $this->temporaryRegistration->type === 'residence' ? $this->temporaryRegistration->guest_name : $this->temporaryRegistration->user->name;
        
        return (new MailMessage)
                    ->subject("Thông báo hết hạn $type - Phòng $apartment")
                    ->greeting("Chào bạn,")
                    ->line("Đơn đăng ký $type cho $guestName tại phòng $apartment đã hết hạn vào ngày " . $this->temporaryRegistration->end_date->format('d/m/Y') . ".")
                    ->line("Trạng thái nhân khẩu trên hệ thống đã được cập nhật tự động.")
                    ->line("Nếu bạn có nhu cầu tiếp tục $type, vui lòng tạo đơn gia hạn mới trên hệ thống.")
                    ->action('Truy cập hệ thống', route('resident.dashboard'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $type = $this->temporaryRegistration->type === 'residence' ? 'Tạm trú' : 'Tạm vắng';
        $guestName = $this->temporaryRegistration->type === 'residence' ? $this->temporaryRegistration->guest_name : $this->temporaryRegistration->user->name;
        
        return [
            'temporary_registration_id' => $this->temporaryRegistration->id,
            'title' => "Đơn $type đã hết hạn",
            'message' => "Đơn đăng ký $type cho $guestName đã hết hạn. Vui lòng gia hạn nếu cần.",
            'type' => 'temporary_registration',
        ];
    }
}
