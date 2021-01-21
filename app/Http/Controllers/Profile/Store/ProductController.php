<?php
namespace App\Http\Controllers\Profile\Store;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSpecification;
use App\Models\Store;
use App\Traits\Validation\HasProductValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ProductController extends ProfileController
{
    use HasProductValidation;

    public function search($store_id, Request $request)
    {
        $category = explode('|', $request->get('category', []));
        $sort = explode('|', $request->get('sort', []));

        return redirect()
            ->route('store.products', [
                'id' => $store_id,
                'current_page' => 1,
                'items_per_page' => 12,
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
        $store_id,
        $current_page = 1,
        $items_per_page = 12,
        $price_from = 0,
        $price_to = 1000000,
        $main_category = 'all',
        $sub_category = 'all',
        $sort_by = 'sold',
        $sort_dir = 'desc',
        $keyword = null
    ) {
        $offset = ($current_page - 1) * $items_per_page;

        if (Cache::tags(['store-products', $request->url()])->has('data')) {
            $products = Cache::tags(['store-products', $request->url()])->get('data');
            $total_count = Cache::tags(['store-products', $request->url()])->get('count');
        } else {
            $query = Product::query()
                ->where('store_id', $store_id);

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

            Cache::tags(['store-products', $request->url()])->put('data', $products);
            Cache::tags(['store-products', $request->url()])->put('count', $total_count);
        }

        $product_categories = Product::query()
            ->select(['main_category', 'sub_category'])
            ->where('store_id', $store_id)
            ->groupBy('main_category', 'sub_category')
            ->get();

        // convert to array[main_category][sub_category] = []
        $categories = [];
        foreach ($product_categories AS $product_category) {
            if ($product_category->sub_category !== null) {
                $categories[$product_category->main_category][$product_category->sub_category] = [];
            } else {
                $categories[$product_category->main_category] = [];
            }
        }

        return view('pages.store.profile.products')
            ->with('products', $products)
            ->with('product_filter', view('partials.product_filter')
                ->with('product_categories', $categories)
                ->with('filters', $request->route()->parameters)
                ->with('url', route('store.search-products', $store_id))
            )
            ->with('pagination', view('shared.pagination')
                ->with('item_start', $offset + 1)
                ->with('item_end', $products->count() + $offset)
                ->with('total_count', $total_count)
                ->with('current_page', $current_page)
                ->with('total_pages', ceil($total_count / $items_per_page))
                ->with('items_per_page', $items_per_page)
                ->with('keyword', $keyword)
                ->with('route_name', 'store.products')
                ->with('route_params', $request->route()->parameters)
            );
    }

    public function showAddProductForm($store_id)
    {
        $store = Store::query()->find($store_id);

        $this->authorize('addProduct', $store);

        return view('stores.profile.products.form')
            ->with('form_title', 'Add Product')
            ->with('categories', config('system.product_categories'));
    }

    public function addProduct(Request $request, $store_id)
    {
        $store = Store::query()->find($store_id);

        $this->authorize('addProduct', $store);

        $validated_data = $request->validate($this->getProductRules());

        try {
            $uploaded_images = [];

            if ($request->files->count() === 0) {
                return back()
                    ->withErrors(['images' => 'Images are required.'])
                    ->withInput($request->all());
            } else {
                $this->beginTransaction();

                // create product record
                $product_category = explode('|', $validated_data['category']);
                $product = Product::query()
                    ->create([
                        'store_id' => $store_id,
                        'name' => $validated_data['name'],
                        'qty' => $validated_data['qty'],
                        'price' => $validated_data['price'],
                        'main_category' => $product_category[0],
                        'sub_category' => $product_category[1] === 'all' ? null : $product_category[1],
                    ]);

                // insert product specifications
                $specifications = explode('|', $validated_data['specifications']);
                foreach ($specifications AS $spec) {
                    list($name, $value) = explode(':', $spec);

                    ProductSpecification::query()
                        ->create([
                            'product_id' => $product->id,
                            'name' => trim($name),
                            'value' => trim($value),
                        ]);
                }

                // validate images
                $files = $request->file('images');
                for ($i = 0; $i < count($files); $i++) {
                    // only allow png and jpeg
                    $ext = substr($files[$i]->getMimeType(), strpos($files[$i]->getMimeType(), '/') + 1);

                    if (in_array($ext, ['jpeg', 'png']) === false) {
                        return back()
                            ->withErrors(['images' => 'Some images have invalid format.'])
                            ->withInput($request->all());
                    }

                    // file size must not exceed 500kb
                    if ($files[$i]->getSize() / 1024 > 500) {
                        return back()
                            ->withErrors(['images' => 'Some files are too large.'])
                            ->withInput($request->all());
                    }

                    $image = Image::make($files[$i]);

                    // resize image to 512x512
                    if ($image->width() === $image->height()) {
                        // square
                        $image->resize(500, 500);
                        $image->resizeCanvas(512, 512, 'center', false, '#ffffff');
                    } elseif ($image->width() > $image->height()) {
                        // horizontal, pad left and right
                        $image->resize(500, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $image->resizeCanvas(512, 512, 'center', false, '#ffffff');
                    } elseif ($image->width() < $image->height()) {
                        // vertical, pad top and bottom
                        $image->resize(null, 500, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $image->resizeCanvas(512, 512, 'center', false, '#ffffff');
                    }

                    $filename = $store_id.$product->id.substr(strtotime('now'), -6).$i.'.'.$ext;
                    $uploaded_images[] = 'products/images/thumbnail/'.$filename;

                    // upload original
                    Storage::put('products/images/original/'.$filename, (string) $image->encode());

                    //upload preview
                    $image->resize(150, 150);
                    Storage::put('products/images/preview/'.$filename, (string) $image->encode());

                    //upload thumbnail
                    $image->resize(50, 50);
                    Storage::put('products/images/thumbnail/'.$filename, (string) $image->encode());

                    ProductImage::query()
                        ->create([
                            'product_id' => $product->id,
                            'filename' => $filename,
                        ]);
                }

                Cache::tags('shop')->flush();

                $this->commit();

                return redirect()
                    ->route('store.products', $store_id)
                    ->with('message_type', 'success')
                    ->with('message_content', $product->name.' has been added.');
            }
        } catch (\Exception $e) {
            $this->rollback();
            logger($e);

            // delete uploaded images
            foreach ($uploaded_images AS $image) {
                Storage::delete($image);
            }

            return back()
                ->with('message_type', 'danger')
                ->with('message_content', 'Server error.')
                ->withInput($request->all());
        }
    }

    public function showEditProductForm($store_id, $product_id)
    {
        $store = Store::query()->find($store_id);

        $product = Product::query()
            ->where('id', $product_id)
            ->where('store_id', $store_id)
            ->with(['specifications' => function ($query) {
                $query->orderBy('name', 'asc');
            }])
            ->with('images')
            ->first();

        if ($product === null) {
            abort(404);
        }

        $this->authorize('editProduct', $store);

        return view('stores.profile.products.form')
            ->with('form_title', 'Edit Product')
            ->with('form_data', $product)
            ->with('categories', config('system.product_categories'));
    }

    public function updateProduct(Request $request, $store_id, $product_id)
    {
        $store = Store::query()->find($store_id);
        $product = Product::query()->find($product_id);

        if ($product === null) {
            abort(404);
        }

        $this->authorize('editProduct', $store);

        $validated_data = $request->validate($this->getProductRules());

        try {
            $uploaded_images = [];

            $this->beginTransaction();

            // update product
            $product_category = explode('|', $validated_data['category']);
            $product->update([
                'name' => $validated_data['name'],
                'qty' => $validated_data['qty'],
                'price' => $validated_data['price'],
                'main_category' => $product_category[0],
                'sub_category' => $product_category[1] === 'all' ? null : $product_category[1],
            ]);

            // delete current specifications
            ProductSpecification::query()
                ->where('product_id', $product_id)
                ->delete();

            // insert product specifications
            $specifications = explode('|', $validated_data['specifications']);
            foreach ($specifications AS $spec) {
                list($name, $value) = explode(':', $spec);

                ProductSpecification::query()
                    ->create([
                        'product_id' => $product->id,
                        'name' => trim($name),
                        'value' => trim($value),
                    ]);
            }

            if ($request->files->count() > 0) {
                // validate images
                $files = $request->file('images');
                for ($i = 0; $i < count($files); $i++) {
                    // only allow png and jpeg
                    $ext = substr($files[$i]->getMimeType(), strpos($files[$i]->getMimeType(), '/') + 1);

                    if (in_array($ext, ['jpeg', 'png']) === false) {
                        return back()
                            ->withErrors(['images' => 'Some images have invalid format.'])
                            ->withInput($request->all());
                    }

                    // file size must not exceed 500kb
                    if ($files[$i]->getSize() / 1024 > 500) {
                        return back()
                            ->withErrors(['images' => 'Some files are too large.'])
                            ->withInput($request->all());
                    }

                    $image = Image::make($files[$i]);

                    // resize image to 512x512
                    if ($image->width() === $image->height()) {
                        // square
                        $image->resize(500, 500);
                        $image->resizeCanvas(512, 512, 'center', false, '#ffffff');
                    } elseif ($image->width() > $image->height()) {
                        // horizontal, pad left and right
                        $image->resize(500, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $image->resizeCanvas(512, 512, 'center', false, '#ffffff');
                    } elseif ($image->width() < $image->height()) {
                        // vertical, pad top and bottom
                        $image->resize(null, 500, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $image->resizeCanvas(512, 512, 'center', false, '#ffffff');
                    }

                    $filename = $store_id.$product->id.substr(strtotime('now'), -6).$i.'.'.$ext;
                    $uploaded_images[] = 'products/images/thumbnail/'.$filename;

                    // upload original
                    Storage::put('products/images/original/'.$filename, (string) $image->encode());

                    //upload preview
                    $image->resize(150, 150);
                    Storage::put('products/images/preview/'.$filename, (string) $image->encode());

                    //upload thumbnail
                    $image->resize(100, 100);
                    Storage::put('products/images/thumbnail/'.$filename, (string) $image->encode());

                    ProductImage::query()
                        ->create([
                            'product_id' => $product->id,
                            'filename' => $filename,
                        ]);

                    if ($i === 0) {
                        $product->update(['preview' => $filename]);
                    }
                }
            }

            $this->commit();

            // process deleted images
            if (empty($request->get('removed', [])) === false) {
                foreach ($request->get('removed') AS $id => $value) {
                    $product_image = ProductImage::query()->find($id);
                    $product_image->delete();

                    if ($product->preview === $product_image->filename) {
                        $new_preview = ProductImage::query()
                            ->where('product_id', $product_id)
                            ->inRandomOrder()
                            ->first();

                        $product->update(['preview' => $new_preview->filename]);
                    }

                    Storage::delete('products/images/original/'.$product_image->filename);
                    Storage::delete('products/images/preview/'.$product_image->filename);
                    Storage::delete('products/images/thumbnail/'.$product_image->filename);
                }
            }

            return back()
                ->with('message_type', 'success')
                ->with('message_content', 'Product has been updated.');
        } catch (\Exception $e) {
            $this->rollback();
            logger($e);

            // delete uploaded images
            foreach ($uploaded_images AS $image) {
                Storage::delete($image);
            }

            return back()
                ->with('message_type', 'danger')
                ->with('message_content', 'Server error.')
                ->withInput($request->all());
        }
    }
}
