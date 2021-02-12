<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function list(User $user, User $target)
    {
        return in_array(strtolower($user->role), ['superadmin', 'admin']);
    }

    public function update(User $user, User $target)
    {
        return $user->id === $target->id;
    }

    public function restrict(User $user, User $target)
    {
        // only ban active users
        // superadmin cannot be banned
        // cannot ban users on same level
        // only superadmin and admins are allowed to ban
        if ($target->banned_until === null
            AND strtolower($target->role) !== 'superadmin'
            AND strtolower($user->role) !== strtolower($target->role)
            AND in_array(strtolower($user->role), ['superadmin', 'admin'])
        ) {
            return true;
        }

        return false;
    }
}
