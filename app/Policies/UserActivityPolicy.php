<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserActivityPolicy
{
    use HandlesAuthorization;

    public function viewActivities(User $user, UserActivity $userActivity, int $user_id)
    {
        return $user->id === $user_id  OR in_array(strtolower($user->role), ['superadmin', 'admin']);
    }
}
