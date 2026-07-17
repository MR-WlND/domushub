<?php

namespace App\Events;

use App\Models\Post;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $post;

    /**
     * Create a new event instance.
     */
    public function __construct(Post $post)
    {
        // Load relationships needed to render the card
        $this->post = $post->load(['user.apartment', 'images']);
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('community-feed'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
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
            'user' => [
                'id' => $this->post->user->id,
                'name' => $this->post->user->name,
                'avatar' => $this->post->user->avatar,
                'apartment' => $this->post->user->apartment ? [
                    'apartment_number' => $this->post->user->apartment->apartment_number,
                ] : null,
            ],
            'images' => $this->post->images->map(function ($image) {
                return [
                    'image_path' => $image->image_path,
                ];
            })->toArray(),
            'comments_count' => 0,
        ];
    }
}
