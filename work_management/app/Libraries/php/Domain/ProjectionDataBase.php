<?php
    namespace App\Libraries\php\Domain;

    use App\Models\Date;
    use Illuminate\Support\Facades\DB;
    use App\Libraries\php\Service\ZeroPadding;

    /**
    * 作業管理システム投影データベース動作クラス
    */

    class ProjectionDataBase {

        /**
         * 選択した投影IDの投影元を取得する
         * @param string $select_id 選択ID
         * 
         * @var string $data データ
         * 
         * @return $data
         */
        public function getId($select_id){

            $data = DB::select('select projection_source_id from dccmta where projection_id = ?', [$select_id]);

            return $data;
        }

        /**
         * 部署一覧画面の投影データ
         * @param string $client 顧客ID
         * @param string $select_id 選択ID
         * 
         * @var string $data データ
         * @var $date App\Models\Date
         * 
         * @return $data
         */
        public function getDepartmentList($client,$select_id){

            $data = DB::select('SELECT ta1.client_id,bs1.department_id,bs1.responsible_person_id,bs1.name,bs1.status,bs1.management_personnel_id,bs1.operation_start_date,
                    bs1.operation_end_date,bs1.remarks,bs1.created_at,bs1.updated_at,bs2.name AS high_name,ks1.high_id,ji1.name AS management_name,ji2.name AS responsible_name
                    FROM dccmta AS ta1
                    left join dcbs01 as bs1 on ta1.projection_source_id = bs1.department_id
                    left join dccmks as ks1 on ta1.projection_id = ks1.lower_id 
                    left join dcbs01 as bs2 on ks1.high_id = bs2.department_id
                    inner join dcji01 as ji1 on bs1.management_personnel_id = ji1.personnel_id
                    inner join dcji01 as ji2 on ji1.management_personnel_id = ji2.personnel_id
                    where ta1.client_id = ? and ks1.high_id = ? order by bs1.department_id',[$client,$select_id]
                    );

            //登録日・修正日のフォーマットを変換
            $date = new Date();
            $date->formatDate($data);

            return $data;
        }

        /**
         * 部署一覧画面の検索投影データ
         * @param string $client 顧客ID
         * @param string $select_id 選択ID
         * 
         * @var string $data データ
         * @var $date App\Models\Date
         * 
         * @return $data
         */
        public function getDepartmentSearch($client,$select_id,$search){

            $data = DB::select('SELECT ta1.client_id,bs1.department_id,bs1.responsible_person_id,bs1.name,bs1.status,bs1.management_personnel_id,bs1.operation_start_date,
                    bs1.operation_end_date,bs1.remarks,bs1.created_at,bs1.updated_at,bs2.name AS high_name,ks1.high_id,ji1.name AS management_name,ji2.name AS responsible_name
                    FROM dccmta AS ta1
                    left join dcbs01 as bs1 on ta1.projection_source_id = bs1.department_id
                    left join dccmks as ks1 on ta1.projection_id = ks1.lower_id 
                    left join dcbs01 as bs2 on ks1.high_id = bs2.department_id
                    inner join dcji01 as ji1 on bs1.management_personnel_id = ji1.personnel_id
                    inner join dcji01 as ji2 on ji1.management_personnel_id = ji2.personnel_id
                    where ta1.client_id = ? and ks1.high_id = ? and bs1.name like ? order by bs1.department_id',[$client,$select_id,'%'.$search.'%']
                    );

            //登録日・修正日のフォーマットを変換
            $date = new Date();
            $date->formatDate($data);

            return $data;
        }

        /**
         * 人員一覧画面の投影データ
         * @param string $client 顧客ID
         * @param string $select_id 選択ID
         * 
         * @var string $data データ
         * @var $date App\Models\Date
         * 
         * @return $data
         */
        public function getPersonnelList($client,$select_id){

            $data = DB::select('SELECT ta1.client_id,ji1.personnel_id,ji1.name,ji1.email,ji1.password,ji1.password_update_day,
                    ji1.status,ji1.management_personnel_id,ji1.login_authority,ji1.system_management,ji1.operation_start_date,
                    ji1.operation_end_date,ji1.remarks,ji1.created_at,ji1.updated_at,bs1.name AS high_name,ks1.high_id,ji2.name AS management_name 
                    FROM dccmta AS ta1
                    left join dcji01 AS ji1 on ta1.projection_source_id = ji1.personnel_id
                    left join dccmks AS ks1 on ta1.projection_id = ks1.lower_id 
                    left join dcbs01 AS bs1 on ks1.high_id = bs1.department_id
                    left join dcji01 AS ji2 on ji1.management_personnel_id = ji2.personnel_id
                    where ji1.client_id = ? and high_id = ? order by ji1.personnel_id',[$client,$select_id]
                    );

            //登録日・修正日のフォーマットを変換
            $date = new Date();
            $date->formatDate($data);

            return $data;
        }

        /**
         * 人員一覧画面の検索投影データ
         * @param string $client 顧客ID
         * @param string $select_id 選択ID
         * 
         * @var string $data データ
         * @var $date App\Models\Date
         * 
         * @return $data
         */
        public function getPersonnelSearch($client,$select_id,$search){

            $data = DB::select('SELECT ta1.client_id,ji1.personnel_id,ji1.name,ji1.email,ji1.password,ji1.password_update_day,
                    ji1.status,ji1.management_personnel_id,ji1.login_authority,ji1.system_management,ji1.operation_start_date,
                    ji1.operation_end_date,ji1.remarks,ji1.created_at,ji1.updated_at,bs1.name AS high_name,ks1.high_id,ji2.name AS management_name 
                    FROM dccmta AS ta1
                    left join dcji01 AS ji1 on ta1.projection_source_id = ji1.personnel_id
                    left join dccmks AS ks1 on ta1.projection_id = ks1.lower_id 
                    left join dcbs01 AS bs1 on ks1.high_id = bs1.department_id
                    left join dcji01 AS ji2 on ji1.management_personnel_id = ji2.personnel_id
                    where ji1.client_id = ? and high_id = ? and ji1.name like ? order by ji1.personnel_id',[$client,$select_id,'%'.$search.'%']
                    );

            //登録日・修正日のフォーマットを変換
            $date = new Date();
            $date->formatDate($data);

            return $data;
        }

        /**
         * 掲示板一覧画面の投影データ
         * @param string $client 顧客ID
         * @param string $select_id 選択ID
         * 
         * @var string $data データ
         * @var $date App\Models\Date
         * 
         * @return $data
         */
        public function getBoardList($client,$select_id){

            $data = DB::select('SELECT ta1.client_id,kb1.board_id,kb1.name,kb1.status,kb1.management_personnel_id,
                                kb1.remarks,kb1.created_at,kb1.updated_at,kb2.name AS high_name,high_id,dcji01.name AS management_name
                                FROM dccmta AS ta1
                                left join dckb01 AS kb1 on ta1.projection_source_id = kb1.board_id
                                left join dccmks AS ks1 on ta1.projection_id = ks1.lower_id
                                left join dckb01 AS kb2 on ks1.high_id = kb2.board_id
                                left join dcji01 on kb1.management_personnel_id = dcji01.personnel_id
                                where ta1.client_id = ? and high_id = ? order by kb1.board_id',[$client,$select_id]
                    );

            return $data;
        }

        /**
         * 選択した投影IDを取得する
         * @param string $client 顧客ID
         * @param string $select_id 選択ID
         * 
         * @var string $data データ
         * 
         * @return $data
         */
        public function getProjectionId($client,$select_id){

            $data = DB::select('select projection_id from dccmta where client_id = ? 
                    and projection_source_id = ?',[$client,$select_id]
                );

            return $data;
        }

        /**
         * 最新の投影IDを取得する
         * @param string $client_id 顧客ID
         * 
         * @var string $id 現時点の最新投影番号データ
         * @var string $projection_id 最新の投影番号
         * 
         * @return $projection_id
         */
        public function getNewId($client_id){

            //登録されている中で一番若い番号を取得
            $id = DB::select('select projection_id from dccmta where client_id = ? 
                order by projection_id desc limit 1',[$client_id]
            );

            if(empty($id)){
                $projection_id = "ta00000001";
            }else{
                //登録する番号を作成
                $padding = new ZeroPadding();
                $projection_id = $padding->padding($id[0]->projection_id);
            }
            return $projection_id;
        }

        /**
         * 登録処理
         * @param string $client_id 顧客ID
         * @param string $projection_id 投影番号
         * @param string $projection_source_id　投影元のID
         */
        public function insert($client_id,$projection_id,$projection_source_id){

            DB::insert('insert into dccmta
                (client_id,projection_id,projection_source_id)
                VALUE (?,?,?)',
                [$client_id,$projection_id,$projection_source_id]
            );
                
        }

        /**
         * 選択したIDの上位IDを取得
         * @param string $client 顧客ID
         * @param string $high_id 下位ID
         * 
         * @var string $high_id データ
         * 
         * @return $high_id
         */
        public function getHighId($client,$lower_id){

            //選択した投影の上位IDを取得
            $high_id = DB::select('select high_id from dccmks where client_id = ?
            and lower_id = ?',[$client,$lower_id]);

            return $high_id;
        }

        /**
         * 削除
         * @param string $client 顧客ID
         * @param string $high_id 投影ID
         */
        public function delete($client,$projection_id){

            DB::delete('delete from dccmta where client_id = ? and projection_id = ?',
            [$client,$projection_id]);
        }

    }