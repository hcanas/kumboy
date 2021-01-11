<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserAddressBook;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserAddressBookPolicy
{
    use HandlesAuthorization;

    public function viewAddressBook(User $user, UserAddressBook $user_address_book, int $user_id)
    {
        return $user->id === $user_id OR in_array(strtolower($user->role), ['superadmin', 'admin']);
    }

    public function addAddress(User $user, UserAddressBook $user_address_book, int $user_id)
    {
        return $user->id === $user_id;
    }

    public function editAddress(User $user, UserAddressBook $user_address_book)
    {
        return $user->id === $user_address_book->user_id;
    }

    public function deleteAddress(User $user, UserAddressBook $user_address_book)
    {
        return $user->id === $user_address_book->user_id;
    }
}
