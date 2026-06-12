<?php

namespace App\Notifications;

use App\Models\Like;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReactionNotification extends Notification
{
    use Queueable;

    protected $like;
    protected $likeable;
    protected $type; // 'post' or 'comment'

    /**
     * Create a new notification instance.
     */
    public function __construct(Like $like, $likeable, string $type)
    {
        $this->like = $like;
        $this->likeable = $likeable;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the reaction label in Vietnamese.
     */
    protected function getReactionLabel(string $type): string
    {
        return match($type) {
            'love' => 'yêu thích',
            'haha' => 'haha',
            'wow' => 'ngạc nhiên',
            'sad' => 'buồn',
            'angry' => 'phẫn nộ',
            default => 'thích',
        };
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        $sender = $this->like->user;
        $senderName = $sender->apartment ? 'Căn hộ ' . $sender->apartment->apartment_number : $sender->name;
        $reactionLabel = $this->getReactionLabel($this->like->type ?? 'like');
        $targetLabel = $this->type === 'post' ? 'bài viết' : 'bình luận';
        
        $postId = $this->type === 'post' ? $this->likeable->id : $this->likeable->post_id;
        $url = route('resident.posts.show', $postId);
        if ($this->type === 'comment') {
            $url .= '#comment-' . $this->likeable->id;
        }

        return [
            'title' => 'Tương tác mới',
            'message' => "{$senderName} đã bày tỏ cảm xúc {$reactionLabel} về {$targetLabel} của bạn.",
            'url' => $url,
            'sender_id' => $this->like->user_id,
            'likeable_id' => $this->like->likeable_id,
            'likeable_type' => $this->like->likeable_type,
            'reaction_type' => $this->like->type ?? 'like',
        ];
    }
}
