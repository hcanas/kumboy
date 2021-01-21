<?php
namespace App\Http\Controllers\Profile\User;

use App\Models\UserActivity;
use Illuminate\Http\Request;

class ActivityController extends ProfileController
{
    public function searchActivity($user_id, Request $request)
    {
        return redirect()
            ->route('user.activity-log', [$user_id, 1, 25, $request->get('keyword')]);
    }

    public function viewActivities($user_id, $current_page = 1, $items_per_page = 12, $keyword = null)
    {
        $this->authorize('viewActivities', [new UserActivity(), $user_id]);

        $this->profile->with('content', 'users.profile.activity.index');

        $userActivity = UserActivity::query()
            ->where('user_id', $user_id);

        if (empty($keyword) === false) {
            $userActivity->whereRaw('MATCH (action_taken) AGAINST (? IN BOOLEAN MODE)', [$keyword.'*']);
        }

        $total_count = $userActivity->count();
        $offset = ($current_page - 1) * $items_per_page;

        $activities = $userActivity->skip($offset)
            ->take($items_per_page)
            ->orderByDesc('date_recorded')
            ->get();

        return $this->profile
            ->with('contentData', [
                'user' => $this->user,
                'activities' => $activities,
                'item_start' => $offset + 1,
                'item_end' => $activities->count() + $offset,
                'total_count' => $total_count,
                'current_page' => $current_page,
                'total_pages' => ceil($total_count / $items_per_page),
                'items_per_page' => $items_per_page,
                'keyword' => $keyword,
                'pagination' => view('shared.pagination')
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
                    ]),
            ]);
    }
}
