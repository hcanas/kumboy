<?php
namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function search(Request $request)
    {
        return redirect()
            ->route('store.view-all', [1, 6, $request->get('keyword')]);
    }

    public function viewAll($current_page = 1, $items_per_page = 6, $keyword = null)
    {
        $stores = Store::query();

        if (empty($keyword) === false) {
            $stores->whereRaw('MATCH (name) AGAINST (? IN BOOLEAN MODE)', [$keyword.'*']);
        }

        $offset = ($current_page - 1) * $items_per_page;
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

        return view('stores.index')
            ->with('stores', $stores)
            ->with('pagination', view('shared.pagination')
                ->with('item_start', $offset + 1)
                ->with('item_end', $stores->count() + $offset)
                ->with('total_count', $total_count)
                ->with('current_page', $current_page)
                ->with('total_pages', ceil($total_count / $items_per_page))
                ->with('items_per_page', $items_per_page)
                ->with('keyword', $keyword)
                ->with('route_name', 'store.view-all')
                ->with('route_params', [
                    'items_per_page' => $items_per_page,
                ])
            );
    }
}
