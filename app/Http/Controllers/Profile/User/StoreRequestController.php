<?php
namespace App\Http\Controllers\Profile\User;

use App\Events\StoreRequestCreate;
use App\Events\StoreRequestApprove;
use App\Events\StoreRequestCancel;
use App\Events\StoreRequestReject;
use App\Models\Store;
use App\Models\User;
use App\Models\StoreRequest;
use App\Services\MapService;
use App\Traits\Validation\HasStoreApplicationValidation;
use App\Traits\Validation\HasStoreTransferValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreRequestController extends StoreController
{
    use HasStoreApplicationValidation, HasStoreTransferValidation;

    public function searchRequest($user_id, Request $request)
    {
        return redirect()
            ->route('user.store-requests', [$user_id, 1, 25, $request->get('keyword')]);
    }

    public function viewRequests($user_id, $current_page = 1, $items_per_page = 12, $keyword = null)
    {
        $this->authorize('viewStoreRequests', [new StoreRequest(), $user_id]);

        $this->profile->with('content', 'users.profile.stores.requests.index');

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
            ])
            ->where('user_id', $user_id);

        if (empty($keyword) === false) {
            $store_request->where(function ($query) use ($keyword) {
                $query->whereRaw('MATCH (code, type, status) AGAINST (? IN BOOLEAN MODE)', [$keyword.'*'])
                    ->orWhereHas('evaluator', function ($query) use ($keyword) {
                        $query->where('name', 'LIKE', '%'.$keyword.'%');
                    });
            });
        }

        $total_count = $store_request->count();
        $offset = ($current_page - 1) * $items_per_page;

        $requests = $store_request->skip($offset)
            ->take($items_per_page)
            ->orderByDesc('created_at')
            ->get();

        return $this->profile->with('contentData', [
                'user' => $this->user,
                'requests' => $requests,
                'item_start' => $offset + 1,
                'item_end' => $requests->count() + $offset,
                'total_count' => $total_count,
                'current_page' => $current_page,
                'total_pages' => ceil($total_count / $items_per_page),
                'items_per_page' => $items_per_page,
                'keyword' => $keyword,
            ]
        );
    }

    public function viewRequestDetails($user_id, $requestCode)
    {
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
            ])
            ->where('user_id', $user_id)
            ->where('code', $requestCode)
            ->first();

        if ($store_request === null) {
            abort(404);
        }

        if (in_array($store_request->type, ['store creation', 'store update'])) {
            $store_request->store_application = $store_request->storeApplication()->first()->toArray();

            if ($store_request->type === 'store update') {
                $store_original = Store::query()->find($store_request->store_application['store_id']);

                $store_request->store_original = $store_original !== null ? $store_original->toArray() : [];
            }
        } elseif ($store_request->type === 'store transfer') {
            $store_request->store_transfer = $store_request->storeTransfer()
                ->addSelect(['target_name' => User::query()
                    ->whereColumn('id', 'store_transfer_requests.target_id')
                    ->select('name')
                    ->limit(1)
                ])
                ->first()
                ->toArray();

            $store_request->store = Store::query()->find($store_request->store_transfer['store_id']);
        }

        $this->authorize('viewRequestDetails', $store_request);

        return $this->profile
            ->with('content', 'users.profile.stores.requests.details')
            ->with('contentData', ['request' => $store_request]);
    }

    public function cancelRequest($user_id, $requestCode)
    {
        $store_request = StoreRequest::query()
            ->where('user_id', $user_id)
            ->where('code', $requestCode)
            ->where('status', 'pending')
            ->first();

        if ($store_request === null) {
            abort(404);
        }

        $this->authorize('cancelRequest', $store_request);

        try {
            $this->beginTransaction();

            $store_request->update(['status' => 'cancelled']);

            event(new StoreRequestCancel($store_request));

            $this->commit();

            return back()
                ->with('message_type', 'success')
                ->with('message_content', 'Request has been cancelled.');
        } catch (\Exception $e) {
            $this->rollback();
            logger($e);
            return back()
                ->with('message_type', 'danger')
                ->with('message_content', 'Server error.');
        }
    }

    public function approveRequest($user_id, $requestCode)
    {
        $store_request = StoreRequest::query()
            ->where('user_id', $user_id)
            ->where('code', $requestCode)
            ->where('status', 'pending')
            ->first();

        if ($store_request === null) {
            abort(404);
        }

        $this->authorize('approveRequest', $store_request);

        try {
            $this->beginTransaction();

            switch ($store_request->type) {
                case 'store creation':
                    $store_application = $store_request->storeApplication()->first();
                    $this->addStore($user_id, $store_application);
                    break;
                case 'store update':
                    $store_application = $store_request->storeApplication()->first();
                    $store_application->user_id = $store_request->user_id;
                    $this->updateStore($store_application->store_id, $store_application);
                    break;
                case 'store transfer':
                    $store_transfer = $store_request->storeTransfer()->first();
                    $this->transferStore($user_id, $store_transfer->id, $store_transfer->target_id);
                    break;
            }

            $store_request->update([
                'status' => 'approved',
                'evaluated_by' => Auth::user()->id,
            ]);

            event(new StoreRequestApprove($store_request));

            $this->commit();

            return back()
                ->with('message_type', 'success')
                ->with('message_content', 'Request has been approved.');
        } catch (\Exception $e) {
            $this->rollback();
            logger($e);
            return back()
                ->with('message_type', 'danger')
                ->with('message_content', 'Server error.');
        }
    }

    public function rejectRequest($user_id, $requestCode)
    {
        $store_request = StoreRequest::query()
            ->where('user_id', $user_id)
            ->where('code', $requestCode)
            ->where('status', 'pending')
            ->first();

        if ($store_request === null) {
            abort(404);
        }

        $this->authorize('rejectRequest', $store_request);

        try {
            $this->beginTransaction();

            $store_request->update([
                'status' => 'rejected',
                'evaluated_by' => Auth::user()->id,
            ]);

            event(new StoreRequestReject($store_request));

            $this->commit();

            return back()
                ->with('message_type', 'success')
                ->with('message_content', 'Request has been rejected.');
        } catch (\Exception $e) {
            $this->rollback();
            logger($e);
            return back()
                ->with('message_type', 'danger')
                ->with('message_content', 'Server error.');
        }
    }

    public function showAddStoreForm($user_id)
    {
        $this->authorize('addStore', [new Store(), $user_id]);

        return $this->profile
            ->with('content', 'users.profile.stores.requests.application.form')
            ->with('contentData', [
                'form_title' => 'Add Store',
            ]);
    }

    public function showEditStoreForm($user_id, $store_id)
    {
        $store = Store::query()
            ->where('id', $store_id)
            ->where('user_id', $user_id)
            ->first();

        if ($store === null) {
            abort(404);
        }

        $this->authorize('editStore', $store);

        return $this->profile
            ->with('content', 'users.profile.stores.requests.application.form')
            ->with('contentData', [
                'form_title' => 'Edit Store',
                'form_data' => $store,
            ]);
    }

    public function createStoreApplication(Request $request, MapService $map_service, $user_id, $store_id = null)
    {
        $this->authorize('addStoreApplication', [new StoreRequest(), $user_id]);

        // insert store id for name validation to work
        $request->merge(['store_id' => $store_id]);
        $validated_data = $request->validate($this->getStoreApplicationRules());

        try {
            $this->beginTransaction();

            if ($map_service->isValidAddress($validated_data['map_coordinates'], $validated_data['map_address'])) {
                // check if there are any changes in case of store update before proceeding
                if ($store_id !== null) {
                    $store = Store::query()
                        ->where('id', $store_id)
                        ->first();

                    if ($store === null) {
                        abort(404);
                    }

                    $store->fill($validated_data);

                    if ($store->isDirty() === false) {
                        return back()
                            ->with('message_type', 'success')
                            ->with('message_content', 'No changes were made.');
                    }
                }

                // reference number date + user_id + last 4 digit unix timestamp
                $code = date('Ymd').$this->user->id.substr(strtotime('now'), -4);

                $store_request = StoreRequest::query()
                    ->create([
                        'user_id' => $user_id,
                        'code' => $code,
                        'type' => $store_id === null ? 'store creation' : 'store update',
                        'status' => preg_match('/admin/i', Auth::user()->role) ? 'approved' : 'pending',
                        'evaluated_by' => preg_match('/admin/i', Auth::user()->role) ? Auth::user()->id : null,
                    ]);

                $store_application = $store_request->storeApplication()
                    ->create([
                        'request_code' => $code,
                        'store_id' => $store_id,
                        'name' => $validated_data['name'],
                        'contact_number' => $validated_data['contact_number'],
                        'address' => $validated_data['address'],
                        'map_coordinates' => $validated_data['map_coordinates'],
                        'map_address' => $validated_data['map_address'],
                        'open_until' => $validated_data['open_until'],
                        'attachment' => $code.'.pdf',
                    ]);

                if ($store_request->status === 'approved') {
                    if ($store_request->type === 'store creation') {
                        $this->addStore($user_id, $store_application);
                    } elseif ($store_request->type === 'store update') {
                        $store_application->user_id = $store_request->user_id;
                        $this->updateStore($store_application->store_id, $store_application);
                    }
                }

                event(new StoreRequestCreate($store_request));

                $request->file('attachment')->storeAs('attachments', $code.'.pdf');

                $this->commit();

                if ($store_request->status === 'approved') {
                    $redirect = redirect()
                        ->route('user.stores', $user_id)
                        ->with('message_type', 'success');

                    if ($store_request->type === 'store creation') {
                        $redirect->with('message_content', 'Store has been created.');
                    } elseif ($store_request->type === 'store update') {
                        $redirect->with('message_content', 'Store has been updated.');
                    }

                    return $redirect;
                } else {
                    return redirect()
                        ->route('user.stores', $user_id)
                        ->with('message_type', 'success')
                        ->with('message_content', 'Application has been submitted. Please wait for approval. Ref#:'.$code);
                }
            } else {
                $this->rollback();

                return back()
                    ->with('message_type', 'danger')
                    ->with('message_content', 'Invalid map address or location is out of service area.');
            }
        } catch (\Exception $e) {
            $this->rollback();
            logger($e);
            return back()
                ->with('message_type', 'danger')
                ->with('message_content', 'Server error.');
        }
    }

    public function showTransferStoreForm($user_id, $store_id)
    {
        $store = Store::query()
            ->where('user_id', $user_id)
            ->where('id', $store_id)
            ->first();

        if ($store === null) {
            abort(404);
        }

        $this->authorize('transferStore', $store);

        return $this->profile
            ->with('content', 'users.profile.stores.requests.transfer.form')
            ->with('contentData', ['store' => $store]);
    }

    public function createStoreTransfer(Request $request, $user_id, $store_id)
    {
        $store = Store::query()
            ->where('user_id', $user_id)
            ->where('id', $store_id)
            ->first();

        $this->authorize('transferStore', $store);

        $validated_data = $request->validate($this->getStoreTransferRules());

        $target = User::query()
            ->where('email', $validated_data['email'])
            ->first();

        try {
            $this->beginTransaction();

            // check for duplicate request
            $store_request = StoreRequest::query()
                ->where('status', 'pending')
                ->whereHas('storeTransfer', function ($query) use ($store_id) {
                    $query->where('user_id', $store_id);
                })
                ->first();

            if ($store_request !== null) {
                return back()
                    ->with('message_type', 'danger')
                    ->with('message_content', 'A pending store transfer request for this store already exists.');
            }

            // reference number date + user_id + last 4 digit unix timestamp
            $code = date('Ymd').$this->user->id.substr(strtotime('now'), -4);

            $store_request = StoreRequest::query()
                ->create([
                    'user_id' => $user_id,
                    'code' => $code,
                    'type' => 'store transfer',
                    'status' => preg_match('/admin/i', Auth::user()->role) ? 'approved' : 'pending',
                    'evaluated_by' => preg_match('/admin/i', Auth::user()->role) ? Auth::user()->id : null,
                ]);

            $store_transfer = $store_request->storeTransfer()
                ->create([
                    'request_code' => $code,
                    'store_id' => $store->id,
                    'target_id' => $target->id,
                    'attachment' => $code.'.pdf',
                ]);

            if ($store_request->status === 'approved') {
                $this->transferStore($user_id, $store->id, $target->id);
            }

            event(new StoreRequestCreate($store_request));

            $request->file('attachment')->storeAs('attachments', $code.'.pdf');

            $this->commit();

            if ($store_request->status === 'approved') {
                return redirect()
                    ->route('user.stores', $user_id)
                    ->with('message_type', 'success')
                    ->with('message_content', 'Store has been transferred.');
            } else {
                return redirect()
                    ->route('user.stores', $user_id)
                    ->with('message_type', 'success')
                    ->with('message_content', 'Request has been submitted. Please wait for approval. Ref#:'.$code);
            }
        } catch (\Exception $e) {
            $this->rollback();
            logger($e);
            return back()
                ->with('message_type', 'danger')
                ->with('message_content', 'Server error.');
        }
    }
}
