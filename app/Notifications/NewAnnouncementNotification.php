<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewAnnouncementNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    protected $announcement;

    /**
     * Create a new notification instance.
     */
    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        $categoryName = match ($this->announcement->category) {
            'maintenance' => 'Bảo trì kĩ thuật',
            'warning' => 'Cảnh báo khẩn cấp',
            'event' => 'Sự kiện',
            default => 'Tin chung',
        };

        return [
            'title' => '[' . $categoryName . '] ' . $this->announcement->title,
            'message' => Str::limit(strip_tags($this->announcement->content), 80),
            'url' => route('resident.announcements.show', $this->announcement->id),
        ];
    }
}
