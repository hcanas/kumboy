<?php
namespace App\Http\Controllers\Profile\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends ProfileController
{
    public function searchNotification($user_id, Request $request)
    {
        return redirect()
            ->route('user.notifications', [$user_id, 1, 15, $request->get('keyword')]);
    }

    public function viewAll($user_id, $current_page = 1, $items_per_page = 12, $keyword = null)
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

        return $this->profile
            ->with('content', 'users.profile.notifications.index')
            ->with('contentData', [
                'user' => $this->user,
                'notifications' => $notifications,
                'item_start' => $offset + 1,
                'item_end' => $notifications->count() + $offset,
                'total_count' => $total_count,
                'current_page' => $current_page,
                'total_pages' => ceil($total_count / $items_per_page),
                'items_per_page' => $items_per_page,
                'keyword' => $keyword,
            ]);
    }

    public function readNotification($user_id, $notif_id)
    {
        if (Auth::user()->id !== $this->user->id) {
            abort(403);
        }

        $notification = Auth::user()->unreadNotifications()->find($notif_id);

        if ($notification === null) {
            abort(404);
        }

        try {
            $this->beginTransaction();

            $notification->markAsRead();

            switch ($notification->data['type']) {
                case 'store_request':
                    $redirect = redirect()
                        ->route('user.store-request-details', [
                            $notification->data['user_id'],
                            $notification->data['code']
                        ]);
                    break;
                case 'store_received':
                    $redirect = redirect()
                        ->route('store.products', $notification->data['store_id']);
                    break;
            }

            $this->commit();

            return $redirect;
        } catch (\Exception $e) {
            $this->rollback();
            logger($e);
            return back()
                ->with('message_type', 'danger')
                ->with('message_content', 'Server error.');
        }
    }

    public function viewNotification($user_id, $notif_id)
    {
        if (Auth::user()->id !== $this->user->id) {
            abort(403);
        }

        $notification = Auth::user()->notifications()->find($notif_id);

        if ($notification === null) {
            abort(404);
        }

        switch ($notification->data['type']) {
            case 'store_request':
                $redirect = redirect()
                    ->route('user.store-request-details', [
                        $notification->data['user_id'],
                        $notification->data['code']
                    ]);
                break;
        }

        return $redirect;
    }
}
