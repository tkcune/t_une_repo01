<?php

    namespace App\Libraries\php\Domain;

    use App\Models\Date;
    use Illuminate\Support\Facades\DB;

    /**
     * 作業管理システム掲示板データベース動作クラス
     */

    class BoardDatabase {

        /**
         * 全掲示板データを取得するメソッド
         * @param $client 顧客ID
         * 
         * @var   $data 取得データ
         * 
         * @return  array $data
         */
        public static function getAll($client_id){

            $data = DB::select('SELECT kb1.client_id,kb1.board_id,kb1.name,kb1.status,kb1.management_personnel_id,
                    kb1.remarks,kb1.created_at,kb1.updated_at,kb2.name AS high_name,high_id,dcji01.name AS management_name FROM dckb01 AS kb1
                    left join dccmks on kb1.board_id = dccmks.lower_id 
                    left join dckb01 as kb2 on dccmks.high_id = kb2.board_id
                    left join dcji01 on kb1.management_personnel_id = dcji01.personnel_id
                    where kb1.client_id = ? order by kb1.board_id',[$client_id]);

            //登録日・修正日のフォーマットを変換
            $date = new Date();
            $date->date($data);

            return $data;
        }

        /**
         * 選択した掲示板データを取得するメソッド
         * @param $client 顧客ID
         * @param $select_id 選択したID 
         * 
         * @var   $data 取得データ
         * 
         * @return  array $data
         */
        public static function get($client_id,$select_id){

            $data = DB::select('SELECT kb1.client_id,kb1.board_id,kb1.name,kb1.status,kb1.management_personnel_id,
                    kb1.remarks,kb1.created_at,kb1.updated_at,kb2.name AS high_name,high_id,dcji01.name AS management_name FROM dckb01 as kb1
                    left join dccmks on kb1.board_id = dccmks.lower_id 
                    left join dckb01 as kb2 on dccmks.high_id = kb2.board_id
                    left join dcji01 on kb1.management_personnel_id = dcji01.personnel_id
                    where kb1.client_id = ? and kb1.board_id = ? order by kb1.board_id',[$client_id,$select_id]);

            //登録日・修正日のフォーマットを変換
            $date = new Date();
            $date->date($data);

            return $data;
        }

        /**
         * 選択した掲示板の配下データを取得するメソッド
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
                    where kb1.client_id = ? and dccmks.high_id = ? order by kb1.board_id',[$client_id,$select_id]);

            //登録日・修正日のフォーマットを変換
            $date = new Date();
            $date->date($data);

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
         * 選択した掲示板の配下データを取得するメソッド
         * 
         * @param $client 顧客ID
         * @param $name タイトル
         * @param $status 状態
         * @param $management_personnel_id 管理者ID
         * @param $remarks 備考
         * 
         */
        public static function insert($client_id,$board_id,$name,$status,$management_personnel_id,$remarks){

            DB::insert('insert into dckb01 (client_id,board_id,name,status,management_personnel_id,remarks) 
            value (?,?,?,?,?,?)',[$client_id,$board_id,$name,$status,$management_personnel_id,$remarks]);

        }

        public static function update($client_id,$board_id,$name,$status,$management_personnel_id,$remarks){

            DB::update('update dckb01 set name = ?,status = ?,management_personnel_id = ?,remarks = ? where client_id = ? and board_id = ?
            ',[$name,$status,$management_personnel_id,$remarks,$client_id,$board_id]);
        }

        public static function delete($client_id,$board_id){

            DB::delete('delete from dckb01 where client_id = ? and board_id = ?',[$client_id,$board_id]);
        }
    }