<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'ad_id',
        'path',
        'alt_text',
        'sort_order',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_primary' => 'boolean',
        ];
    }

    protected $appends = ['url'];

    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }

    public function getUrlAttribute(): string
    {
        $path = (string) $this->path;

        if ($path === '') {
            return $this->fallbackImageUrl();
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Storage::disk('public')->exists($path)) {
            return asset('storage/'.$path);
        }

        return $this->fallbackImageUrl();
    }

    private function fallbackImageUrl(): string
    {
        $seed = 'ad-'.($this->ad_id ?? 0).'-image-'.($this->id ?? 0);

        return "https://picsum.photos/seed/{$seed}/1200/800";
    }
}
