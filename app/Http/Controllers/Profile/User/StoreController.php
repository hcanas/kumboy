<?php
namespace App\Http\Controllers\Profile\User;

use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class StoreController extends ProfileController
{
    public function showStores($user_id)
    {
        $this->authorize('viewUserStores', [new Store(), $user_id]);

        $this->profile->with('content', 'users.profile.stores.index');

        $stores = Store::query()
            ->where('user_id', $user_id)
            ->get();

        return $this->profile->with('contentData', ['stores' => $stores]);
    }

    public function uploadLogo(Request $request, $user_id, $store_id)
    {
        if ($request->wantsJson()) {
            $store = Store::query()
                ->where('user_id', $user_id)
                ->where('id', $store_id)
                ->first();

            if ($store === null) {
                return response()->json('Store not found.', 404);
            }

            $validator = Validator::make($request->all(), [
                'store_logo' => 'required|file|mimetypes:image/jpeg,image/png',
            ]);

            if ($validator->failed()) {
                return response()->json($validator->errors(), 400);
            }

            $gate = Gate::inspect('uploadLogo', $store);

            if ($gate->allowed()) {
                try {
                    $logo = Image::make($request->file('store_logo')->path());
                    $logo->resize(
                        $logo->width() > 150 ? 150 : $logo->width(),
                        $logo->height() > 150 ? 150 : $logo->height()
                    );

                    if ($logo->mime() === 'image/png') {
                        $file_ext = '.png';
                    } elseif ($logo->mime() === 'image/jpeg') {
                        $file_ext = '.jpg';
                    }

                    $filename = $store_id.'_'.strtotime('now').$file_ext;

                    if ($store->logo !== null) {
                        Storage::delete('stores/logos/'.$store->logo);
                    }

                    Storage::put('stores/logos/'.$filename, (string) $logo->encode());

                    $store->update(['logo' => $filename]);

                    return response()->json($filename);
                } catch (\Exception $e) {
                    logger($e);
                    return response()->json('Server error.', 500);
                }
            } else {
                return response()->json('Forbidden.', 403);
            }
        }
    }

    protected function addStore($user_id, $data)
    {
        Store::query()
            ->create([
                'user_id' => $user_id,
                'name' => $data->name,
                'contact_number' => $data->contact_number,
                'address' => $data->address,
                'map_coordinates' => $data->map_coordinates,
                'map_address' => $data->map_address,
                'open_until' => $data->open_until,
            ]);
    }

    protected function updateStore($storeUuid, $data)
    {
        $store = Store::query()
            ->where('id', $storeUuid)
            ->first();

        if ($store === null) {
            abort(404);
        }

        $store->update([
            'user_id' => $data['user_id'],
            'name' => $data['name'],
            'contact_number' => $data['contact_number'],
            'address' => $data['address'],
            'map_address' => $data['map_address'],
            'map_coordinates' => $data['map_coordinates'],
            'open_until' => $data['open_until'],
        ]);
    }

    protected function transferStore($ownerUuid, $storeUuid, $targetUuid)
    {
        $store = Store::query()
            ->where('id', $storeUuid)
            ->where('user_id', $ownerUuid)
            ->first();

        $target = User::query()
            ->where('id', $targetUuid)
            ->first();

        if ($store === null OR $target === null) {
            abort(404);
        }

        $store->update([
            'user_id' => $targetUuid,
        ]);
    }
}
