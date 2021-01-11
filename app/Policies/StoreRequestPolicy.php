<?php

namespace App\Policies;

use App\Models\User;
use App\Models\StoreRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class StoreRequestPolicy
{
    use HandlesAuthorization;

    public function viewAllRequests(User $user, StoreRequest $store_request)
    {
        return in_array(strtolower($user->role), ['superadmin', 'admin']);
    }

    public function viewStoreRequests(User $user, StoreRequest $store_request, int $user_id)
    {
        return $user->id === $user_id OR in_array(strtolower($user->role), ['superadmin', 'admin']);
    }

    public function viewRequestDetails(User $user, StoreRequest $store_request)
    {
        return $user->id === $store_request->user_id OR in_array(strtolower($user->role), ['superadmin', 'admin']);
    }

    public function cancelRequest(User $user, StoreRequest $store_request)
    {
        return $user->id === $store_request->user_id;
    }

    public function approveRequest(User $user, StoreRequest $store_request)
    {
        return in_array(strtolower($user->role), ['superadmin', 'admin']);
    }

    public function rejectRequest(User $user, StoreRequest $store_request)
    {
        return in_array(strtolower($user->role), ['superadmin', 'admin']);
    }

    public function countPendingRequests(User $user, StoreRequest $store_request)
    {
        return in_array(strtolower($user->role), ['superadmin', 'admin']);
    }

    public function addStoreApplication(User $user, StoreRequest $store_request, int $user_id)
    {
        return $user->id === $user_id;
    }
}
