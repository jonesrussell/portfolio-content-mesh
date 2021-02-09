<?php

namespace App\Repositories\Form;

use Illuminate\Support\ServiceProvider;

class FormRepoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->bind(
            'App\Repositories\FormInterface',
            'App\Repositories\FormRepository'
        );
    }
}
