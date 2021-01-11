<?php

namespace App\Http\Controllers\Profile\User;

use App\Http\Controllers\DatabaseController;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends DatabaseController
{
    protected $profile = 'users.profile.index';

    protected $user;

    public function __construct(Request $request)
    {
        $this->profile = view($this->profile);

        $user = User::query()
            ->where('id', $request->route('id'))
            ->first();

        if ($user === null) {
            abort(404);
        }

        $this->user = $user;
        $this->profile->with('user', $user);
    }
}
