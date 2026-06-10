<?php

namespace App\Events;

use App\Models\Comment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('post.' . $this->comment->post_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->comment->id,
            'post_id' => $this->comment->post_id,
            'content' => $this->comment->content,
        ];
    }
}
