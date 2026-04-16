<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'currency' => $this->currency,
            'city' => $this->city,
            'area' => $this->area,
            'condition' => $this->condition,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'views_count' => $this->views_count,
            'contact_phone' => $this->contact_phone,
            'whatsapp_number' => $this->whatsapp_number,
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'category' => $this->whenLoaded('category', fn (): array => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ]),
            'user' => $this->whenLoaded('user', fn (): array => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'city' => $this->user->city,
                'phone' => $this->user->phone,
            ]),
            'primary_image' => $this->whenLoaded('primaryImage', fn (): ?array => $this->primaryImage ? [
                'id' => $this->primaryImage->id,
                'path' => $this->primaryImage->path,
                'url' => $this->primaryImage->url,
                'alt_text' => $this->primaryImage->alt_text,
            ] : null),
            'images' => $this->whenLoaded('images', fn () => $this->images->map(fn ($image): array => [
                'id' => $image->id,
                'path' => $image->path,
                'url' => $image->url,
                'alt_text' => $image->alt_text,
                'sort_order' => $image->sort_order,
                'is_primary' => $image->is_primary,
            ])),
        ];
    }
}
