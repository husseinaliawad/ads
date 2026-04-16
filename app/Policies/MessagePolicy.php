<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;

class MessagePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    public function view(User $user, Message $message): bool
    {
        return $message->sender_id === $user->id || $message->receiver_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->is_active;
    }

    public function update(User $user, Message $message): bool
    {
        return false;
    }

    public function delete(User $user, Message $message): bool
    {
        return $message->sender_id === $user->id;
    }

    public function restore(User $user, Message $message): bool
    {
        return false;
    }

    public function forceDelete(User $user, Message $message): bool
    {
        return false;
    }
}
