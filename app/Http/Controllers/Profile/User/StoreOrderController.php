<?php
namespace App\Http\Controllers\Profile\User;

use App\Models\Order;
use App\Services\MapService;
use Illuminate\Support\Facades\Auth;

class StoreOrderController extends ProfileController
{
    public function view(MapService $map_service, $user_id, $tracking_number)
    {
        $view = view('users.profile.stores.orders.details');

        $order = Order::query()
            ->with(['items' => function ($query) {
                $query->whereHas('product.store.user', function ($query) {
                        $query->where('id', Auth::id());
                    })
                    ->with('product');
            }])
            ->where('tracking_number', $tracking_number)
            ->first();

        if ($order === null OR $order->items === null) {
            abort(404);
        }

        $view->with('order', $order);

        list($status, $response) = $map_service->getDistanceInKm(
            $order->map_coordinates,
            $order->items->first()->map_coordinates
        );

        if ($status === true) {
            $delivery_fee_rate = config('system.delivery_fee_rate');
            $order->delivery_fee = $delivery_fee_rate * $response;
        } else {
            return $view
                ->with('message_type', 'danger')
                ->with('message_content', 'Failed to compute delivery fee.');
        }

        return $view;
    }
}
