<?php
namespace App\Http\Controllers\Profile\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends ProfileController
{
    public function search($user_id, Request $request)
    {
        return redirect()
            ->route('user.notifications', [$user_id, 1, 15, $request->get('keyword')]);
    }

    public function list($user_id, $current_page = 1, $items_per_page = 12, $keyword = null)
    {
        if (Auth::user()->id !== $this->user->id) {
            abort(403);
        }

        $userNotifications = Auth::user()->notifications();

        if (empty($keyword) === false) {
            $userNotifications->whereRaw('MATCH (data) AGAINST (? IN BOOLEAN MODE)', [$keyword.'*']);
        }

        $offset = ($current_page - 1) * $items_per_page;
        $total_count = $userNotifications->count();

        $notifications = $userNotifications->skip($offset)
            ->take($items_per_page)
            ->get();

        return view('pages.user.notification.list')
            ->with('notifications', $notifications)
            ->with('keyword', $keyword)
            ->with('pagination', view('shared.pagination')
                ->with('item_start', $offset + 1)
                ->with('item_end', $notifications->count() + $offset)
                ->with('total_count', $total_count)
                ->with('current_page', $current_page)
                ->with('total_pages', ceil($total_count / $items_per_page))
                ->with('items_per_page', $items_per_page)
                ->with('keyword', $keyword)
                ->with('route_name', 'user.notifications')
                ->with('route_params', [
                    'id' => $user_id,
                    'items_per_page' => $items_per_page,
                ])
            );
    }

    public function read($user_id, $notif_id)
    {
        if (Auth::user()->id !== $this->user->id) {
            abort(403);
        }

        $notification = Auth::user()->unreadNotifications()->find($notif_id);

        if ($notification === null) {
            abort(404);
        }

        try {
            $notification->markAsRead();
            return $this->view($user_id, $notif_id);
        } catch (\Exception $e) {
            logger($e);
            return back()
                ->with('message_type', 'danger')
                ->with('message_content', 'Server error.');
        }
    }

    public function view($user_id, $notif_id)
    {
        if (Auth::user()->id !== $this->user->id) {
            abort(403);
        }

        $notification = Auth::user()->notifications()->find($notif_id);

        if ($notification === null) {
            abort(404);
        }

        switch ($notification->data['category']) {
            case 'new_store':
            case 'update_store':
            case 'store_transfer':
                $redirect = redirect()
                    ->route('user.store-request-details', [
                        $notification->data['user_id'],
                        $notification->data['ref_no']
                    ]);
                break;
        }

        return $redirect;
    }
}
