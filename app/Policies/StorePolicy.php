<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StorePolicy
{
    use HandlesAuthorization;

    public function viewUserStores(User $user, Store $store, int $user_id)
    {
        return $user->id === $user_id OR in_array(strtolower($user->role), ['superadmin', 'admin']);
    }

    public function addStore(User $user, Store $store, int $user_id)
    {
        return $user->id === $user_id;
    }

    public function editStore(User $user, Store $store)
    {
        return $user->id === $store->user_id;
    }

    public function transferStore(User $user, Store $store)
    {
        return $user->id === $store->user_id;
    }

    public function uploadLogo(User $user, Store $store)
    {
        return $user->id === $store->user_id;
    }

    public function addProduct(User $user, Store $store)
    {
        return $user->id === $store->user_id;
    }

    public function editProduct(User $user, Store $store)
    {
        return $user->id === $store->user_id;
    }
}
