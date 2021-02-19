<?php

namespace App\Policies;

use App\Models\User;
use App\Models\StoreRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class StoreRequestPolicy
{
    use HandlesAuthorization;

    public function list(User $user, StoreRequest $store_request)
    {
        return in_array(strtolower($user->role), ['superadmin', 'admin']);
    }

    public function listOwn(User $user, StoreRequest $store_request, int $user_id)
    {
        return $user->id === $user_id OR in_array(strtolower($user->role), ['superadmin', 'admin']);
    }

    public function view(User $user, StoreRequest $store_request)
    {
        return $user->id === $store_request->user_id
            OR ($store_request->category === 'store_transfer' AND $user->id === $store_request->storeTransfer()->first()->target_id)
            OR in_array(strtolower($user->role), ['superadmin', 'admin']);
    }

    public function cancel(User $user, StoreRequest $store_request)
    {
        return $user->id === $store_request->user_id;
    }

    public function evaluate(User $user, StoreRequest $store_request)
    {
        return in_array(strtolower($user->role), ['superadmin', 'admin']);
    }

    public function countPendingRequests(User $user, StoreRequest $store_request)
    {
        return in_array(strtolower($user->role), ['superadmin', 'admin']);
    }
}
