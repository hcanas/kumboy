<?php
namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StoreController extends Controller
{
    public function search(Request $request)
    {
        return redirect()
            ->route('store.list', [1, 9, $request->get('keyword')]);
    }

    public function index(Request $request, $current_page = 1, $items_per_page = 9, $keyword = null)
    {
        $offset = ($current_page - 1) * $items_per_page;

        if (Cache::tags(['stores', $request->url()])->has('data')) {
            $stores = Cache::tags(['stores', $request->url()])->get('data');
            $total_count = Cache::tags(['stores', $request->url()])->get('count');
        } else {
            $stores = Store::query();

            if (!empty($keyword)) {
                $stores->whereRaw('MATCH (name) AGAINST (? IN BOOLEAN MODE)', [$keyword.'*']);
            }

            $total_count = $stores->count();

            $stores = $stores
                ->addSelect(['user_name' => User::query()
                    ->whereColumn('id', 'stores.user_id')
                    ->select('name')
                    ->limit(1)
                ])
                ->skip($offset)
                ->take($items_per_page)
                ->get();

            Cache::tags(['stores', $request->url()])->put('data', $stores);
            Cache::tags(['stores', $request->url()])->put('count', $total_count);
        }

        return view('pages.store.list')
            ->with('stores', $stores)
            ->with('keyword', $keyword)
            ->with('pagination', view('partials.pagination')
                ->with('item_start', $offset + 1)
                ->with('item_end', $stores->count() + $offset)
                ->with('total_count', $total_count)
                ->with('current_page', $current_page)
                ->with('total_pages', ceil($total_count / $items_per_page))
                ->with('items_per_page', $items_per_page)
                ->with('keyword', $keyword)
                ->with('route_name', 'store.list')
                ->with('route_params', [
                    'items_per_page' => $items_per_page,
                ])
            );
    }
}
