<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Voucher;
use Illuminate\Auth\Access\HandlesAuthorization;

class VoucherPolicy
{
    use HandlesAuthorization;

    public function manage(User $user, Voucher $voucher, int $user_id)
    {
        return $user->id === $user_id;
    }
}
