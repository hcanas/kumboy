<?php
namespace App\Http\Controllers\Profile\User;

use App\Events\UserAddAddress;
use App\Events\UserDeleteAddress;
use App\Events\UserEditAddress;
use App\Models\UserAddressBook;
use App\Services\MapService;
use App\Traits\Validation\HasUserAddressValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressBookController extends ProfileController
{
    use HasUserAddressValidation;

    public function showAddressBook($user_id)
    {
        $this->authorize('viewAddressBook', [new UserAddressBook(), $user_id]);

        $this->profile->with('content', 'users.profile.address_book.index');

        $user_address_book = UserAddressBook::query()
            ->where('user_id', $user_id)
            ->get();

        return $this->profile->with('contentData', ['addressBook' => $user_address_book]
        );
    }

    public function showAddAddressForm($user_id)
    {
        $this->authorize('addAddress', [new UserAddressBook(), $user_id]);

        return $this->profile
            ->with('content', 'users.profile.address_book.form')
            ->with('contentData', [
                'form_title' => 'Add Address',
            ]);
    }

    public function addAddress($user_id, Request $request, MapService $map_service)
    {
        $this->authorize('addAddress', [new UserAddressBook(), $user_id]);

        $validated_data = $request->validate($this->getUserAddressRules());
        $validated_data['user_id'] = $user_id;

        try {
            $this->beginTransaction();

            if ($map_service->isValidAddress($validated_data['map_coordinates'], $validated_data['map_address'])) {
                $userAddress = UserAddressBook::query()
                    ->create($validated_data);

                event(new UserAddAddress($userAddress));

                $this->commit();

                return redirect()->route('user.address-book', $user_id);
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

    public function showEditAddressForm($user_id, $addressID)
    {
        $userAddress = UserAddressBook::query()
            ->find($addressID);

        if ($userAddress === null) {
            abort(404);
        }

        $this->authorize('edit-address', $userAddress);

        return $this->profile->with('content', 'users.profile.address_book.form')
            ->with('contentData', [
                'form_title' => 'Edit Address',
                'form_data' => $userAddress,
            ]);
    }

    public function editAddress($user_id, $addressID, Request $request, MapService $map_service)
    {
        $userAddress = UserAddressBook::query()
            ->find($addressID);

        if ($userAddress === null) {
            abort(404);
        }

        $this->authorize('editAddress', $userAddress);

        $validated_data = $request->validate($this->getUserAddressRules());
        $validated_data['user_id'] = $user_id;

        try {
            $this->beginTransaction();

            if ($map_service->isValidAddress($validated_data['map_coordinates'], $validated_data['map_address'])) {
                $userAddress->fill($validated_data);
                $oldAddress = $userAddress->getOriginal();
                $userAddress->save();

                event(new UserEditAddress($userAddress, $oldAddress));

                $this->commit();

                return back()
                    ->with('message_type', 'success')
                    ->with('message_content', $userAddress->wasChanged()
                        ? 'Address has been changed.'
                        : 'No changes made.'
                    );
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

    public function showDeleteAddressDialog($user_id, $addressID)
    {
        $address = UserAddressBook::query()
            ->find($addressID);

        if ($address === null) {
            abort(404);
        }

        $this->authorize('deleteAddress', $address);

        return $this->profile
            ->with('content', 'users.profile.address_book.delete_dialog')
            ->with('contentData', [
                'address' => $address,
            ]);
    }

    public function deleteAddress($user_id, $addressID)
    {
        $userAddress = UserAddressBook::query()
            ->find($addressID);

        if ($userAddress === null) {
            abort(404);
        }

        $this->authorize('deleteAddress', $userAddress);

        try {
            $this->beginTransaction();

            $userAddress->delete();

            event(new UserDeleteAddress($userAddress));

           $this->commit();

            return redirect()
                ->route('user.address-book', $user_id)
                ->with('message_type', 'success')
                ->with('message_content', 'Address has been deleted.');
        } catch (\Exception $e) {
            $this->rollback();
            logger($e);
            return redirect()
                ->route('user.address-book', $user_id)
                ->with('message_type', 'danger')
                ->with('message_content', 'Server error.');
        }
    }
}
