<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class PostCommentedNotification extends Notification
{
    use Queueable;

    protected $comment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
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
        $sender = $this->comment->user;
        $senderName = $sender->apartment ? 'Căn hộ ' . $sender->apartment->apartment_number : $sender->name;
        $contentPreview = Str::limit($this->comment->content, 40);

        return [
            'title' => 'Bình luận mới',
            'message' => "{$senderName} đã bình luận về bài viết của bạn: \"{$contentPreview}\"",
            'url' => route('resident.posts.show', $this->comment->post_id) . '#comment-' . $this->comment->id,
            'sender_id' => $this->comment->user_id,
            'comment_id' => $this->comment->id,
            'post_id' => $this->comment->post_id,
        ];
    }
}
