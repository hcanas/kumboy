<?php

namespace App\Providers;

use App\Events\AcceptedStoreApplication;
use App\Events\AcceptedStoreTransfer;
use App\Events\AutoAcceptedStoreApplication;
use App\Events\AutoAcceptedStoreTransfer;
use App\Events\CancelledStoreApplication;
use App\Events\CancelledStoreTransfer;
use App\Events\CreatedProduct;
use App\Events\GenericUserActivity;
use App\Events\RejectedStoreApplication;
use App\Events\RejectedStoreTransfer;
use App\Events\StoreApplication;
use App\Events\StoreTransfer;
use App\Events\UpdatedProduct;
use App\Listeners\LogAcceptedStoreApplication;
use App\Listeners\LogAcceptedStoreTransfer;
use App\Listeners\LogAutoAcceptedStoreApplication;
use App\Listeners\LogAutoAcceptedStoreTransfer;
use App\Listeners\LogCancelledStoreApplication;
use App\Listeners\LogCancelledStoreTransfer;
use App\Listeners\LogCreatedProduct;
use App\Listeners\LogGenericUserActivity;
use App\Listeners\LogRejectedStoreApplication;
use App\Listeners\LogRejectedStoreTransfer;
use App\Listeners\LogStoreApplication;
use App\Listeners\LogStoreTransfer;
use App\Listeners\LogUpdatedProduct;
use App\Listeners\NotifyAcceptedStoreApplication;
use App\Listeners\NotifyAcceptedStoreTransfer;
use App\Listeners\NotifyAutoAcceptedStoreTransfer;
use App\Listeners\NotifyRejectedStoreApplication;
use App\Listeners\NotifyRejectedStoreTransfer;
use App\Listeners\NotifyStoreApplication;
use App\Listeners\NotifyStoreTransfer;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        GenericUserActivity::class => [
            LogGenericUserActivity::class,
        ],
        StoreApplication::class => [
            LogStoreApplication::class,
            NotifyStoreApplication::class,
        ],
        AcceptedStoreApplication::class => [
            LogAcceptedStoreApplication::class,
            NotifyAcceptedStoreApplication::class,
        ],
        AutoAcceptedStoreApplication::class => [
            LogAutoAcceptedStoreApplication::class,
        ],
        RejectedStoreApplication::class => [
            LogRejectedStoreApplication::class,
            NotifyRejectedStoreApplication::class,
        ],
        CancelledStoreApplication::class => [
            LogCancelledStoreApplication::class,
        ],
        StoreTransfer::class => [
            LogStoreTransfer::class,
            NotifyStoreTransfer::class,
        ],
        AcceptedStoreTransfer::class => [
            LogAcceptedStoreTransfer::class,
            NotifyAcceptedStoreTransfer::class,
        ],
        AutoAcceptedStoreTransfer::class => [
            LogAutoAcceptedStoreTransfer::class,
            NotifyAutoAcceptedStoreTransfer::class,
        ],
        RejectedStoreTransfer::class => [
            LogRejectedStoreTransfer::class,
            NotifyRejectedStoreTransfer::class,
        ],
        CancelledStoreTransfer::class => [
            LogCancelledStoreTransfer::class,
        ],
        CreatedProduct::class => [
            LogCreatedProduct::class,
        ],
        UpdatedProduct::class => [
            LogUpdatedProduct::class,
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
