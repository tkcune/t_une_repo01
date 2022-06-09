<?php

    namespace App\Libraries\php\Domain;

    use Illuminate\Support\Facades\DB;


    /**
     * スタンプ機能DB動作クラス
     */

    class StampDatabase {

        /**
         * スタンプが押されているかどうかのチェック
         * @param $client 顧客ID
         * @param $board_id 掲示板ID
         * @param $personnel_id 人員ID
         * @param $stamp_id スタンプID
         * 
         * @var   $data 取得データ
         * 
         * @return  array $data
         */
        public static function get($client_id,$board_id,$personnel_id,$stamp_id){

            $data = DB::select('SELECT stamp_id FROM dcsv01 
                    where client_id = ? and board_id = ? and personnel_id = ? and stamp_id = ? ',
                    [$client_id,$board_id,$personnel_id,$stamp_id]);

            return $data;
        }

        /**
         * スタンプ情報の取得
         * @param $client 顧客ID
         * @param $board_id 掲示板ID
         * @param $personnel_id 人員ID
         * @param $stamp_id スタンプID
         * 
         * @var   $data 取得データ
         * 
         * @return  array $data
         */
        public static function stamp($client,$board_id){

            $data = DB::table('dcsv01')
                    ->join('dcsp01','dcsv01.stamp_id','=', 'dcsp01.stamp_id')
                    ->join('dcji01',function ($join) {
                        $join->on('dcsv01.personnel_id','=', 'dcji01.personnel_id')
                             ->on('dcsv01.client_id','=', 'dcji01.client_id');
                        })
                    ->select(DB::raw('dcsv01.stamp_id,base64,dcji01.name'))
                    ->where('dcsv01.client_id',$client)
                    ->where('board_id',$board_id)
                    ->get();

            return $data;
        }

        /**
         * インサート
         * @param $client 顧客ID
         * @param $board_id 掲示板ID
         * @param $personnel_id 人員ID
         * @param $stamp_id スタンプID
         * 
         * @var   $data 取得データ
         * 
         * @return  array $data
         */
        public static function insert($client,$board_id,$personnel_id,$stamp_id){

            DB::insert('INSERT INTO `dcsv01`(`client_id`,`personnel_id`,`board_id`,`stamp_id`) VALUES (?,?,?,?)',
            [$client,$personnel_id,$board_id,$stamp_id]);

        }

        /**
         * 削除
         * @param $client 顧客ID
         * @param $board_id 掲示板ID
         * @param $personnel_id 人員ID
         * @param $stamp_id スタンプID
         * 
         * @var   $data 取得データ
         * 
         * @return  array $data
         */
        public static function delete($client,$board_id,$personnel_id,$stamp_id){

            DB::delete('Delete FROM dcsv01 WHERE client_id = ? AND board_id = ? AND personnel_id = ? AND stamp_id = ?',
            [$client,$board_id,$personnel_id,$stamp_id]);
        }
        
    }