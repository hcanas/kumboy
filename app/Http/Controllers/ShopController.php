<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ShopController extends DatabaseController
{
    public function search(Request $request)
    {
        $category = explode('|', $request->get('category') ?? []);
        $sort = explode('|', $request->get('sort') ?? []);

        return redirect()
            ->route('shop', [
                'current_page' => 1,
                'items_per_page' => 24,
                'price_from' => $request->get('price_from') ?? 0,
                'price_to' => $request->get('price_to') ?? 1000000,
                'main_category' => $category[0] ?? 'all',
                'sub_category' => $category[1] ?? 'all',
                'sort_by' => $sort[0] ?? 'sold',
                'sort_dir' => $sort[1] ?? 'desc',
                'keyword' => $request->get('keyword'),
            ]);
    }

    public function index(
        Request $request,
        $current_page = 1,
        $items_per_page = 24,
        $price_from = 0,
        $price_to = 1000000,
        $main_category = 'all',
        $sub_category = 'all',
        $sort_by = 'sold',
        $sort_dir = 'desc',
        $keyword = null
    ) {
        $offset = ($current_page - 1) * $items_per_page;

        $query = Product::query()
            ->addSelect(['preview' => ProductImage::query()
                ->whereColumn('product_images.product_id', 'products.id')
                ->select('filename')
                ->limit(1)
            ]);

        if (!empty($keyword)) {
            $query->whereRaw('MATCH (name) AGAINST (? IN BOOLEAN MODE)', [$keyword.'*']);
        }

        if (!empty($main_category) AND $main_category !== 'all') {
            $query->where('main_category', $main_category);
        }

        if (!empty($sub_category) AND $sub_category !== 'all') {
            $query->where('sub_category', $sub_category);
        }

        $query->whereBetween('price', [$price_from, $price_to]);

        $total_count = $query->count();

        $products = $query->skip($offset)
            ->take($items_per_page)
            ->orderBy($sort_by, $sort_dir)
            ->get();

        return view('pages.product.shop')
            ->with('products', $products)
            ->with('product_filter', view('partials.product_filter')
                ->with('filters', $request->route()->parameters)
                ->with('product_categories', config('system.product_categories'))
                ->with('url', route('shop.search'))
            )
            ->with('pagination', view('partials.pagination')
                ->with('item_start', $offset + 1)
                ->with('item_end', $products->count() + $offset)
                ->with('total_count', $total_count)
                ->with('current_page', $current_page)
                ->with('total_pages', ceil($total_count / $items_per_page))
                ->with('items_per_page', $items_per_page)
                ->with('keyword', $keyword)
                ->with('route_name', 'shop')
                ->with('route_params', $request->route()->parameters)
            );
    }
}
