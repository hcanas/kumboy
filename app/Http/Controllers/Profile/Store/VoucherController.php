<?php

namespace App\Http\Controllers\Profile\Store;

use App\Events\CreatedVoucher;
use App\Events\UpdatedVoucher;
use App\Models\Store;
use App\Models\Voucher;
use App\Traits\Validation\HasVoucherValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class VoucherController extends ProfileController
{
    use HasVoucherValidation;

    public function search(Request $request, $store_id)
    {
        return redirect()
            ->route('store.vouchers', [
                'id' => $store_id,
                'current_page' => 1,
                'items_per_page' => 24,
                'keyword' => $request->get('keyword'),
            ]);
    }

    public function list(Request $request, $store_id, $current_page = 1, $items_per_page = 12, $keyword = null)
    {
        $offset = ($current_page - 1) * $items_per_page;
        $store = Store::query()->find($store_id);

        if ($store === null) {
            abort(404);
        }

        $query = Voucher::query()
            ->where('store_id', $store_id);

        if (!Auth::check() OR Auth::id() !== $store->user_id) {
            $query->where('status', 'active');
        }

        if (!empty($keyword)) {
            $query->whereRaw('MATCH (code, categories) AGAINST (? IN BOOLEAN MODE)', [$keyword.'*']);
        }

        $total_count = $query->count();

        $vouchers = $query->skip($offset)
            ->limit($items_per_page)
            ->orderBy('valid_to', 'desc')
            ->get();

        return view('pages.store.profile.vouchers')
            ->with('vouchers', $vouchers)
            ->with('keyword', $keyword)
            ->with('pagination', view('partials.pagination')
                ->with('item_start', $offset + 1)
                ->with('item_end', $vouchers->count() + $offset)
                ->with('total_count', $total_count)
                ->with('current_page', $current_page)
                ->with('total_pages', ceil($total_count / $items_per_page))
                ->with('items_per_page', $items_per_page)
                ->with('keyword', $keyword)
                ->with('route_name', 'store.vouchers')
                ->with('route_params', $request->route()->parameters)
            );
    }

    public function create(Request $request, $store_id)
    {
        if ($request->wantsJson()) {
            $store = Store::query()->find($store_id);

            if ($store === null) {
                return response()->json('Store not found.', 404);
            }

            $gate = Gate::inspect('manage', [new Voucher(), $store->user_id]);

            if ($gate->allowed()) {
                $validator = Validator::make($request->all(), $this->getVoucherRules());

                if ($validator->fails()) {
                    return response()->json($validator->errors(), 400);
                }

                try {
                    $this->beginTransaction();

                    $voucher = Voucher::query()
                        ->create([
                            'store_id' => $store->id,
                            'code' => $request->get('code'),
                            'amount' => $request->get('amount'),
                            'type' => $request->get('type'),
                            'categories' => $request->get('categories'),
                            'limit_per_user' => $request->get('limit_per_user'),
                            'qty' => $request->get('qty'),
                            'valid_from' => $request->get('valid_from'),
                            'valid_to'=> $request->get('valid_to'),
                            'status' => 'active',
                        ]);

                    event(new CreatedVoucher($voucher, $store));

                    $this->commit();

                    return response()->json($voucher);
                } catch (\Exception $e) {
                    $this->rollback();
                    logger($e);
                    return response()->json('Unable to create voucher. Try again later.', 500);
                }
            } else {
                return response()->json('Forbidden.', 403);
            }
        }
    }

    public function update(Request $request, $store_id)
    {
        if ($request->wantsJson()) {
            $store = Store::query()->find($store_id);

            if ($store === null) {
                return response()->json('Store not found.', 404);
            }

            $gate = Gate::inspect('manage', [new Voucher(), $store->user_id]);

            if ($gate->allowed()) {
                $validator = Validator::make($request->all(), $this->getVoucherRules());

                if ($validator->fails()) {
                    return response()->json($validator->errors(), 400);
                }

                try {
                    $this->beginTransaction();

                    $voucher = Voucher::query()->find($request->get('id'));

                    if ($voucher === null) {
                        return response()->json('Voucher not found.', 404);
                    }

                    $voucher->update([
                        'code' => $request->get('code'),
                        'amount' => $request->get('amount'),
                        'type' => $request->get('type'),
                        'categories' => $request->get('categories'),
                        'limit_per_user' => $request->get('limit_per_user'),
                        'qty' => $request->get('qty'),
                        'valid_from' => $request->get('valid_from'),
                        'valid_to' => $request->get('valid_to'),
                    ]);

                    event(new UpdatedVoucher($voucher, $store));

                    $this->commit();

                    return response()->json($voucher);
                } catch (\Exception $e) {
                    $this->rollback();
                    logger($e);
                    return response()->json('Unable to update voucher. Try again later.', 500);
                }
            } else {
                return response()->json('Forbidden.', 403);
            }
        }
    }

    public function activate(Request $request, $store_id)
    {
        if ($request->wantsJson()) {
            $store = Store::query()->find($store_id);

            if ($store === null) {
                return response()->json('Store not found.', 404);
            }

            $gate = Gate::inspect('manage', [new Voucher(), $store->user_id]);

            if ($gate->allowed()) {
                try {
                    $this->beginTransaction();

                    $voucher = Voucher::query()->find($request->get('id'));

                    if ($voucher === null) {
                        return response()->json('Voucher not found.', 404);
                    }

                    if ($request->get('status') !== 'active') {
                        return response()->json('Invalid status.', 400);
                    }

                    $voucher->update([
                        'status' => $request->get('status'),
                    ]);

                    event(new UpdatedVoucher($voucher, $store));

                    $this->commit();

                    return response()->json($voucher);
                } catch (\Exception $e) {
                    $this->rollback();
                    logger($e);
                    return response()->json('Unable to activate voucher. Try again later.', 500);
                }
            } else {
                return response()->json('Forbidden.', 403);
            }
        }
    }

    public function deactivate(Request $request, $store_id)
    {
        if ($request->wantsJson()) {
            $store = Store::query()->find($store_id);

            if ($store === null) {
                return response()->json('Store not found.', 404);
            }

            $gate = Gate::inspect('manage', [new Voucher(), $store->user_id]);

            if ($gate->allowed()) {
                try {
                    $this->beginTransaction();

                    $voucher = Voucher::query()->find($request->get('id'));

                    if ($voucher === null) {
                        return response()->json('Voucher not found.', 404);
                    }

                    if ($request->get('status') !== 'inactive') {
                        return response()->json('Invalid status.', 400);
                    }

                    $voucher->update([
                        'status' => $request->get('status'),
                    ]);

                    event(new UpdatedVoucher($voucher, $store));

                    $this->commit();

                    return response()->json($voucher);
                } catch (\Exception $e) {
                    $this->rollback();
                    logger($e);
                    return response()->json('Unable to deactivate voucher. Try again later.', 500);
                }
            } else {
                return response()->json('Forbidden.', 403);
            }
        }
    }
}
