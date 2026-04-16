<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ad extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'description',
        'price',
        'currency',
        'city',
        'area',
        'condition',
        'status',
        'is_featured',
        'views_count',
        'contact_phone',
        'whatsapp_number',
        'featured_until',
        'approved_by',
        'approved_at',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_featured' => 'boolean',
            'views_count' => 'integer',
            'featured_until' => 'datetime',
            'approved_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(Image::class)->orderByDesc('is_primary')->orderBy('sort_order');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query
            ->where('is_featured', true)
            ->where(function (Builder $inner): void {
                $inner
                    ->whereNull('featured_until')
                    ->orWhere('featured_until', '>=', now());
            });
    }

    public function canBeViewedBy(?User $user): bool
    {
        if ($this->status === 'approved') {
            return true;
        }

        if ($user === null) {
            return false;
        }

        return $user->id === $this->user_id || $user->isAdmin();
    }
}
