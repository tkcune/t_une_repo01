<?php

namespace App\Providers;

use App\Libraries\php\DatabaseException;
use Illuminate\Support\ServiceProvider;

class DatabaseExceptionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('exception', function () {
            return new DatabaseException();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
