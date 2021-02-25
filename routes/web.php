<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Profile\Store\VoucherController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\Profile\Product\ProfileController as ProductProfileController;
use App\Http\Controllers\Profile\Store\ProductController as StoreProductController;
use App\Http\Controllers\Profile\User\ActivityController as UserActivityController;
use App\Http\Controllers\Profile\User\AddressBookController as UserAddressBookController;
use App\Http\Controllers\Profile\User\NotificationController as UserNotificationController;
use App\Http\Controllers\Profile\User\StoreOrderController as UserStoreOrderController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\Profile\User\StoreRequestController as UserStoreRequestController;
use App\Http\Controllers\Profile\User\StoreController as UserStoreController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Profile\User\UserController as AccountSettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
 * Home Route Group
 */
Route::get('/', [HomeController::class, 'index'])->name('home');

/*
 * Auth Route Group
 */
Route::prefix('google')->group(function () {
    Route::get('redirect', [AuthController::class, 'redirectToGoogle'])
        ->name('google.login');
    Route::get('callback', [AuthController::class, 'handleGoogleCallback']);
});
Route::prefix('auth')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])
        ->name('login');
    Route::post('login', [AuthController::class, 'login'])
        ->middleware('throttle:login');
    Route::get('logout', [AuthController::class, 'logout'])
        ->name('logout');
});

/*
 * User Route Group
 */
Route::prefix('users')->group(function () {
    Route::get('register', [UserController::class, 'showRegistrationForm'])
        ->name('user.register');
    Route::post('register', [UserController::class, 'register']);

    Route::get('forgot-password', [UserController::class, 'showPasswordResetForm'])
        ->name('user.password-reset');
    Route::post('forgot-password', [UserController::class, 'resetPassword']);

    Route::post('find-email', [UserController::class, 'findByEmail'])
        ->name('user.find-email');
    Route::post('send-email-verification-code', [UserController::class, 'sendEmailVerificationCode']);
    Route::post('send-password-reset-code', [UserController::class, 'sendPasswordResetCode']);

    Route::post('search', [UserController::class, 'search'])
        ->name('user.search');
    Route::get('view-all/{current_page?}/{items_per_page?}/{keyword?}', [UserController::class, 'list'])
        ->name('user.list');

    // profile
    Route::middleware('auth')->prefix('{id}')->group(function () {
        // activity log
        Route::post('activity-log/search', [UserActivityController::class, 'search'])
            ->name('user.search-activity');
        Route::get('activity-log/{current_page?}/{items_per_page?}/{keyword?}', [UserActivityController::class, 'list'])
            ->name('user.activity-log');

        // account settings
        Route::get('account-settings', [AccountSettingsController::class, 'showSettings'])
            ->name('user.account-settings');
        Route::post('change-name', [AccountSettingsController::class, 'changeName'])
            ->name('user.change-name');
        Route::post('send-password-reset-code', [AccountSettingsController::class, 'sendPasswordResetCode'])
            ->name('user.request-password-reset-code');
        Route::post('change-password', [AccountSettingsController::class, 'changePassword'])
            ->name('user.change-password');

        // address book
        Route::get('address-book', [UserAddressBookController::class, 'list'])
            ->name('user.address-book');
        Route::post('save-address', [UserAddressBookController::class, 'save'])
            ->name('user.save-address');
        Route::post('delete-address', [UserAddressBookController::class, 'delete'])
            ->name('user.delete-address');

        // store
        Route::get('stores', [UserStoreController::class, 'list'])
            ->name('user.stores');
        Route::post('stores/{sub_id}/upload-logo', [UserStoreController::class, 'uploadLogo'])
            ->name('user.upload-store-logo');

        Route::prefix('stores/requests')->group(function () {
            Route::post('create-application', [UserStoreRequestController::class, 'createApplication'])
                ->name('user.new-store-application');
            Route::post('create-store-transfer', [UserStoreRequestController::class, 'createStoreTransfer'])
                ->name('user.new-store-transfer');
            Route::post('search', [UserStoreRequestController::class, 'search'])
                ->name('user.search-store-request');
            Route::get('{current_page?}/{items_per_page?}/{keyword?}', [UserStoreRequestController::class, 'list'])
                ->name('user.store-requests');
            Route::get('{ref_no}/view', [UserStoreRequestController::class, 'view'])
                ->name('user.store-request-details');
            Route::post('{ref_no}/cancel', [UserStoreRequestController::class, 'cancel'])
                ->name('user.cancel-store-request');
            Route::post('{ref_no}/accept', [UserStoreRequestController::class, 'accept'])
                ->name('user.accept-store-request');
            Route::post('{ref_no}/reject', [UserStoreRequestController::class, 'reject'])
                ->name('user.reject-store-request');
        });

        Route::prefix('stores/orders')->group(function () {
            Route::get('{tracking_number}/details', [UserStoreOrderController::class, 'view'])
                ->name('user.store-order-details');
        });

        // notifications
        Route::post('notifications/search', [UserNotificationController::class, 'search'])
            ->name('user.search-notification');
        Route::get('notifications/{current_page?}/{items_per_page?}/{keyword?}', [UserNotificationController::class, 'list'])
            ->name('user.notifications');
        Route::get('notifications/{notification}/read', [UserNotificationController::class, 'read'])
            ->name('user.read-notification');
        Route::get('notifications/{notification}/view', [UserNotificationController::class, 'view'])
            ->name('user.view-notification');
    });
});

