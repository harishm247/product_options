<?php

namespace App\Providers;

use App\Http\Composers\CheckoutSubNavComposer;
use App\Http\Composers\DesignWidgetComposer;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //Generate a list of months and future years for use on payment views
        view()->composer([
            'partials.checkout_subnav',
        ], CheckoutSubNavComposer::class);
        // Selected Design Widget
        view()->composer([
            'product'
        ], DesignWidgetComposer::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
