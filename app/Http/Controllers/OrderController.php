<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\UserAddressBook;
use App\Models\VoucherCode;
use App\Services\MapService;
use App\Traits\Validation\HasUserAddressValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends DatabaseController
{
    use HasUserAddressValidation;

    public function viewItems()
    {
        return view('orders.cart');
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
                ->with('vendor')
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

    public function showAddressForm()
    {
        $address_book = UserAddressBook::query()
            ->where('user_id', Auth::check() ? Auth::id() : null)
            ->get();

        return view('orders.address')
            ->with('address_book', $address_book->isNotEmpty() ? $address_book : []);
    }

    public function validateAddress(Request $request, MapService $map_service)
    {
        if ($request->wantsJson()) {
            $validator = Validator::make($request->all(), $this->getUserAddressRules([
                'contact_person',
                'contact_number',
                'address',
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

    public function showPaymentForm()
    {
        return view('orders.payment');
    }

    public function placeOrder(Request $request, MapService $map_service)
    {
        // items array indexes are product ids, values are corresponding quantities
        $items = $request->get('items');
        $voucher_code = $request->get('voucher_code');

        $validator = Validator::make($request->all(), $this->getUserAddressRules([
            'contact_person',
            'contact_number',
            'address',
            'map_address',
            'map_coordinates',
        ]));

        if ($validator->fails()) {
            return back()
                ->with('message_type', 'danger')
                ->with('message_content', 'Invalid address information.');
        }

        if ($map_service->isValidAddress($request->get('map_coordinates'), $request->get('map_address')) === false) {
            return back()
                ->with('message_type', 'danger')
                ->with('message_content', 'Invalid map address or out of service area.');
        }

        if ($items !== null AND is_array($items)) {
            foreach ($items AS $k => $v) {
                if (ctype_digit($k) === false OR ctype_digit($v) === false) {
                    return back()
                        ->with('message_type', 'danger')
                        ->with('message_content', 'Invalid request.');
                }
            }

            if ($voucher_code !== null) {
                $voucher_code = VoucherCode::query()
                    ->where('code', $voucher_code)
                    ->first();

                if ($voucher_code === null) {
                    return back()
                        ->with('message_type', 'danger')
                        ->with('message_content', 'Invalid voucher code.');
                }
            }

            try {
                $this->beginTransaction();

                $products = Product::query()
                    ->whereIn('id', array_keys($items))
                    ->with('vendor')
                    ->with(['specifications' => function ($query) {
                        $query->orderBy('name');
                    }])
                    ->lockForUpdate()
                    ->get();

                // date + random bytes to decimal
                $tracking_number = date('Ymd', strtotime('now')).hexdec(bin2hex(random_bytes(2)));

                $order = Order::query()
                    ->create([
                        'user_id' => Auth::check() ? Auth::id() : null,
                        'tracking_number' => $tracking_number,
                        'contact_person' => $validator->validated()['contact_person'],
                        'contact_number' => $validator->validated()['contact_number'],
                        'address' => $validator->validated()['address'],
                        'map_address' => $validator->validated()['map_address'],
                        'map_coordinates' => $validator->validated()['map_coordinates'],
                    ]);

                foreach ($products AS $product) {
                    if ($product->qty < $items[$product->id]) {
                        $this->rollback();
                        return back()
                            ->with('message_type', 'danger')
                            ->with('message_content', 'Some products have insufficient stock.');
                    }

                    $product->update(['qty' => $product->qty - $items[$product->id]]);

                    $specifications = '';
                    foreach ($product->specifications AS $specification) {
                        $specifications .= ','.$specification['name'].':'.$specification['value'];
                    }
                    $specifications = ltrim($specifications, ',');

                    $order->items()->create([
                        'order_id' => $order->id,
                        'name' => $product->name,
                        'specifications' => $specifications,
                        'qty' => $items[$product->id],
                        'price' => $product->price,
                        'status' => 'pending',
                    ]);
                }

                $this->commit();

                return redirect()
                    ->route('order.complete', $tracking_number);
            } catch (\Exception $e) {
                logger($e);
                $this->rollback();
                return back()
                    ->with('message_type', 'danger')
                    ->with('message_content', 'Server error. Try again later.');
            }
        } else {
            return back()
                ->with('message_type', 'danger')
                ->with('message_content', 'Invalid request.');
        }
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
