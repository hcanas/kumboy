<?php
namespace App\Http\Controllers\Profile\User;

use App\Events\GenericUserActivity;
use App\Models\UserAddressBook;
use App\Services\MapService;
use App\Traits\Validation\HasUserAddressValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class AddressBookController extends ProfileController
{
    use HasUserAddressValidation;

    public function list($user_id)
    {
        $this->authorize('manage', [new UserAddressBook(), $user_id]);

        $address_book = UserAddressBook::query()
            ->where('user_id', $user_id)
            ->get();

        return view('pages.user.address.list')
            ->with('list', $address_book);
    }

    public function save($user_id, Request $request, MapService $map_service)
    {
        if ($request->wantsJson()) {
            $gate = Gate::inspect('manage', [new UserAddressBook(), $user_id]);

            if ($gate->allowed()) {
                $validator = Validator::make($request->all(), $this->getUserAddressRules());

                if ($validator->fails()) {
                    return response()->json($validator->errors(), 400);
                }

                if ($map_service->isValidAddress($request->get('map_coordinates'), $request->get('map_address'))) {
                    try {
                        $this->beginTransaction();

                        if ($request->get('id')) {
                            $user_address = UserAddressBook::query()->find($request->get('id'));

                            if ($user_address === null) {
                                return response()->json('User address not found.', 404);
                            }

                            $old_label = $user_address->label;
                            $user_address->update($validator->validated());

                            if ($old_label === $user_address->label) {
                                $activity = 'Updated "'.$old_label.'" address.';
                            } else {
                                $activity = 'Updated "'.$user_address->label.'" (prev. '.$old_label.') address.';
                            }

                            event(new GenericUserActivity($activity));
                        } else {
                            $user_address = UserAddressBook::query()
                                ->create(array_merge(['user_id' => $user_id], $validator->validated()));

                            event(new GenericUserActivity('Added '.$user_address->label.' address.'));
                        }

                        $this->commit();

                        return response()->json($user_address);
                    } catch (\Exception $e) {
                        $this->rollback();
                        logger($e);
                        return response()->json('Unable to add address, try again later.', 500);
                    }
                } else {
                    return response()->json('Invalid map address or location is out of service area.', 400);
                }
            } else {
                return response()->json('Forbidden.', 403);
            }
        }
    }

    public function delete(Request $request, $user_id)
    {
        if ($request->wantsJson()) {
            $user_address = UserAddressBook::query()->find($request->get('id'));

            if ($user_address === null) {
                return response()->json('User address not found.', 404);
            }

            $gate = Gate::inspect('delete', $user_address);

            if ($gate->allowed()) {
                try {
                    $this->beginTransaction();

                    $user_address->delete();
                    event(new GenericUserActivity('Deleted "'.$user_address->label.'" address.'));

                    $this->commit();

                    return response($user_address->label.' address has been deleted.');
                } catch (\Exception $e) {
                    $this->rollback();
                    logger($e);
                    return response()->json('Unable to delete address. Try again later.', 500);
                }
            } else {
                return response()->json('Forbidden.', 403);
            }
        }
    }
}
