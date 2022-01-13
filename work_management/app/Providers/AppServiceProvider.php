<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Libraries\php\Service\OutputLogClass;
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
            $client_id = $app->request->input('client_id', 'kk00000001');
            //クライアントIDが10文字であるか調べる。
            if(mb_strlen($client_id) == 10){
                //先頭2文字がアルファベットで、残り八ケタが数字出なければ、仮の値
                if(!preg_match("/^[a-zA-Z]{2}[0-9]{8}$/", $client_id)){
                    $client_id = 'kk00000001';
                }
            }else{
                //十文字でなければ、仮の値
                $client_id = 'kk00000001';
            }

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
