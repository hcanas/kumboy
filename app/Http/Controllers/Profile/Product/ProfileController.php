<?php
namespace App\Http\Controllers\Profile\Product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DatabaseController;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class ProfileController extends DatabaseController
{
    public function __construct(Request $request)
    {
        $product = Product::query()
            ->with('store')
            ->with(['specifications' => function ($query) {
                $query->orderBy('name');
            }])
            ->with('images')
            ->where('id', $request->route('id'))
            ->first();

        $product->variants = Product::query()
            ->where('id', '!=', $product->id)
            ->where('name', $product->name)
            ->where('store_id', $product->store_id)
            ->with(['specifications' => function ($query) {
                $query->orderBy('name');
            }])
            ->get();

        if ($product === null) {
            abort(404);
        }

        View::share('product', $product);
    }

    public function index()
    {
        return view('pages.product.profile');
    }
}
