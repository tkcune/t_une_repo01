<?php

    namespace App\Libraries\php\Domain;

    use App\Models\Date;
    use Illuminate\Support\Facades\DB;
    use App\Facades\OutputLog;
    use App\Libraries\php\Service\ZeroPadding;
    use App\Libraries\php\Service\DatabaseException;

    /**
     * 作業管理システムURL管理データベース動作クラス
     */

    class FileDatabase {

        /**
         * ファイルを保存するメソッド
         * @param $client 顧客ID
         * @param $file 顧客ID
         * 
         * @var   $data 取得データ
         * 
         * @return  array $data
         */
        public static function insert($client_id,$url_id,$url,$board_id){

            DB::insert('insert into dc0001 (client_id,url_id,url) 
            value (?,?,?)',[$client_id,$url_id,$url]);

            DB::insert('insert into dccmks (client_id,lower_id,high_id) 
            value (?,?,?,?)',[$client_id,$url,$board_id]);

        }
    }