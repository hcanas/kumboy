<?php

namespace App\Providers;

use App\Models\Store;
use App\Models\StoreRequest;
use App\Models\VerificationCode;
use App\Models\Voucher;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('valid_code', function ($attribute, $value, $parameters, $validator) {
            $email = $validator->getData()[$parameters[0]];

            $verificationCode = VerificationCode::query()
                ->where('email', $email)
                ->where('code', $value)
                ->where('created_at', '<=', now())
                ->where('expires_at', '>=', now())
                ->where('status', 'unused')
                ->first();

            return $verificationCode !== null;
        }, 'The verification code is no longer valid.');

        Validator::extend('contact_number', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^(09[0-9]{2}-?[0-9]{3}-?[0-9]{4})|([0-9]{1,5}-?[0-9]{3}-?[0-9]{4})$/', $value);
        }, 'Invalid format.');

        Validator::extend('store_application', function ($attribute, $value, $parameters, $validator) {
            $store_id = $validator->getData()['store_id'] ?? null;

            if ($store_id === null) {
                // check if store name is already taken
                $store = Store::query()
                    ->where('name', $value)
                    ->first();

                if ($store !== null) {
                    return false;
                }
            } else {
                $store = Store::query()
                    ->where('id', '!=', $store_id)
                    ->where('name', $value)
                    ->first();

                if ($store !== null) {
                    return false;
                }
            }

            $store_request = StoreRequest::query()
                ->where('status', 'pending')
                ->whereHas('storeApplication', function ($query) use ($value, $store_id) {
                    $query->where('name', $value)
                        ->orWhere('store_id', $store_id);
                })
                ->first();

            return $store_request === null;
        }, 'A pending request already exists for this store or the store name is already taken.');

        Validator::extend('product_category', function ($attribute, $value, $parameters, $validator) {
            list($main, $sub) = explode('|', $value);

            $categories = config('system.product_categories');

            if (!isset($categories[$main])) {
                return false;
            }

            if (empty($sub) OR ($sub !== 'all' AND !isset($categories[$main][$sub]))) {
                return false;
            }

            return true;
        }, 'Invalid category.');

        Validator::extend('product_specifications', function ($attribute, $value, $parameters, $validator) {
            $specifications = explode('|', $value);

            foreach ($specifications AS $spec) {
                if (!preg_match('/\w+\:\w+/', str_replace(' ', '', $spec))) {
                    return false;
                }
            }

            return true;
        }, 'Some items have invalid format.');

        Validator::extend('voucher_code', function ($attribute, $value, $parameters, $validator) {
            $id = $validator->getData()['id'];

            $voucher = Voucher::query()
                ->where('code', $value)
                ->first();

            if ($voucher !== null AND empty($id)) {
                return false;
            } elseif ($voucher !== null AND $voucher->id !== (int) $id) {
                return false;
            }

            return true;
        }, 'The code has already been taken.');

        Validator::extend('voucher_amount', function ($attribute, $value, $parameters, $validator) {
            $type = $validator->getData()['type'];

            if ($type === 'Percentage' AND $value > 100) {
                return false;
            }

            return true;
        }, 'Amount is over the allowed range.');
    }
}
