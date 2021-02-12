<?php

namespace App\Policies;

use App\Models\Store;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StorePolicy
{
    use HandlesAuthorization;

    public function listOwn(User $user, Store $store, int $user_id)
    {
        return $user->id === $user_id OR in_array(strtolower($user->role), ['superadmin', 'admin']);
    }

    public function create(User $user, Store $store, int $user_id)
    {
        return $user->id === $user_id;
    }

    public function update(User $user, Store $store)
    {
        return $user->id === $store->user_id;
    }

    public function transfer(User $user, Store $store)
    {
        return $user->id === $store->user_id;
    }

    public function uploadLogo(User $user, Store $store)
    {
        return $user->id === $store->user_id;
    }
}
