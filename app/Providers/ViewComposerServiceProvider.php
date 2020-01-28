<?php

namespace App\Providers;

use App\Http\Composers\DesignWidgetComposer;

use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap our view composers
     *
     * @return void
     */
    public function boot()
    {
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
