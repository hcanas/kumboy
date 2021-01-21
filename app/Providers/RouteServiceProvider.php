<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });

        // lists
        Route::pattern('current_page', '[0-9]+');
        Route::pattern('items_per_page', '[0-9]+');

        // products
        $categories = config('system.product_categories');
        $main_categories = implode('|', array_keys($categories));
        $sub_categories = rtrim(implode('|', array_map(function ($cat) {
            return implode('|', array_keys($cat));
        }, $categories)), '|');

        Route::pattern('price_from', '[0-9]+');
        Route::pattern('price_to', '[0-9]+');
        Route::pattern('main_category', '(all|'.$main_categories.')');
        Route::pattern('sub_category', '(all|'.$sub_categories.')');
        Route::pattern('sort_by', '(name|price|sold)');
        Route::pattern('sort_dir', '(asc|desc)');

        // general
        Route::pattern('id', '[0-9]+');
        Route::pattern('sub_id', '[0-9]+');
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
