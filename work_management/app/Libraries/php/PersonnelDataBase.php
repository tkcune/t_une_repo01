<?php

    namespace App\Libraries\php;

    use App\Models\Date;
    use Illuminate\Support\Facades\DB;

    /**
     * 作業管理システム人員データベース動作クラス
     */

    class PersonnelDataBase {

        /**
         * 選択した人員の情報を取得するメソッド
         * @param $client 顧客ID
         * @param $select_id 選択したID 
         * 
         * @var   $data 取得データ
         * 
         * @return  array $data
         */
        public static function get($client,$select_id){

            $data = DB::select('select dcji01.client_id ,personnel_id,name,email,password,password_update_day,status,management_personnel_id,login_authority,
            system_management,operation_start_date,operation_end_date,dcji01.created_at, dcji01.updated_at ,high_id ,lower_id from dcji01 inner join dccmks 
            on dcji01.personnel_id = dccmks.lower_id where dcji01.client_id = ? and dcji01.personnel_id = ?',[$client,$select_id]);

            //登録日・修正日のフォーマットを変換
            $date = new Date();
            $date->formatDate($data);

            return $data;
        }

        /**
         * 全ての人員の情報を取得するメソッド
         * @param $client 顧客ID
         * @param $select_id 選択したID 
         * 
         * @var   $data 取得データ
         * 
         * @return  array $data
         */
        public static function getAll($client){

            $data = DB::select('select dcji01.client_id ,personnel_id,name,email,password,password_update_day,status,management_personnel_id,login_authority,system_management,operation_start_date,operation_end_date,dcji01.created_at, dcji01.updated_at ,high_id ,lower_id
            from dcji01 inner join dccmks on dcji01.personnel_id = dccmks.lower_id and dcji01.client_id = ?',[$client]);

            return $data;
        }
    }