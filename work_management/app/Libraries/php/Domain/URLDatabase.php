<?php

    namespace App\Libraries\php\Domain;

    use Illuminate\Support\Facades\DB;
    use App\Libraries\php\Service\ZeroPadding;

    /**
     * 作業管理システムURL管理データベース動作クラス
     */

    class UrlDatabase {

        /**
         * ファイルを保存するメソッド
         * @param $client 顧客ID
         * @param $file 顧客ID
         * 
         * @var   $data 取得データ
         * 
         * @return  array $data
         */
        public static function insert($client_id,$url,$board_id){

            //仮置きタイプ
            $data_type = 2;

            //顧客IDに対応した最新のIDを取得
            $id = DB::select('select url_id from dcur01 where client_id = ? 
            order by url_id desc limit 1',[$client_id]);

            
            if(empty($id)){
                $url_id = "ur00000001";
            }else{
                //登録する番号を作成
                $padding = new ZeroPadding();
                $url_id = $padding->padding($id[0]->url_id);
            }

            DB::insert('insert into dcur01 (client_id,url_id,path) 
            value (?,?,?)',[$client_id,$url_id,$url]);

            //付帯定義登録
            //顧客IDに対応した最新のIDを取得
            $id2 = DB::select('select incidental_id from dcft01 where client_id = ? 
            order by incidental_id desc limit 1',[$client_id]);

            if(empty($id2)){
                $incidental_id = "ft00000001";
            }else{
                //登録する番号を作成
                $padding = new ZeroPadding();
                $incidental_id = $padding->padding($id2[0]->incidental_id);
            }

            //付帯定義登録
            DB::insert('insert into dcft01 (client_id,incidental_id,data_id,data_type) 
            value (?,?,?,?)',[$client_id,$incidental_id,$url_id,$data_type]);

            //掲示板に付帯定義情報を登録
            $hierarchical = new Hierarchical();
            $hierarchical->insert($client_id,$incidental_id,$board_id);

        }

        /**
         * 更新
         * @param $client 顧客ID
         * @param $file 顧客ID
         * 
         * @var   $data 取得データ
         * 
         * @return  array $data
         */
        public static function update($client_id,$url,$id){

            DB::update('UPDATE dcur01 SET path = ? WHERE client_id = ? AND url_id = ?'
            ,[$url,$client_id,$id]);

        }
    }