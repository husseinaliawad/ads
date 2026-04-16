<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ad_id' => $this->ad_id,
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'body' => $this->body,
            'read_at' => $this->read_at,
            'created_at' => $this->created_at,
            'sender' => $this->whenLoaded('sender', fn (): array => [
                'id' => $this->sender->id,
                'name' => $this->sender->name,
            ]),
            'receiver' => $this->whenLoaded('receiver', fn (): array => [
                'id' => $this->receiver->id,
                'name' => $this->receiver->name,
            ]),
            'ad' => $this->whenLoaded('ad', fn (): ?array => $this->ad ? [
                'id' => $this->ad->id,
                'title' => $this->ad->title,
                'slug' => $this->ad->slug,
            ] : null),
        ];
    }
}
