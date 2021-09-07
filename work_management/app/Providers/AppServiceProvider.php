<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Librarys\php\OutputLogClass;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //ログクラスを登録する
        app()->bind('OutputLogClass', OutputLogClass::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
