<?php

namespace App\Providers;

use App\Events\ApproveStoreApplication;
use App\Events\CancelStoreApplication;
use App\Events\RejectStoreApplication;
use App\Events\StoreRequestCreate;
use App\Events\UserAddAddress;
use App\Events\UserChangeName;
use App\Events\UserChangePassword;
use App\Events\UserDeleteAddress;
use App\Events\UserEditAddress;
use App\Events\UserLogin;
use App\Events\UserLogout;
use App\Events\StoreRequestApprove;
use App\Events\StoreRequestCancel;
use App\Events\StoreRequestReject;
use App\Listeners\LogApproveStoreApplication;
use App\Listeners\LogCancelStoreApplication;
use App\Listeners\LogRejectStoreApplication;
use App\Listeners\LogStoreRequestCreate;
use App\Listeners\LogUserAddAddress;
use App\Listeners\LogUserChangeName;
use App\Listeners\LogUserChangePassword;
use App\Listeners\LogUserDeleteAddress;
use App\Listeners\LogUserEditAddress;
use App\Listeners\LogUserLogin;
use App\Listeners\LogUserLogout;
use App\Listeners\LogStoreRequestApprove;
use App\Listeners\LogStoreRequestCancel;
use App\Listeners\LogStoreRequestReject;
use App\Listeners\NotifyStoreRequestApprove;
use App\Listeners\NotifyStoreRequestCreate;
use App\Listeners\NotifyStoreRequestReject;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UserLogin::class => [
            LogUserLogin::class,
        ],
        UserLogout::class => [
            LogUserLogout::class,
        ],
        UserChangeName::class => [
            LogUserChangeName::class,
        ],
        UserChangePassword::class => [
            LogUserChangePassword::class,
        ],
        UserAddAddress::class => [
            LogUserAddAddress::class,
        ],
        UserEditAddress::class => [
            LogUserEditAddress::class,
        ],
        UserDeleteAddress::class => [
            LogUserDeleteAddress::class,
        ],
        StoreRequestCancel::class => [
            LogStoreRequestCancel::class,
        ],
        StoreRequestApprove::class => [
            LogStoreRequestApprove::class,
            NotifyStoreRequestApprove::class,
        ],
        StoreRequestReject::class => [
            LogStoreRequestReject::class,
            NotifyStoreRequestReject::class,
        ],
        StoreRequestCreate::class => [
            LogStoreRequestCreate::class,
            NotifyStoreRequestCreate::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
