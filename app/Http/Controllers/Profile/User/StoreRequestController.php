<?php
namespace App\Http\Controllers\Profile\User;

use App\Events\AcceptedStoreApplication;
use App\Events\AcceptedStoreTransfer;
use App\Events\AutoAcceptedStoreApplication;
use App\Events\CancelledStoreApplication;
use App\Events\CancelledStoreTransfer;
use App\Events\RejectedStoreApplication;
use App\Events\RejectedStoreTransfer;
use App\Models\Store;
use App\Models\StoreApplication;
use App\Models\StoreTransfer;
use App\Models\User;
use App\Models\StoreRequest;
use App\Services\MapService;
use App\Traits\Validation\HasStoreApplicationValidation;
use App\Traits\Validation\HasStoreTransferValidation;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class StoreRequestController extends StoreController
{
    use HasStoreApplicationValidation, HasStoreTransferValidation;

    public function createApplication(Request $request, MapService $map_service, $user_id)
    {
        if ($request->wantsJson()) {
            $gate = Gate::inspect('create', [new Store(), $user_id]);

            if ($gate->allowed()) {
                $validator = Validator::make($request->all(), $this->getStoreApplicationRules());

                if ($validator->fails()) {
                    return response()->json($validator->errors(), 400);
                }

                $data = $validator->validated();

                if ($map_service->isValidAddress($data['map_coordinates'], $data['map_address'])) {
                    try {
                        $this->beginTransaction();

                        $data['user_id'] = $user_id;
                        $data['ref_no'] = date('Ymd').$this->user->id.substr(strtotime('now'), -4);

                        if ($request->get('store_id')) {
                            $store = Store::query()->find($request->get('store_id'));

                            if ($store === null) {
                                return response()->json('Store not found.', 404);
                            }

                            $data['store_id'] = $request->get('store_id');
                            $store_request = $this->updateStoreApplication(
                                $store,
                                $data,
                                $request->file('attachment'),
                                in_array(Auth::user()->role, ['superadmin', 'admin'])
                            );
                        } else {
                            $store_request = $this->newStoreApplication(
                                $data,
                                $request->file('attachment'),
                                in_array(Auth::user()->role, ['superadmin', 'admin'])
                            );
                        }

                        $this->commit();

                        return response()->json($store_request);
                    } catch (\Exception $e) {
                        $this->rollback();
                        logger($e);
                        return response()->json('Unable to create application. Try again later.', 500);
                    }
                } else {
                    return response()->json('Invalid location or out of service area.', 400);
                }
            } else {
                return response()->json('Forbidden.', 403);
            }
        }
    }

    private function newStoreApplication($data, UploadedFile $attachment, $auto_accept = false)
    {
        $store_request = StoreRequest::query()
            ->create([
                'user_id' => $data['user_id'],
                'ref_no' => $data['ref_no'],
                'category' => 'new_store',
                'status' => $auto_accept ? 'accepted' : 'pending',
                'evaluated_by' => $auto_accept ? Auth::id() : null,
            ]);

        $store_application = StoreApplication::query()
            ->create([
                'ref_no' => $data['ref_no'],
                'name' => $data['name'],
                'contact_number' => $data['contact_number'],
                'address_line' => $data['address_line'],
                'map_coordinates' => $data['map_coordinates'],
                'map_address' => $data['map_address'],
                'open_until' => $data['open_until'],
                'attachment' => $data['ref_no'].'.pdf',
            ]);

        $attachment->move(storage_path('app/public/stores/attachments'), $data['ref_no'].'.pdf');

        if ($auto_accept) {
            $store_application->user_id = $data['user_id'];
            $store = parent::create($store_application);
            $store_request->store_id = $store->id;
            event(new AutoAcceptedStoreApplication($store_request, $store_application));
        } else {
            event(new \App\Events\StoreApplication($store_request, $store_application));
        }

        return $store_request;
    }

    private function updateStoreApplication($store, $data, UploadedFile $attachment, $auto_accept = false)
    {
        $store_request = StoreRequest::query()
            ->create([
                'user_id' => $data['user_id'],
                'ref_no' => $data['ref_no'],
                'category' => 'update_store',
                'status' => $auto_accept ? 'accepted' : 'pending',
                'evaluated_by' => $auto_accept ? Auth::id() : null,
            ]);

        $store_application = StoreApplication::query()
            ->create([
                'ref_no' => $data['ref_no'],
                'store_id' => $data['store_id'],
                'name' => $data['name'],
                'contact_number' => $data['contact_number'],
                'address_line' => $data['address_line'],
                'map_coordinates' => $data['map_coordinates'],
                'map_address' => $data['map_address'],
                'open_until' => $data['open_until'],
                'attachment' => $data['ref_no'].'.pdf',
            ]);

        $attachment->move(storage_path('app/public/stores/attachments'), $data['ref_no'].'.pdf');

        if ($auto_accept) {
            $store_application->user_id = $data['user_id'];
            parent::update($data['store_id'], $store_application);
            $store_request->store_id = $data['store_id'];
            event(new AutoAcceptedStoreApplication($store_request, $store_application));
        } else {
            event(new \App\Events\StoreApplication($store_request, $store_application, $store));
        }

        return $store_request;
    }

    public function search($user_id, Request $request)
    {
        return redirect()
            ->route('user.store-requests', [$user_id, 1, 25, $request->get('keyword')]);
    }

    public function list($user_id, $current_page = 1, $items_per_page = 12, $keyword = null)
    {
        $this->authorize('listOwn', [new StoreRequest(), $user_id]);

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
                $query->whereRaw('MATCH (ref_no, category, status) AGAINST (? IN BOOLEAN MODE)', [$keyword.'*'])
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

        return view('pages.user.store.request.list')
            ->with('requests', $requests)
            ->with('keyword', $keyword)
            ->with('pagination', view('shared.pagination')
                ->with('item_start', $offset + 1)
                ->with('item_end', $requests->count() + $offset)
                ->with('total_count', $total_count)
                ->with('current_page', $current_page)
                ->with('total_pages', ceil($total_count / $items_per_page))
                ->with('items_per_page', $items_per_page)
                ->with('keyword', $keyword)
                ->with('route_name', 'user.store-requests')
                ->with('route_params', [
                    'id' => $user_id,
                    'items_per_page' => $items_per_page,
                ])
            );
    }

    public function view($user_id, $ref_no)
    {
        $store_request = StoreRequest::query()
            ->addSelect(['evaluator_name' => User::query()
                ->whereColumn('id', 'store_requests.evaluated_by')
                ->select('name')
                ->limit(1)
            ])
            ->where('user_id', $user_id)
            ->where('ref_no', $ref_no)
            ->first();

        if ($store_request === null) {
            abort(404);
        }

        $this->authorize('view', $store_request);

        if (in_array($store_request->category, ['new_store', 'update_store'])) {
            $store_request->store = $store_request->storeApplication()->first();

            if ($store_request->category === 'update_store') {
                $store_request->latest = Store::query()->find($store_request->store->store_id);
            }
        } elseif ($store_request->category === 'store_transfer') {
            $store_transfer = $store_request->storeTransfer()->first();

            $store_request->store = Store::query()->find($store_transfer->store_id);
            $store_request->store->attachment = $store_transfer->attachment;
            $store_request->recipient = User::query()->find($store_transfer->target_id);
        }

        return view('pages.user.store.request.details')
            ->with('request', $store_request);
    }

    public function accept(Request $request, $user_id, $ref_no)
    {
        if ($request->wantsJson()) {
            $store_request = StoreRequest::query()
                ->where('user_id', $user_id)
                ->where('ref_no', $ref_no)
                ->where('status', 'pending')
                ->first();

            if ($store_request === null) {
                return response()->json('Application not found.', 404);
            }

            $gate = Gate::inspect('evaluate', $store_request);

            if ($gate->allowed()) {
                try {
                    $this->beginTransaction();

                    $store_request->update([
                        'status' => 'accepted',
                        'evaluated_by' => Auth::id(),
                    ]);

                    switch ($store_request->category) {
                        case 'new_store':
                        case 'update_store':
                            $store_application = $store_request->storeApplication()->first();
                            $store_application->user_id = $user_id;
                            parent::create($store_application);
                            event(new AcceptedStoreApplication($store_request, $store_application));
                            break;
                        case 'store_transfer':
                            $store_transfer = $store_request->storeTransfer()->first();
                            $store_transfer->user_id = $user_id;
                            $store = Store::query()->find($store_transfer->store_id);
                            parent::transfer($user_id, $store_transfer->store_id, $store_transfer->target_id);
                            event(new AcceptedStoreTransfer($store_request, $store_transfer, $store));
                            break;
                    }

                    $this->commit();

                    return response()->json('Application has been accepted.');
                } catch (\Exception $e) {
                    $this->rollback();
                    logger($e);
                    return response()->json('Unable to perform action. Try again later.', 500);
                }
            } else {
                return response()->json('Forbidden.', 403);
            }
        }
    }

    public function reject(Request $request, $user_id, $ref_no)
    {
        if ($request->wantsJson()) {
            $store_request = StoreRequest::query()
                ->where('user_id', $user_id)
                ->where('ref_no', $ref_no)
                ->where('status', 'pending')
                ->first();

            if ($store_request === null) {
                return response()->json('Application not found.', 404);
            }

            $gate = Gate::inspect('evaluate', $store_request);

            if ($gate->allowed()) {
                try {
                    $this->beginTransaction();

                    $store_request->update([
                        'status' => 'rejected',
                        'evaluated_by' => Auth::id(),
                    ]);

                    switch ($store_request->category) {
                        case 'new_store':
                        case 'update_store':
                            $store_application = $store_request->storeApplication()->first();
                            $store_application->user_id = $user_id;
                            event(new RejectedStoreApplication($store_request, $store_application));
                            break;
                        case 'store_transfer':
                            $store_transfer = $store_request->storeTransfer()->first();
                            $store_transfer->user_id = $user_id;
                            $store = Store::query()->find($store_transfer->store_id);
                            event(new RejectedStoreTransfer($store_request, $store_transfer, $store));
                            break;
                    }

                    $this->commit();

                    return response()->json('Application has been rejected.');
                } catch (\Exception $e) {
                    $this->rollback();
                    logger($e);
                    return response()->json('Unable to perform action. Try again later.', 500);
                }
            } else {
                return response()->json('Forbidden.', 403);
            }
        }
    }

    public function cancel(Request $request, $user_id, $ref_no)
    {
        if ($request->wantsJson()) {
            $store_request = StoreRequest::query()
                ->where('user_id', $user_id)
                ->where('ref_no', $ref_no)
                ->where('status', 'pending')
                ->first();

            if ($store_request === null) {
                return response()->json('Application not found.', 404);
            }

            $gate = Gate::inspect('cancel', $store_request);

            if ($gate->allowed()) {
                try {
                    $this->beginTransaction();

                    $store_request->update([
                        'status' => 'cancelled',
                        'evaluated_by' => Auth::id(),
                    ]);

                    switch ($store_request->category) {
                        case 'new_store':
                        case 'update_store':
                            $store_application = $store_request->storeApplication()->first();
                            $store_application->user_id = $user_id;
                            event(new CancelledStoreApplication($store_request, $store_application));
                            break;
                        case 'store_transfer':
                            $store_transfer = $store_request->storeTransfer()->first();
                            $store_transfer->user_id = $user_id;
                            $store = Store::query()->find($store_transfer->store_id);
                            event(new CancelledStoreTransfer($store_request, $store_transfer, $store));
                            break;
                    }

                    $this->commit();

                    return response()->json('Application has been cancelled.');
                } catch (\Exception $e) {
                    $this->rollback();
                    logger($e);
                    return response()->json('Unable to perform action. Try again later.', 500);
                }
            } else {
                return response()->json('Forbidden.', 403);
            }
        }
    }

    public function createStoreTransfer(Request $request, $user_id)
    {
        if ($request->wantsJson()) {
            $store = Store::query()
                ->where('user_id', $user_id)
                ->where('id', $request->get('store_id'))
                ->first();

            $gate = Gate::inspect('transfer', $store);

            if ($gate->allowed()) {
                $validator = Validator::make($request->all(), $this->getStoreTransferRules());

                if ($validator->fails()) {
                    return response()->json($validator->errors(), 400);
                }

                try {
                    $this->beginTransaction();

                    $target = User::query()
                        ->where('email', $validator->validated()['email'])
                        ->whereNull('banned_until')
                        ->first();

                    if ($target === null) {
                        return response()->json('User not found.', 404);
                    } elseif ($target->id === $store->user_id) {
                        return response()->json('Unable to transfer store to self.', 400);
                    }

                    // check for duplicate request
                    $store_request = StoreRequest::query()
                        ->where('status', 'pending')
                        ->whereHas('storeTransfer', function ($query) use ($request) {
                            $query->where('store_id', $request->get('store_id'));
                        })
                        ->first();

                    if ($store_request !== null) {
                        return response()->json('A pending transfer application for this store already exists.', 409);
                    }

                    // reference number date + user_id + last 4 digit unix timestamp
                    $ref_no = date('Ymd').$this->user->id.substr(strtotime('now'), -4);

                    $store_request = StoreRequest::query()
                        ->create([
                            'user_id' => $user_id,
                            'ref_no' => $ref_no,
                            'category' => 'store_transfer',
                            'status' => 'pending',
                            'evaluated_by' => null,
                        ]);

                    $store_transfer = StoreTransfer::query()
                        ->create([
                            'ref_no' => $ref_no,
                            'store_id' => $store->id,
                            'target_id' => $target->id,
                            'attachment' => $ref_no.'.pdf',
                        ]);

                    $request->file('attachment')->move(storage_path('app/public/stores/attachments'), $ref_no.'.pdf');

                    event(new \App\Events\StoreTransfer($store_request, $store_transfer, $store));

                    $this->commit();

                    return response()->json($store_request);
                } catch (\Exception $e) {
                    $this->rollback();
                    logger($e);
                    return response()->json('Unable to transfer store. Try again later.', 500);
                }
            } else {
                return response()->json('Forbidden.', 403);
            }
        }
    }
}
