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
    }