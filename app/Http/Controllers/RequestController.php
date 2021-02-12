<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\StoreRequest;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function countPending(Request $request)
    {
        $this->authorize('countPendingRequests', new StoreRequest());

        if ($request->wantsJson()) {
            $count = StoreRequest::query()
                ->where('status', 'pending')
                ->count();

            return response()->json($count);
        }
    }

    public function search(Request $request)
    {
        return redirect()
            ->route('request.list', [1, 25, $request->get('keyword')]);
    }

    public function list($current_page = 1, $items_per_page = 12, $keyword = null)
    {
        $this->authorize('list', new StoreRequest());

        $offset = ($current_page - 1) * $items_per_page;

        $store_request = StoreRequest::query()
            ->addSelect(['user_name' => User::query()
                ->whereColumn('id', 'store_requests.user_id')
                ->select('name')
                ->limit(1)
            ])
            ->addSelect(['evaluator_name' => User::query()
                ->whereColumn('id', 'store_requests.evaluated_by')
                ->select('name')
                ->limit(1)
            ]);

        if (empty($keyword) === false) {
            $store_request->whereRaw('MATCH (code, type, status) AGAINST(? IN BOOLEAN MODE)', [$keyword.'*'])
                ->orWhereHas('user', function ($query) use ($keyword) {
                    $query->where('name', 'LIKE', '%'.$keyword.'%');
                })
                ->orWhereHas('evaluator', function ($query) use ($keyword) {
                    $query->where('name', 'LIKE', '%'.$keyword.'%');
                });
        }

        $total_count = $store_request->count();

        $list = $store_request->skip($offset)
            ->take($items_per_page)
            ->orderByRaw('status = "pending" DESC, created_at DESC')
            ->get();

        return view('pages.request.list')
            ->with('requests', $list)
            ->with('pagination', view('partials.pagination')
                ->with('item_start', $offset + 1)
                ->with('item_end', $list->count() + $offset)
                ->with('total_count', $total_count)
                ->with('current_page', $current_page)
                ->with('total_pages', ceil($total_count / $items_per_page))
                ->with('items_per_page', $items_per_page)
                ->with('keyword', $keyword)
                ->with('route_name', 'request.list')
                ->with('route_params', [
                    'items_per_page' => $items_per_page,
                ])
            );
    }
}
