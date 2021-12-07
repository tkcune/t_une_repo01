<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Libraries\php\OutputLogClass;
use Illuminate\Support\Facades\Config;

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
        app()->bind('OutputLogClass', function($app){
            //@var string クライアントのid、リクエストになければ、仮の値
            $client_id = $app->request->input('client_id', 'kk00000000');

            //@var string ユーザーのメールアドレス
            $user = $app->request->input('user', 'august@roma.com');

            //@var string デバックモードの値
            $debug_mode = '0';
            //config/config.phpがなければ、0
            if(Config::get('config') == null){
                $debug_mode = '0';
            }else{
                //debug_modeの記載がなければ、0
                if(!array_key_exists('debug_mode', Config::get('config'))){
                    $debug_mode = '0';
                }else{
                    //config.phpがあり、debug_modeがあれば、取得する
                    $debug_mode = Config::get('config')['debug_mode'];
                }
            }
            
            //インスタンスを作成し、返す
            return new OutputLogClass($client_id, $user, $debug_mode);
        });
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
