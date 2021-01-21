<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
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
use App\Http\Controllers\Profile\User\UserController as UserProfileController;
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
    Route::get('view-all/{current_page?}/{items_per_page?}/{keyword?}', [UserController::class, 'viewAll'])
        ->name('user.view-all');

    // profile
    Route::middleware('auth')->prefix('{id}')->group(function () {
        // activity log
        Route::post('activities/search', [UserActivityController::class, 'searchActivity'])
            ->name('user.search-activity');
        Route::get('activities/{current_page?}/{items_per_page?}/{keyword?}', [UserActivityController::class, 'viewActivities'])
            ->name('user.activity-log');

        // account settings
        Route::get('account-settings', [UserProfileController::class, 'showAccountSettings'])
            ->name('user.account-settings');
        Route::post('change-name', [UserProfileController::class, 'changeName'])
            ->name('user.change-name');
        Route::post('send-password-reset-code', [UserProfileController::class, 'sendPasswordResetCode']);
        Route::post('change-password', [UserProfileController::class, 'changePassword'])
            ->name('user.change-password');

        // address book
        Route::get('address-book', [UserAddressBookController::class, 'showAddressBook'])
            ->name('user.address-book');
        Route::get('add-address', [UserAddressBookController::class, 'showAddAddressForm'])
            ->name('user.add-address');
        Route::post('add-address', [UserAddressBookController::class, 'addAddress']);
        Route::get('edit-address/{sub_id}', [UserAddressBookController::class, 'showEditAddressForm'])
            ->name('user.edit-address');
        Route::post('edit-address/{sub_id}', [UserAddressBookController::class, 'editAddress']);
        Route::get('delete-address/{sub_id}', [UserAddressBookController::class, 'showDeleteAddressDialog'])
            ->name('user.delete-address');
        Route::post('delete-address/{sub_id}', [UserAddressBookController::class, 'deleteAddress']);

        // store
        Route::get('stores', [UserStoreController::class, 'showStores'])
            ->name('user.stores');
        Route::get('stores/add', [UserStoreRequestController::class, 'showAddStoreForm'])
            ->name('user.add-store');
        Route::post('stores/add', [UserStoreRequestController::class, 'createStoreApplication']);
        Route::get('stores/{sub_id}/edit', [UserStoreRequestController::class, 'showEditStoreForm'])
            ->name('user.edit-store');
        Route::post('stores/{sub_id}/edit', [UserStoreRequestController::class, 'createStoreApplication']);
        Route::get('stores/{sub_id}/transfer', [UserStoreRequestController::class, 'showTransferStoreForm'])
            ->name('user.transfer-store');
        Route::post('stores/{sub_id}/transfer', [UserStoreRequestController::class, 'createStoreTransfer']);
        Route::post('stores/{sub_id}/logo', [UserStoreController::class, 'uploadLogo'])
            ->name('user.upload-store-logo');

        Route::prefix('stores/requests')->group(function () {
            Route::post('search', [UserStoreRequestController::class, 'searchRequest'])
                ->name('user.search-store-request');
            Route::get('{current_page?}/{items_per_page?}/{keyword?}', [UserStoreRequestController::class, 'viewRequests'])
                ->name('user.store-requests');
            Route::get('{code}/view', [UserStoreRequestController::class, 'viewRequestDetails'])
                ->name('user.store-request-details');
            Route::post('{code}/cancel', [UserStoreRequestController::class, 'cancelRequest'])
                ->name('user.cancel-store-request');
            Route::post('{code}/approve', [UserStoreRequestController::class, 'approveRequest'])
                ->name('user.approve-store-request');
            Route::post('reject-request/{code}', [UserStoreRequestController::class, 'rejectRequest'])
                ->name('user.reject-store-request');
        });

        Route::prefix('stores/orders')->group(function () {
            Route::get('{tracking_number}/details', [UserStoreOrderController::class, 'view'])
                ->name('user.store-order-details');
        });

        // notifications
        Route::post('notifications/search', [UserNotificationController::class, 'searchNotification'])
            ->name('user.search-notification');
        Route::get('notifications/{current_page?}/{items_per_page?}/{keyword?}', [UserNotificationController::class, 'viewAll'])
            ->name('user.notifications');
        Route::get('notifications/{notification}/read', [UserNotificationController::class, 'readNotification'])
            ->name('user.read-notification');
        Route::get('notifications/{notification}/view', [UserNotificationController::class, 'viewNotification'])
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
        Route::get('add-product', [StoreProductController::class, 'showAddProductForm'])
            ->name('store.add-product');
        Route::post('add-product', [StoreProductController::class, 'addProduct']);
        Route::get('edit-product/{sub_id}', [StoreProductController::class, 'showEditProductForm'])
            ->name('store.edit-product');
        Route::post('edit-product/{sub_id}', [StoreProductController::class, 'updateProduct']);
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
    });
});

/*
 * Orders Route Group
 */
Route::prefix('orders')->group(function () {
    Route::get('cart', [OrderController::class, 'viewItems'])
        ->name('order.cart');
    Route::post('get-items', [OrderController::class, 'getItems'])
        ->name('order.get-items');
    Route::get('select-address', [OrderController::class, 'showAddressForm'])
        ->name('order.select-address');
    Route::get('user-address-book', [OrderController::class, 'getUserAddressBook'])
        ->name('order.user-address-book');
    Route::post('validate-address', [OrderController::class, 'validateAddress'])
        ->name('order.validate-address');
    Route::get('payment', [OrderController::class, 'showPaymentForm'])
        ->name('order.payment');
    Route::post('create', [OrderController::class, 'placeOrder'])
        ->name('order.create');
    Route::get('complete/{tracking_number}', [OrderController::class, 'complete'])
        ->name('order.complete');

    Route::get('checkout', [OrderController::class, 'checkout'])
        ->name('order.checkout');
});

/*
 * Request Route Group
 */
Route::middleware('auth')->prefix('requests')->group(function () {
    Route::get('count-pending', [RequestController::class, 'countPending'])
        ->name('request.count-pending');
    Route::post('search', [RequestController::class, 'search'])
        ->name('request.search');
    Route::get('{current_page?}/{items_per_page?}/{keyword?}', [RequestController::class, 'viewAll'])
        ->name('request.view-all');
});

/*
 * Notification Route Group
 */
Route::middleware('auth')->prefix('notifications')->group(function () {
    Route::get('count-unread', [NotificationController::class, 'countUnread'])
        ->name('notification.count-unread');
});