/*
 * Store Route Group
 */
Route::prefix('stores')->group(function () {
    Route::post('search', [StoreController::class, 'search'])
        ->name('store.search');
    Route::get('{current_page?}/{items_per_page?}/{keyword?}', [StoreController::class, 'index'])
        ->name('store.list');

    Route::prefix('{id}')->group(function () {
        Route::post('search', [StoreProductController::class, 'search'])
            ->name('store.search-products');
        Route::get('products'
            .'/{current_page?}'
            .'/{items_per_page?}'
            .'/{price_from?}'
            .'/{price_to?}'
            .'/{main_category?}'
            .'/{sub_category?}'
            .'/{sort_by?}'
            .'/{sort_dir?}'
            .'/{keyword?}',
            [StoreProductController::class, 'index']
        )->name('store.products');
        Route::post('add-product', [StoreProductController::class, 'create'])
            ->name('store.add-product');

        Route::prefix('vouchers')->group(function () {
            Route::post('search', [VoucherController::class, 'search'])
                ->name('store.search-vouchers');
            Route::get('{current_page?}/{items_per_page?}/{keyword?}', [VoucherController::class, 'list'])
                ->name('store.vouchers');
            Route::post('create', [VoucherController::class, 'create'])
                ->name('store.add-voucher');
            Route::post('update', [VoucherController::class, 'update'])
                ->name('store.update-voucher');
            Route::post('activate', [VoucherController::class, 'activate'])
                ->name('store.activate-voucher');
            Route::post('deactivate', [VoucherController::class, 'deactivate'])
                ->name('store.deactivate-voucher');
        });
    });
});

/*
 * Shop Route Group
 */
Route::prefix('shop')->group(function () {
    Route::post('search', [ShopController::class, 'search'])
        ->name('shop.search');
    Route::get('{current_page?}'
            .'/{items_per_page?}'
            .'/{price_from?}'
            .'/{price_to?}'
            .'/{main_category?}'
            .'/{sub_category?}'
            .'/{sort_by?}'
            .'/{sort_dir?}'
            .'/{keyword?}',
            [ShopController::class, 'index'])
        ->name('shop');
});

/*
 * Product Route Group
 */
Route::prefix('products')->group(function () {
    Route::prefix('{id}')->group(function () {
        Route::get('info', [ProductProfileController::class, 'index'])
            ->name('product.info');
        Route::post('edit', [ProductProfileController::class, 'update'])
            ->name('product.edit');
    });
});

/*
 * Orders Route Group
 */
Route::prefix('orders')->group(function () {
    Route::get('checkout', [OrderController::class, 'checkout'])
        ->name('order.checkout');
    Route::post('get-items', [OrderController::class, 'getItems'])
        ->name('order.get-items');
    Route::get('user-address-book', [OrderController::class, 'getUserAddressBook'])
        ->name('order.user-address-book');
    Route::post('validate-address', [OrderController::class, 'validateAddress'])
        ->name('order.validate-address');
    Route::post('create', [OrderController::class, 'placeOrder'])
        ->name('order.create');
    Route::get('complete/{tracking_number}', [OrderController::class, 'complete'])
        ->name('order.complete');
    Route::post('voucher-details', [OrderController::class, 'getVoucherDetails'])
        ->name('order.voucher-details');
});

/*
 * Request Route Group
 */
Route::middleware('auth')->prefix('requests')->group(function () {
    Route::get('count-pending', [RequestController::class, 'countPending'])
        ->name('request.count-pending');
    Route::post('search', [RequestController::class, 'search'])
        ->name('request.search');
    Route::get('{current_page?}/{items_per_page?}/{keyword?}', [RequestController::class, 'list'])
        ->name('request.list');
});

/*
 * Notification Route Group
 */
Route::middleware('auth')->prefix('notifications')->group(function () {
    Route::get('count-unread', [NotificationController::class, 'countUnread'])
        ->name('notification.count-unread');
});