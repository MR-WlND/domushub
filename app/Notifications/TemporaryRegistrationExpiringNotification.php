<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\TemporaryRegistration;

class TemporaryRegistrationExpiringNotification extends Notification implements ShouldQueue
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
                    ->subject("Nhắc nhở: Đơn $type sắp hết hạn - Phòng $apartment")
                    ->greeting("Chào bạn,")
                    ->line("Hệ thống xin thông báo đơn đăng ký $type cho $guestName tại phòng $apartment sẽ hết hạn vào ngày " . $this->temporaryRegistration->end_date->format('d/m/Y') . ".")
                    ->line("Vui lòng chủ động gia hạn trên hệ thống nếu bạn có nhu cầu tiếp tục.")
                    ->action('Gia hạn ngay', route('resident.temporary-registrations.index'));
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
        $date = $this->temporaryRegistration->end_date->format('d/m/Y');
        
        return [
            'temporary_registration_id' => $this->temporaryRegistration->id,
            'title' => "Đơn $type sắp hết hạn",
            'message' => "Đơn đăng ký $type cho $guestName sẽ hết hạn vào $date. Vui lòng gia hạn nếu cần.",
            'type' => 'temporary_registration',
        ];
    }
}
