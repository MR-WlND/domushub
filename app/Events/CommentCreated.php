<?php

namespace App\Events;

use App\Models\Comment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $comment;

    /**
     * Create a new event instance.
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment->load(['user.apartment', 'parent.user']);
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('post.' . $this->comment->post_id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->comment->id,
            'post_id' => $this->comment->post_id,
            'parent_id' => $this->comment->parent_id,
            'content' => $this->comment->content,
            'created_at_human' => $this->comment->created_at->diffForHumans(),
            'user' => [
                'id' => $this->comment->user->id,
                'name' => $this->comment->user->name,
                'avatar' => $this->comment->user->avatar,
                'apartment' => $this->comment->user->apartment ? [
                    'apartment_number' => $this->comment->user->apartment->apartment_number,
                ] : null,
            ],
            'parent' => $this->comment->parent ? [
                'user' => [
                    'name' => $this->comment->parent->user->name ?? 'Cư dân',
                ]
            ] : null,
        ];
    }
}
