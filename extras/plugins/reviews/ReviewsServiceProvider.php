<?php

namespace extras\plugins\reviews;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Larapen\Admin\RoutesCrud;

class ReviewsServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Load plugin views
        $this->loadViewsFrom(realpath(__DIR__ . '/resources/views'), 'reviews');

        // Load plugin languages files
		$this->loadTranslationsFrom(realpath(__DIR__ . '/resources/lang'), 'reviews');

        $this->registerAdminMiddleware($this->app->router);
        $this->setupRoutes($this->app->router);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('reviews', function ($app) {
            return new Reviews($app);
        });
    }

    /**
     * Define the routes for the application.
     *
     * @param Router $router
     */
    public function setupRoutes(Router $router)
    {
        // Front
        $router->group([
//            'prefix'     => \LaravelLocalization::setLocale(),
            'middleware' => ['web', 'localize', 'localizationRedirect', 'localeSessionRedirect'],
            'namespace' => 'extras\plugins\reviews\app\Http\Controllers'
        ], function ($router)
        {
            $router->pattern('id', '[0-9]+');
            Route::post('post/{id}/review/create', 'ReviewController@create');
            Route::get('review/delete/{id}', 'ReviewController@delete');
        });

        // Admin
        $router->group(['namespace' => 'extras\plugins\reviews\app\Http\Controllers\Admin'], function ($router) {
            Route::group([
                'middleware' => ['admin', 'bannedUser'],
                'prefix'     => config('larapen.admin.route_prefix', 'admin'),
            ], function () {
				RoutesCrud::resource('reviews', 'ReviewController');
            });
        });
    }

    public function registerAdminMiddleware(Router $router)
    {
		Route::aliasMiddleware('admin', \App\Http\Middleware\Admin::class);
		Route::aliasMiddleware('bannedUser', \App\Http\Middleware\BannedUser::class);
	
		Route::aliasMiddleware('localize', \Larapen\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class);
		Route::aliasMiddleware('localizationRedirect', \Larapen\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class);
		Route::aliasMiddleware('localeSessionRedirect', \Larapen\LaravelLocalization\Middleware\LocaleSessionRedirect::class);
    }
}
