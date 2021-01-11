<?php
namespace App\Http\Controllers\Profile\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class ProfileController extends Controller
{
    protected $product;

    public function __construct(Request $request)
    {
        $product = Product::query()
            ->with('vendor')
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

        $this->product = $product;
        View::share('product', $product);
    }

    public function index()
    {
        return view('products.profile.index');
    }
}
