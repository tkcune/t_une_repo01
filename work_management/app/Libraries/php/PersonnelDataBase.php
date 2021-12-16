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
            system_management,operation_start_date,operation_end_date,remarks, dcji01.created_at, dcji01.updated_at ,high_id ,lower_id from dcji01 inner join dccmks 
            on dcji01.personnel_id = dccmks.lower_id where dcji01.client_id = ? and dcji01.personnel_id = ?',[$client,$select_id]);

            //登録日・修正日のフォーマットを変換
            $date = new Date();
            $date->formatDate($data);

            return $data;
        }

        /**
         * 人員の情報を取得するメソッド
         * @param $client 顧客ID
         * @param $select_id 選択したID 
         * 
         * @var   $data 取得データ
         * 
         * @return  array $data
         */
        public static function getData($client,$select_id){

            $data = DB::select('select * from dcji01 where client_id = ? and personnel_id = ?',[$client,$select_id]);
        
            return $data;
        }

        /**
         * 全ての人員の情報を取得するメソッド
         * @param $client 顧客ID
         * 
         * @var   $data 取得データ
         * 
         * @return  array $data
         */
        public static function getAll($client){

            $data = DB::select('select dcji01.client_id ,personnel_id,name,email,password,password_update_day,status,management_personnel_id,login_authority,system_management,operation_start_date,operation_end_date,
            remarks,dcji01.created_at, dcji01.updated_at ,high_id ,lower_id from dcji01 inner join dccmks on dcji01.personnel_id = dccmks.lower_id and dcji01.client_id = ?',[$client]);
            
            return $data;
        }

        /**
         * 最新の人員IDを取得
         * @param $client 顧客ID
         * 
         * @var   $id Id
         * 
         * @return  array $id
         */

        public static function getId($client_id){

            $id = DB::select('select personnel_id from dcji01 where client_id = ? 
            order by personnel_id desc limit 1',[$client_id]);

            return $id;
        }

        /**
         * 選択した人員データを取得
         * @param $client 顧客ID
         * @param $select_id 選択したID 
         * 
         * @var   $data 取得データ
         * 
         * @return  array $data
         */
        public static function getClick($client,$select_id){

            $data = DB::select('select dcji01.client_id,personnel_id,name,email,password,
            password_update_day,status,management_personnel_id,login_authority,system_management,operation_start_date,
            operation_end_date,remarks,dcji01.created_at,dcji01.updated_at,high_id from dcji01 inner join dccmks 
            on dcji01.personnel_id = dccmks.lower_id where dcji01.client_id = ?
            and dcji01.personnel_id = ?',[$client,$select_id]);

            return $data;
        }

        /**
         * 選択した人員の所属部署を取得
         * @param $client 顧客ID
         * @param $select_id 選択したID 
         * 
         * @var   $data 取得データ
         * 
         * @return  array $data
         */
        public static function getClickDepartment($client,$select_id){

            $data = DB::select('select high_id from dccmks where client_id = ?
            and lower_id = ?',[$client,$select_id]);

            return $data;
        }

        /**
         * 検索
         * @param $client 顧客ID
         * @param $search 検索文字
         * 
         * @var   $data 取得データ
         * 
         * @return  array $data
         */
        public static function search($client,$search){

            $data = DB::select('select dcji01.client_id,personnel_id,name,email,password,password_update_day,status,management_personnel_id,
            login_authority,system_management,operation_start_date,operation_end_date,remarks,dcji01.created_at, dcji01.updated_at ,high_id ,lower_id
            from dcji01 inner join dccmks on dcji01.personnel_id = dccmks.lower_id and dcji01.client_id = ?
            where dcji01.name like ?',[$client,'%'.$search.'%']);

            return $data;
        }

        /**
         * 登録
         * @param $client_id 顧客ID
         * @param $personnel_id 人員ID
         * @param $name 名前
         * @param $email メールアドレス
         * @param $password パスワード
         * @param $status 状態
         * @param $login_authority ログイン権限
         * @param $system_management 管理者権限
         */
        public static function insert($client_id,$personnel_id,$name,$email,$password,$status,$login_authority,$system_management){

            $date = new Date();

            DB::insert('insert into dcji01
            (client_id,
            personnel_id,
            name,
            email,
            password,
            password_update_day,
            status,
            management_personnel_id,
            login_authority,
            system_management,
            operation_start_date
            )
            VALUE (?,?,?,?,?,?,?,?,?,?,?)',
            [
            $client_id,
            $personnel_id,
            $name,
            $email,
            $password,
            $date->today(),
            $status,
            $personnel_id,
            $login_authority,
            $system_management,
            $date->today()
            ]);

        }
    }