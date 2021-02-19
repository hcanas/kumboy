<?php
namespace App\Http\Controllers\Profile\User;

use App\Models\UserActivity;
use Illuminate\Http\Request;

class ActivityController extends ProfileController
{
    public function search($user_id, Request $request)
    {
        return redirect()
            ->route('user.activity-log', [$user_id, 1, 25, $request->get('keyword')]);
    }

    public function list($user_id, $current_page = 1, $items_per_page = 12, $keyword = null)
    {
        $this->authorize('list', [new UserActivity(), $user_id]);

        $activity_log = UserActivity::query()
            ->where('user_id', $user_id);

        if (empty($keyword) === false) {
            $activity_log->whereRaw('MATCH (action_taken) AGAINST (? IN BOOLEAN MODE)', [$keyword.'*']);
        }

        $total_count = $activity_log->count();
        $offset = ($current_page - 1) * $items_per_page;

        $activities = $activity_log->skip($offset)
            ->take($items_per_page)
            ->orderByDesc('date_recorded')
            ->get();

        return view('pages.user.activity.list')
            ->with('activities', $activities)
            ->with('keyword', $keyword)
            ->with('pagination', view('partials.pagination')
                ->with('item_start', $offset + 1)
                ->with('item_end', $activities->count() + $offset)
                ->with('total_count', $total_count)
                ->with('current_page', $current_page)
                ->with('total_pages', ceil($total_count / $items_per_page))
                ->with('items_per_page', $items_per_page)
                ->with('keyword', $keyword)
                ->with('route_name', 'user.activity-log')
                ->with('route_params', [
                    'id' => $user_id,
                    'items_per_page' => $items_per_page,
                ])
            );
    }
}
