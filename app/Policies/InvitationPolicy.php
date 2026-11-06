<?php

namespace App\Policies;

use App\Models\Invitation;
use App\Models\User;

// Every dashboard route on an invitation goes through this; everything under an invitation inherits it.
class InvitationPolicy
{
    public function before(User $user): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function view(User $user, Invitation $invitation): bool
    {
        return $invitation->user_id === $user->id;
    }

    public function update(User $user, Invitation $invitation): bool
    {
        return $invitation->user_id === $user->id;
    }

    public function delete(User $user, Invitation $invitation): bool
    {
        return $invitation->user_id === $user->id;
    }
}
