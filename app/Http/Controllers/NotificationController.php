<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends DatabaseController
{
    public function countUnread(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json(Auth::user()->unreadNotifications->count());
        }
    }
}
