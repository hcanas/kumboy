<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    public function manage(User $user, Product $product, int $user_id)
    {
        return $user->id === $user_id;
    }
}
