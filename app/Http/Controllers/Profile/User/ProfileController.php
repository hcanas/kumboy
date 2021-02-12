<?php

namespace App\Http\Controllers\Profile\User;

use App\Http\Controllers\DatabaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ProfileController extends DatabaseController
{
    protected $user;

    public function __construct(Request $request)
    {
        $user = User::query()
            ->where('id', $request->route('id'))
            ->first();

        if ($user === null) {
            abort(404);
        }

        $this->user = $user;
        View::share('user', $user);
    }
}
