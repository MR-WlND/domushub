<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class CommentRepliedNotification extends Notification
{
    use Queueable;

    protected $reply;

    /**
     * Create a new notification instance.
     */
    public function __construct(Comment $reply)
    {
        $this->reply = $reply;
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
        $sender = $this->reply->user;
        $senderName = $sender->apartment ? 'Căn hộ ' . $sender->apartment->apartment_number : $sender->name;
        $contentPreview = Str::limit($this->reply->content, 40);

        return [
            'title' => 'Phản hồi bình luận',
            'message' => "{$senderName} đã trả lời bình luận của bạn: \"{$contentPreview}\"",
            'url' => route('resident.posts.show', $this->reply->post_id) . '#comment-' . $this->reply->id,
            'sender_id' => $this->reply->user_id,
            'comment_id' => $this->reply->id,
            'parent_id' => $this->reply->parent_id,
            'post_id' => $this->reply->post_id,
        ];
    }
}
