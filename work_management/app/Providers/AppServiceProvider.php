<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Libraries\php\OutputLogClass;
use Illuminate\Support\Facades\DB;

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
            $user = $app->request->input('user', '');

            if($user == ''){
                //メールアドレスがなければ、ユーザーのidからメールアドレスを取得する
                //@var string ユーザーのid
                $personnel_id = $app->request->input('personnel_id', '');

                if($personnel_id == ''){
                    //ユーザーのidがなければ、仮の値
                    $user = 'log@log.com';
                }else{

                    //ユーザーのidがあれば、データベースから取得する
                    $personnel_email = DB::select('select email from dcji01');
                    
                    if($personnel_email){
                        $user = $personnel_email[0]->email;
                    }else{
                        //データベースに存在しないユーザーのidなら仮の値
                        $user = 'log@log.com';
                    }
                }
            }
            //@var string デバックモードの値
            $debug_mode = $app->config['config']['debug_mode'];
            
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
