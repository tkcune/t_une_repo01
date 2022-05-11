<?php

    namespace App\Libraries\php\Domain;

    use App\Models\Date;
    use Illuminate\Support\Facades\DB;
    use App\Facades\OutputLog;
    use App\Libraries\php\Service\ZeroPadding;
    use App\Libraries\php\Service\DatabaseException;
    use App\Libraries\php\Domain\ProjectionDataBase;

    /**
     * 作業管理システム付帯定義データベース動作クラス
     */

    class IncidentalDatabase {

        /**
         * 選択したデータを取得するメソッド
         * @param $client 顧客ID
         * @param $select_id 選択したID 
         * 
         * @var   $data 取得データ
         * 
         * @return  array $data
         */
        public static function getList($client_id,$select_id){

            $data = DB::select('SELECT kb1.client_id,kb1.board_id,kb1.name,kb1.status,kb1.management_personnel_id,
                    kb1.remarks,kb1.created_at,kb1.updated_at,kb2.name AS high_name,high_id,dcji01.name AS management_name FROM dckb01 as kb1
                    left join dccmks on kb1.board_id = dccmks.lower_id 
                    left join dckb01 as kb2 on dccmks.high_id = kb2.board_id
                    left join dcji01 on kb1.management_personnel_id = dcji01.personnel_id
                    where kb1.client_id = ? and kb1.board_id = ? order by kb1.board_id',[$client_id,$select_id]);

            return $data;
        }

        /**
         * 最新のID取得
         * @param $client_id 顧客ID
         * 
         * @var   $id 取得id
         * 
         * @return  array $id
         */
        public static function getId($client_id){

            $id = DB::select('select board_id from dckb01 where client_id = ? 
            order by board_id desc limit 1',[$client_id]);

            return $id;
        }

        /**
         * 更新メソッド
         * 
         * 
         */
        public static function update($client_id,$board_id,$name,$status,$management_personnel_id,$remarks){

        }

        /**
         * 削除メソッド
         * 
         * @param $client 顧客ID
         * @param $board_id 掲示板ID
         * 
         */

        public static function delete($client_id,$board_id){

            DB::delete('delete from dckb01 where client_id = ? and board_id = ?',[$client_id,$board_id]);
        }

        /**
         * 掲示板を複製するメソッド
         * 
         * @param $copy_id 複製ID
         * @param $client 顧客ID
         * @param $high 上位ID
         * @param $number 複製直前の最新の部署ID
         * 
         */
        public function copy($copy_id,$client_id,$high,$number){

        }

        /**
         * 検索した掲示板データを取得するメソッド
         * @param $client 顧客ID
         * @param $select_id 選択したID 
         * 
         * @var   $data 取得データ
         * 
         * @return  array $data
         */
        public static function getSearchList($client_id,$select_id,$search){

        }
    }