<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserAddressBook;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserAddressBookPolicy
{
    use HandlesAuthorization;

    public function manage(User $user, UserAddressBook $user_address_book, int $user_id)
    {
        return $user->id === $user_id;
    }

    public function delete(User $user, UserAddressBook $user_address_book)
    {
        return $user->id === $user_address_book->user_id;
    }
}
