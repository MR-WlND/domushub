<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class PostSharedNotification extends Notification
{
    use Queueable;

    protected $post;
    protected $sender;
    protected $customMessage;

    /**
     * Create a new notification instance.
     */
    public function __construct(Post $post, User $sender, ?string $customMessage = null)
    {
        $this->post = $post;
        $this->sender = $sender;
        $this->customMessage = $customMessage;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        $senderName = $this->sender->apartment 
            ? 'Căn hộ ' . $this->sender->apartment->apartment_number 
            : $this->sender->name;
            
        $postTitlePreview = Str::limit($this->post->title ?: $this->post->content, 30);
        
        $msg = "{$senderName} đã chia sẻ một bài viết với bạn: \"{$postTitlePreview}\"";
        if ($this->customMessage) {
            $msg = "{$senderName} đã chia sẻ với bạn: \"{$this->customMessage}\"";
        }

        return [
            'title' => 'Bài viết được chia sẻ',
            'message' => $msg,
            'url' => route('resident.posts.show', $this->post->id),
            'sender_id' => $this->sender->id,
            'post_id' => $this->post->id,
        ];
    }
}
