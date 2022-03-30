<?php

    namespace App\Libraries\php\Domain;

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

            
            $data = DB::select('SELECT bs1.client_id,bs1.department_id,bs1.responsible_person_id,bs1.name,bs1.status,bs1.management_personnel_id,bs1.operation_start_date,
                    bs1.operation_end_date,bs1.remarks,bs1.created_at,bs1.updated_at,bs2.name AS high_name,lower_id,high_id,ji1.name AS management_name,ji2.name AS responsible_name
                    FROM dcbs01 AS bs1
                    left join dccmks on bs1.department_id = dccmks.lower_id 
                    left join dcbs01 as bs2 on dccmks.high_id = bs2.department_id
                    left join dcji01 as ji1 on bs1.management_personnel_id = ji1.personnel_id
                    left join dcji01 as ji2 on bs1.responsible_person_id = ji2.personnel_id
                    where bs1.client_id = ? and bs1.department_id = ? order by bs1.department_id',[$client,$select_id]
                    );

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

            $data = DB::select('select dcbs01.client_id, department_id,responsible_person_id,
                    name,status,management_personnel_id,operation_start_date,operation_end_date,remarks,
                    lower_id, high_id,dcbs01.created_at,dcbs01.updated_at from dcbs01 inner join dccmks 
                    on dcbs01.department_id = dccmks.lower_id and dcbs01.client_id = ?',[$client_id]
                    );

            return $data;
        }

        /**
         * 一覧の部署データの取得
         * @param $client 顧客ID
         * 
         * @var   $data 取得データ
         * @var   $date App\Models\Date;
         * 
         * @return  array $data
         */
        public static function getList($client_id){

            $data = DB::select('SELECT bs1.client_id,bs1.department_id,bs1.responsible_person_id,bs1.name,bs1.status,bs1.management_personnel_id,bs1.operation_start_date,
                    bs1.operation_end_date,bs1.remarks,bs1.created_at,bs1.updated_at,bs2.name AS high_name,high_id,dcji01.name AS management_name 
                    FROM dcbs01 AS bs1
                    left join dccmks on bs1.department_id = dccmks.lower_id 
                    left join dcbs01 as bs2 on dccmks.high_id = bs2.department_id
                    left join dcji01 on bs1.management_personnel_id = dcji01.personnel_id
                    where bs1.client_id = ? order by bs1.department_id',[$client_id]
                    );
            
            //登録日・修正日のフォーマットを変換
            $date = new Date();
            $date->formatDate($data);

            return $data;
        }

        /**
         * 選択部署の一覧部署データの取得
         * @param $client 顧客ID
         * 
         * @var   $data 取得データ
         * @var   $date App\Models\Date;
         * 
         * @return  array $data
         */
        public static function getSelectList($client_id,$select_id){

            $data = DB::select('SELECT bs1.client_id,bs1.department_id,bs1.responsible_person_id,bs1.name,bs1.status,bs1.management_personnel_id,bs1.operation_start_date,
                    bs1.operation_end_date,bs1.remarks,bs1.created_at,bs1.updated_at,bs2.name AS high_name,high_id,ji1.name AS management_name,ji2.name AS responsible_name
                    FROM dcbs01 AS bs1
                    left join dccmks on bs1.department_id = dccmks.lower_id 
                    left join dcbs01 as bs2 on dccmks.high_id = bs2.department_id
                    left join dcji01 as ji1 on bs1.management_personnel_id = ji1.personnel_id
                    left join dcji01 as ji2 on bs1.responsible_person_id = ji2.personnel_id
                    where bs1.client_id = ? and high_id = ? order by bs1.department_id',[$client_id,$select_id]
                    );
            
            //登録日・修正日のフォーマットを変換
            $date = new Date();
            $date->formatDate($data);

            return $data;
        }

        /**
         * 人員詳細の部署一覧データ
         * @param $client 顧客ID
         * @param $select_id 選択ID
         * 
         * @var   $data 取得データ
         * @var   $date App\Models\Date;
         * 
         * @return  array $data
         */

        public static function getDepartmentList($client_id,$select_id){

            $data = DB::select('SELECT 
                    bs1.client_id,bs1.department_id,bs1.responsible_person_id,bs1.name,bs1.status,bs1.management_personnel_id,bs1.operation_start_date,
                    bs1.operation_end_date,bs1.remarks,bs1.created_at,bs1.updated_at,bs2.name AS high_name,ks2.high_id,ji1.name AS management_name,ji2.name AS responsible_name
                    FROM dcji01 AS ji1
                    left join dccmks as ks1 on ji1.personnel_id = ks1.lower_id
                    inner join dcbs01 as bs1 on ks1.high_id = bs1.department_id
                    left join dccmks as ks2 on bs1.department_id = ks2.lower_id
                    left join dcbs01 as bs2 on ks2.high_id = bs2.department_id
                    inner join dcji01 as ji2 on ji1.management_personnel_id = ji2.personnel_id
                    where ji1.client_id = ? and ji1.personnel_id = ?',[$client_id,$select_id]
                    );
            
            //登録日・修正日のフォーマットを変換
            $date = new Date();
            $date->formatDate($data);

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

        /**
         * 選択した部署データの取得
         * @param $client 顧客ID
         * @param $high_id 上位ID
         * 
         * @var   $data 取得データ
         * 
         * @return  array $data
         */
        public static function getClickDepartment($client,$high_id){

            $data = DB::select('select * from dcbs01 inner join dccmks on 
                    dcbs01.department_id = dccmks.lower_id where dcbs01.client_id = ?
                    and dcbs01.department_id = ?',[$client,$high_id]
                    );
            //登録日・修正日のフォーマットを変換
            $date = new Date();
            $date->formatDate($data);

            return $data;
        }

        /**
         * 選択した人員が所属する部署データの取得
         * @param $client 顧客ID
         * @param $high_id 上位ID
         * 
         * @var   $data 取得データ
         * 
         * @return  array $data
         */
        public static function getClickDepartmentData($client,$high_id){

            $data = DB::select('select dcbs01.client_id,department_id,responsible_person_id,
                    name,status,management_personnel_id,operation_start_date,operation_end_date,
                    remarks,lower_id, high_id, dcbs01.created_at, dcbs01.updated_at
                    from dcbs01 inner join dccmks on dcbs01.department_id = dccmks.lower_id 
                    where dcbs01.client_id = ? and dcbs01.department_id = ?',[$client,$high_id]
                    );

            return $data;
        }

        /**
         * 選択部署が最上位部署かどうかの判別
         * @param $client 顧客ID
         * @param $high_id 上位ID
         * 
         * @var   $data 取得データ
         * 
         * @return  array $data
         */
        public static function getClickTop($client,$high_id){

            $data = DB::select('select * from dcbs01 where client_id = ? and department_id = ?',[$client,$high_id]);

            return $data;
        }

        /**
         * 部署データの取得
         * @param $client 顧客ID
         * 
         * @var   $data 取得データ
         * 
         * @return  array $data
         */
        public static function getData($client){

            $data = DB::select('select * from dcbs01 inner join dccmks on dcbs01.department_id = dccmks.lower_id and dcbs01.client_id = ?',[$client]);

            return $data;
        }

        /**
         * 部署データの検索
         * @param $client 顧客ID
         * @param $search 検索文字
         * 
         * @var   $data 取得データ
         * @var   $date App\Models\Date
         * 
         * @return  array $data
         */
        public static function search($client,$select_id,$search){

            $data = DB::select('SELECT bs1.client_id,bs1.department_id,bs1.responsible_person_id,bs1.name,bs1.status,bs1.management_personnel_id,bs1.operation_start_date,
            bs1.operation_end_date,bs1.remarks,bs1.created_at,bs1.updated_at,bs2.name AS high_name,high_id,ji1.name AS management_name,ji2.name AS responsible_name
            FROM dcbs01 AS bs1
            left join dccmks on bs1.department_id = dccmks.lower_id 
            left join dcbs01 as bs2 on dccmks.high_id = bs2.department_id
            left join dcji01 as ji1 on bs1.management_personnel_id = ji1.personnel_id
            inner join dcji01 as ji2 on ji1.management_personnel_id = ji2.personnel_id
            where bs1.client_id = ? and high_id = ? and bs1.name like ? order by bs1.department_id',
            [$client,$select_id,'%'.$search.'%']);
            
            //登録日・修正日のフォーマットを変換
            $date = new Date();
            $date->formatDate($data);

            return $data;
        }

        /**
         * 人員詳細画面による部署データの検索
         * @param $client 顧客ID
         * @param $search 検索文字
         * 
         * @var   $data 取得データ
         * @var   $date App\Models\Date
         * 
         * @return  array $data
         */
        public static function searchDetailPersonnel($client,$select_id,$search){

            $data = DB::select('SELECT ji1.client_id,bs1.department_id,bs1.responsible_person_id,bs1.name,bs1.status,bs1.management_personnel_id,bs1.operation_start_date,
            bs1.operation_end_date,bs1.remarks,bs1.created_at,bs1.updated_at,bs2.name AS high_name,ks2.high_id,ji2.name AS management_name,ji3.name AS responsible_name
            FROM dcji01 AS ji1
            left join dccmks as ks1 on ji1.personnel_id = ks1.lower_id 
            left join dcbs01 as bs1 on ks1.high_id = bs1.department_id
            left join dccmks as ks2 on bs1.department_id = ks2.lower_id
            left join dcbs01 as bs2 on ks2.high_id = bs2.department_id
            inner join dcji01 as ji2 on bs1.management_personnel_id = ji2.personnel_id
            inner join dcji01 as ji3 on ji2.management_personnel_id = ji3.personnel_id
            where bs1.client_id = ? and ji1.personnel_id = ? and bs1.name like ? order by bs1.department_id',
            [$client,$select_id,'%'.$search.'%']);

            //登録日・修正日のフォーマットを変換
            $date = new Date();
            $date->formatDate($data);

            return $data;
        }

        /**
         * 最新の部署ID取得
         * @param $client_id 顧客ID
         * 
         * @var   $id 取得id
         * 
         * @return  array $id
         */
        public static function getId($client_id){

            $id = DB::select('select department_id from dcbs01 where client_id = ? 
            order by department_id desc limit 1',[$client_id]);

            return $id;
        }

        /**
         * 部署の新規登録
         * @param $client_id 顧客ID
         * @param $department_id 部署ID
         * @param $responsible_person_id 責任者ID
         * @param $name 名前
         * @param $status 状態
         * @param $management_personnel_id 管理者ID 
         * @param $operation_start_date 稼働開始日
         * @param $remarks 備考
         * 
         */
        public static function insert($client_id,$department_id,$responsible_person_id,
        $name,$status,$management_personnel_id,$operation_start_date,$remarks){

            DB::insert('insert into dcbs01 (client_id,department_id,
            responsible_person_id,name,status,management_personnel_id,
            operation_start_date,remarks) VALUE (?,?,?,?,?,?,?,?)',
            [$client_id,$department_id,$responsible_person_id,
            $name,$status,$management_personnel_id,
            $operation_start_date,$remarks]);

        }   


        /**
         * 部署の更新
         * @param $responsible_person_id 責任者ID
         * @param $name 名前
         * @param $status 状態
         * @param $management_number 管理者ID
         * @param $start_day 稼働開始日
         * @param $finish_day 稼働終了日
         * @param $remarks 備考
         * @param $client_id 顧客ID
         * @param $department_id 部署ID
         *
         */

        public static function update($responsible_person_id,$name,$status,$management_number,$start_day,$finish_day,$remarks,$client_id,$department_id){

            DB::update('update dcbs01 set responsible_person_id = ?,name = ?,status = ?,management_personnel_id = ?,operation_start_date = ?,operation_end_date = ?,remarks = ? where client_id = ? and department_id = ?',
            [$responsible_person_id,$name,$status,$management_number,$start_day,$finish_day,$remarks,$client_id,$department_id]);

        }

        /**
         * 削除メソッド
         * @param $client 顧客ID
         * @param $select_id 選択したID 
         */
        public static function delete($client,$select_id){

            DB::delete('delete from dcbs01 where client_id = ? and department_id = ?',
            [$client,$select_id]);

        }
    }