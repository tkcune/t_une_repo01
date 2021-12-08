<?php

    namespace App\Libraries\php;

    use App\Models\Date;
    use Illuminate\Support\Facades\DB;

    /**
     * 作業管理システム部署データベース動作クラス
     */

    class DepartmentDataBase {

        /**
         * 選択した部署を取得するメソッド
         * @param $client 顧客ID
         * @param $select_id 選択したID 
         * 
         * @var   $data 取得データ
         * 
         * @return  array $data
         */
        public static function get($client,$select_id){

            $data = DB::select('select dcbs01.client_id,department_id,responsible_person_id,name,status,management_personnel_id,operation_start_date,operation_end_date,remarks,lower_id, high_id, dcbs01.created_at, dcbs01.updated_at
            from dcbs01 inner join dccmks on dcbs01.department_id = dccmks.lower_id where dcbs01.client_id = ? and department_id = ?',[$client,$select_id]);

            //登録日・修正日のフォーマットを変換
            $date = new Date();
            $date->formatDate($data);

            return $data;
        }

        /**
         * 全体の部署データの取得
         * @param $client 顧客ID
         * 
         * @var   $data 取得データ
         * 
         * @return  array $data
         */
        public static function getAll($client_id){

            $data = DB::select('select dcbs01.client_id, department_id,responsible_person_id,name,status,management_personnel_id,operation_start_date,operation_end_date,remarks,lower_id, high_id, dcbs01.created_at, dcbs01.updated_at
            from dcbs01 inner join dccmks on dcbs01.department_id = dccmks.lower_id and dcbs01.client_id = ?',[$client_id]);

            return $data;
        }

        /**
         * 最上位部署データの取得
         * @param $client 顧客ID
         * 
         * @var   $data 取得データ
         * 
         * @return  array $data
         */
        public static function getTop($client_id){

            $data = DB::select('select * from dcbs01 where client_id = ? limit 1',[$client_id]);

            //登録日・修正日のフォーマットを変換
            $date = new Date();
            $date->formatDate($data);

            return $data;
        }
    }