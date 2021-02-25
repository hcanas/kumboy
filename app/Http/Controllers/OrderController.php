<?php
namespace App\Http\Controllers;

use App\Events\OrderPlaced;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\UserAddressBook;
use App\Models\Voucher;
use App\Models\VoucherCode;
use App\Services\MapService;
use App\Traits\Validation\HasUserAddressValidation;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends DatabaseController
{
    use HasUserAddressValidation;

    public function checkout()
    {
        return view('pages.order.checkout');
    }

    public function getItems(Request $request)
    {
        if ($request->wantsJson()) {
            $ids = $request->get('ids', []);

            if (empty($ids)) {
                return response()->json('No items selected.', 400);
            }

            $products = Product::query()
                ->whereIn('id', $ids)
                ->with('store')
                ->with(['specifications' => function ($query) {
                    $query->orderBy('name');
                }])
                ->get();

            if ($products->isEmpty()) {
                return response()->json('No records found.', 404);
            } else {
                return response()->json($products);
            }
        }
    }

    public function validateAddress(Request $request, MapService $map_service)
    {
        if ($request->wantsJson()) {
            $validator = Validator::make($request->all(), $this->getUserAddressRules([
                'contact_person',
                'contact_number',
                'address_line',
                'map_address',
                'map_coordinates',
            ]));

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            if ($map_service->isValidAddress($request->get('map_coordinates'), $request->get('map_address'))) {
                return response()->json($validator->validated());
            } else {
                return response()->json('Invalid map address or location is out of service area.', 400);
            }
        }
    }

    public function getVoucherDetails(Request $request)
    {
        if ($request->wantsJson()) {
            $voucher = Voucher::query()
                ->where('code', $request->get('code'))
                ->whereDate('valid_from', '<=', now())
                ->whereDate('valid_to', '>=', now())
                ->where('status', 'active')
                ->first();

            if ($voucher === null) {
                return response()->json('Unable to use voucher code.', 400);
            }

            return response()->json($voucher);
        }
    }

    public function placeOrder(Request $request, MapService $map_service)
    {
        dd($request->all());
    }

    public function complete($tracking_number)
    {
        $order = Order::query()
            ->where('tracking_number', $tracking_number)
            ->with('items')
            ->first();

        if ($order === null) {
            abort(404);
        }

        return view('orders.complete')
            ->with('order', $order);
    }
}
