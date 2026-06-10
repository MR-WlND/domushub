<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LikeToggled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $likeableType;
    public $likeableId;
    public $likesCount;
    public $postId;

    public function __construct(string $likeableType, int $likeableId, int $likesCount, int $postId)
    {
        $this->likeableType = $likeableType;
        $this->likeableId = $likeableId;
        $this->likesCount = $likesCount;
        $this->postId = $postId;
    }

    public function broadcastOn(): array
    {
        $channels = [
            new Channel('post.' . $this->postId),
        ];
        if ($this->likeableType === 'post') {
            $channels[] = new Channel('community-feed');
        }
        return $channels;
    }

    public function broadcastWith(): array
    {
        return [
            'likeable_type' => $this->likeableType,
            'likeable_id' => $this->likeableId,
            'likes_count' => $this->likesCount,
        ];
    }
}
