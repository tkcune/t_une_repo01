<?php

    namespace App\Libraries\php;

    use Illuminate\Support\Facades\DB;
    use App\Models\Psnw;

    class Network {

        /**
         * フォームに記入されているデータをテーブルにインサートするメソッド
         *
         * @return void
         */
        public function addNetwork($network_datas){
            $psnw = new Psnw();
            DB::table('psnw')->insert($network_datas);
        }   
    }