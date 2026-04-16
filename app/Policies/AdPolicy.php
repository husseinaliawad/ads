<?php

namespace App\Policies;

use App\Models\Ad;
use App\Models\User;

class AdPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    public function view(User $user, Ad $ad): bool
    {
        return $ad->status === 'approved' || $ad->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->is_active;
    }

    public function update(User $user, Ad $ad): bool
    {
        return $ad->user_id === $user->id;
    }

    public function delete(User $user, Ad $ad): bool
    {
        return $ad->user_id === $user->id;
    }

    public function restore(User $user, Ad $ad): bool
    {
        return $ad->user_id === $user->id;
    }

    public function forceDelete(User $user, Ad $ad): bool
    {
        return $ad->user_id === $user->id;
    }
}
