<?php
    namespace App\Libraries\php;

    use App\Models\Date;
    use Illuminate\Support\Facades\DB;
    use App\Libraries\php\ZeroPadding;

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

    }