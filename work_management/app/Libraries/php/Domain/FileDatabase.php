<?php

    namespace App\Libraries\php\Domain;

    use App\Models\Date;
    use Illuminate\Support\Facades\DB;
    use App\Facades\OutputLog;
    use App\Libraries\php\Domain\Hierarchical;
    use App\Libraries\php\Service\ZeroPadding;
    use App\Libraries\php\Service\DatabaseException;

    /**
     * 作業管理システムファイル管理データベース動作クラス
     */

    class FileDatabase {

        /**
         * ファイルを保存するメソッド
         * @param $client 顧客ID
         * @param $file 顧客ID
         * 
         * @var   $data 取得データ
         * 
         * @return  array $data
         */
        public static function insert($client_id,$file,$board_id){

            $file_name = $file->getClientOriginalName();
            $dir = 'upload';
            $path = $file->storeAs($dir, $file_name, ['disk' => 'local']);
            $data_type = 1;

            //顧客IDに対応した最新のIDを取得
            $id = DB::select('select file_id from dcfi01 where client_id = ? 
            order by file_id desc limit 1',[$client_id]);

            
            if(empty($id)){
                $file_id = "fi00000001";
            }else{
                //登録する番号を作成
                $padding = new ZeroPadding();
                $file_id = $padding->padding($id[0]->file_id);
            }
            //ファイル登録
            DB::insert('insert into dcfi01 (client_id,file_id,name,path) 
            value (?,?,?,?)',[$client_id,$file_id,$file_name,$path]);

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
            value (?,?,?,?)',[$client_id,$incidental_id,$file_id,$data_type]);

            //掲示板に付帯定義情報を登録
            $hierarchical = new Hierarchical();
            $hierarchical->insert($client_id,$incidental_id,$board_id);
        }

        /**
         * ファイルパスを取得するメソッド
         * @param $client 顧客ID
         * @param $file 顧客ID
         * 
         * @var   $data 取得データ
         * 
         * @return  array $data
         */
        public static function path($client_id,$select_id){

            $path = DB::select('SELECT name,path FROM dcfi01 WHERE client_id = ? AND file_id = ?'
            ,[$client_id,$select_id]);

            return $path;
        }
    }