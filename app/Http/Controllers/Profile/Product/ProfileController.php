<?php
namespace App\Http\Controllers\Profile\Product;

use App\Events\UpdatedProduct;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DatabaseController;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSpecification;
use App\Models\Store;
use App\Services\ImageService;
use App\Traits\Validation\HasProductValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class ProfileController extends DatabaseController
{
    use HasProductValidation;

    public function __construct(Request $request)
    {
        $product = $this->getDetails($request->route('id'));

        if ($product === null) {
            abort(404);
        }

        View::share('product', $product);
    }

    public function index()
    {
        return view('pages.product.profile');
    }

    public function update(Request $request, ImageService $image_service, $product_id)
    {
        if ($request->wantsJson()) {
            $product = Product::query()->find($product_id);

            if ($product === null) {
                return response()->json('Product not found.', 404);
            }

            $store = Store::query()->find($product->store_id);

            $gate = Gate::inspect('manage', [new Product(), $store->user_id]);

            if ($gate->allowed()) {
                $validator = Validator::make($request->all(), $this->getProductRules());

                if ($validator->fails()) {
                    return response()->json($validator->errors(), 400);
                }

                try {
                    $this->beginTransaction();

                    $category = explode('|', $request->get('category'));
                    $product->update([
                        'name' => $request->get('name'),
                        'qty' => $request->get('qty'),
                        'price' => $request->get('price'),
                        'main_category' => $category[0],
                        'sub_category' => $category[1] === 'all' ? null : $category[1],
                    ]);

                    // delete existing specifications and replace with new ones
                    ProductSpecification::query()
                        ->where('product_id', $product->id)
                        ->delete();

                    $specifications = explode('|', $request->get('specifications'));
                    foreach ($specifications AS $spec) {
                        list($name, $value) = explode(':', $spec);

                        ProductSpecification::query()
                            ->create([
                                'product_id' => $product->id,
                                'name' => trim($name),
                                'value' => trim($value),
                            ]);
                    }

                    if ($request->has('deleted_images')) {
                        foreach ($request->get('deleted_images') AS $image_id => $val) {
                            $product_image = ProductImage::query()->find($image_id);

                            if ($product_image !== null) {
                                Storage::delete('products/images/original/'.$product_image->filename);
                                Storage::delete('products/images/preview/'.$product_image->filename);
                                Storage::delete('products/images/thumbnail/'.$product_image->filename);
                                $product_image->delete();
                            }
                        }
                    }

                    if ($request->files->count() > 0) {
                        $uploaded_files = [];

                        foreach ($request->file('images') AS $file) {
                            if (!$image_service->isValid($file)) {
                                return response()->json([
                                    'images' => 'Some files are too large or has invalid format.',
                                ],
                                    400
                                );
                            }
                        }

                        foreach ($request->file('images') AS $file) {
                            $ext = substr($file->getMimeType(), strpos($file->getMimeType(), '/') + 1);
                            $filename = $product->id.'_'.bin2hex(random_bytes(4)).'_'.$store->id.'.'.$ext;
                            $uploaded_files[] = $filename;

                            ProductImage::query()
                                ->create([
                                    'product_id' => $product->id,
                                    'filename' => $filename,
                                ]);

                            $original = $image_service->resize($file, 512, 512, 6);
                            Storage::put('products/images/original/'.$filename, $original);

                            $preview = $image_service->resize($file, 150, 150, 6);
                            Storage::put('products/images/preview/'.$filename, $preview);

                            $thumbnail = $image_service->resize($file, 100, 100, 6);
                            Storage::put('products/images/thumbnail/'.$filename, $thumbnail);
                        }
                    }

                    event(new UpdatedProduct($store, $product));

                    $this->commit();

                    return response()->json($product);
                } catch (\Exception $e) {
                    $this->rollback();
                    logger($e);

                    if (!empty($uploaded_files)) {
                        foreach ($uploaded_files AS $filename) {
                            Storage::delete('products/images/original/'.$filename);
                            Storage::delete('products/images/preview/'.$filename);
                            Storage::delete('products/images/thumbnail/'.$filename);
                        }
                    }

                    return response()->json('Unable to add product. Try again later.', 500);
                }
            } else {
                return response()->json('Forbidden.', 403);
            }
        }
    }

    private function getDetails($id)
    {
        $product = Product::query()
            ->with('store')
            ->with(['specifications' => function ($query) {
                $query->orderBy('name');
            }])
            ->with('images')
            ->where('id', $id)
            ->first();

        $product->variants = Product::query()
            ->addSelect(['preview' => ProductImage::query()
                ->whereColumn('product_images.product_id', 'products.id')
                ->select('filename')
                ->limit(1)
            ])
            ->where('id', '!=', $product->id)
            ->where('name', $product->name)
            ->where('store_id', $product->store_id)
            ->where('main_category', $product->main_category)
            ->where('sub_category', $product->sub_category)
            ->with(['specifications' => function ($query) {
                $query->orderBy('name');
            }])
            ->get();

        return $product;
    }
}
