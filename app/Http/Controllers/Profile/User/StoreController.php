<?php
namespace App\Http\Controllers\Profile\User;

use App\Models\Store;
use App\Models\User;
use App\Services\ImageService;
use App\Traits\Validation\HasStoreApplicationValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class StoreController extends ProfileController
{
    use HasStoreApplicationValidation;

    public function list($user_id)
    {
        $this->authorize('listOwn', [new Store(), $user_id]);

        $stores = Store::query()
            ->where('user_id', $user_id)
            ->orderBy('name')
            ->get();

        return view('pages.user.store.list')
            ->with('stores', $stores);
    }

    public function uploadLogo(Request $request, ImageService $image_service, $user_id, $store_id)
    {
        if ($request->wantsJson()) {
            $store = Store::query()
                ->where('user_id', $user_id)
                ->where('id', $store_id)
                ->first();

            if ($store === null) {
                return response()->json('Store not found.', 404);
            }

            $gate = Gate::inspect('uploadLogo', $store);

            if ($gate->allowed()) {
                $validator = Validator::make($request->all(), [
                    'logo' => 'required|file|mimetypes:image/jpeg,image/png',
                ]);

                if ($validator->fails()) {
                    return response()->json($validator->errors(), 400);
                }

                try {
                    if ($logo = $image_service->make($request->file('logo'))) {
                        $ext = substr($logo->mime(), strpos($logo->mime(), '/') + 1);
                        $filename = $store_id.'_'.strtotime('now').'.'.$ext;
                        $logo = $image_service->resize($request->file('logo'), 150, 150, 0);

                        if ($store->logo !== null) {
                            Storage::delete('stores/logos/'.$store->logo);
                        }

                        $store->update(['logo' => $filename]);
                        Storage::put('stores/logos/'.$filename, $logo);

                        return response()->json($filename);
                    } else {
                        return response()->json('File is not a valid png/jpg image.', 400);
                    }
                } catch (\Exception $e) {
                    logger($e);
                    return response()->json('Unable to upload logo. Try again later.', 500);
                }
            } else {
                return response()->json('Forbidden.', 403);
            }
        }
    }

    protected function create($data)
    {
        $store = Store::query()
            ->create([
                'user_id' => $data->user_id,
                'name' => $data->name,
                'contact_number' => $data->contact_number,
                'address_line' => $data->address_line,
                'map_coordinates' => $data->map_coordinates,
                'map_address' => $data->map_address,
                'open_until' => $data->open_until,
            ]);

        return $store;
    }

    protected function update($store_id, $data)
    {
        $store = Store::query()
            ->where('id', $store_id)
            ->first();

        $store->update([
            'name' => $data['name'],
            'contact_number' => $data['contact_number'],
            'address_line' => $data['address_line'],
            'map_address' => $data['map_address'],
            'map_coordinates' => $data['map_coordinates'],
            'open_until' => $data['open_until'],
        ]);
    }

    protected function transfer($owner_id, $store_id, $target_id)
    {
        $store = Store::query()
            ->where('id', $store_id)
            ->where('user_id', $owner_id)
            ->first();

        $target = User::query()
            ->where('id', $target_id)
            ->first();

        if ($store === null OR $target === null) {
            abort(404);
        }

        $store->update([
            'user_id' => $target_id,
        ]);
    }
}
