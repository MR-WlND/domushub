<?php

namespace App\Events;

use App\Models\Post;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $post;

    public function __construct(Post $post)
    {
        $this->post = $post->load(['user.apartment', 'images']);
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('community-feed'),
            new Channel('post.' . $this->post->id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->post->id,
            'title' => $this->post->title,
            'content' => $this->post->content,
            'price' => $this->post->price,
            'status' => $this->post->status,
            'created_at' => $this->post->created_at->toIso8601String(),
            'created_at_human' => $this->post->created_at->diffForHumans(),
        ];
    }
}
