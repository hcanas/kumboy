<?php
namespace App\Http\Controllers\Profile\Store;

use App\Http\Controllers\DatabaseController;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ProfileController extends DatabaseController
{
    public function __construct(Request $request)
    {
        $store = Store::query()
            ->addSelect(['user_name' => User::query()
                ->whereColumn('id', 'stores.user_id')
                ->select('name')
                ->limit(1)
            ])
            ->where('id', $request->route('id'))
            ->first();

        if ($store === null) {
            abort(404);
        }

        View::share('store', $store);
    }
}
