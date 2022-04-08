<?php

    namespace App\Libraries\php\Domain;

    use App\Models\Date;
    use Illuminate\Support\Facades\DB;
    use App\Facades\OutputLog;
    use App\Libraries\php\Service\ZeroPadding;
    use App\Libraries\php\Service\DatabaseException;
    use App\Libraries\php\Domain\ProjectionDataBase;

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

        /**
         * 更新メソッド
         * 
         * @param $client 顧客ID
         * @param $board_id 掲示板ID
         * @param $name 名前
         * @param $status 状態
         * @param $management_personnel_id 管理者ID
         * @param $remarks 備考
         * 
         */
        public static function update($client_id,$board_id,$name,$status,$management_personnel_id,$remarks){

            DB::update('update dckb01 set name = ?,status = ?,management_personnel_id = ?,remarks = ? where client_id = ? and board_id = ?
            ',[$name,$status,$management_personnel_id,$remarks,$client_id,$board_id]);
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

            //複製するデータの取得
            try{
                $copy_board = DB::select('select * from dckb01 where client_id = ? 
                and board_id = ?',[$client_id,$copy_id]);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('index');
            }

            //複製開始前の状態で、複製するデータに直下配下があるかどうかの確認
            try{
                $lists = DB::select('select * from dccmks where client_id = ? and substring(lower_id, 1, 2) = "kb" and substring(lower_id, 3, 10) <= ? and high_id = ?',
                [$client_id,$number,$copy_id]);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('index');
            }

            //顧客IDに対応した最新の掲示板IDを取得
            try{
                $id = DB::select('select board_id from dckb01 where client_id = ? 
                order by board_id desc limit 1',[$client_id]);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('index');
            }

            //登録する番号を作成
            $padding = new ZeroPadding();
            $board_id = $padding->padding($id[0]->board_id);

            //dd($copy_board[0]);
            //データベースに掲示板情報を登録
            try{
                DB::insert('insert into dckb01 (client_id,board_id,name,status,management_personnel_id,remarks) value (?,?,?,?,?,?)',
                [$client_id,$board_id,$copy_board[0]->name,$copy_board[0]->status,$copy_board[0]->management_personnel_id,$copy_board[0]->remarks]);
            }catch(\Exception $e){
                dd($e);
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('index');
            }

            //データベースに階層情報を登録
            try{
                DB::insert('insert into dccmks
                (client_id,lower_id,high_id)
                VALUE (?,?,?)',
                [$client_id,$board_id,$high]);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('index');
            }

            if(isset($lists)){
                foreach($lists as $list){
                    $copy_id = $list->lower_id;
                    $high = $board_id;
                    $this->copy($copy_id,$client_id,$high,$number);
                }
            }
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

            $data = DB::select('SELECT kb1.client_id,kb1.board_id,kb1.name,kb1.status,kb1.management_personnel_id,
                    kb1.remarks,kb1.created_at,kb1.updated_at,kb2.name AS high_name,high_id,dcji01.name AS management_name FROM dckb01 as kb1
                    left join dccmks on kb1.board_id = dccmks.lower_id 
                    left join dckb01 as kb2 on dccmks.high_id = kb2.board_id
                    left join dcji01 on kb1.management_personnel_id = dcji01.personnel_id
                    where kb1.client_id = ? and dccmks.high_id = ? and kb1.name like ? order by kb1.board_id',[$client_id,$select_id,'%'.$search.'%']);

            //登録日・修正日のフォーマットを変換
            $date = new Date();
            $date->date($data);

            return $data;
        }

        /**
         * 掲示板概要画面で検索した掲示板データを取得するメソッド
         * @param $client 顧客ID
         * @param $select_id 選択したID 
         * 
         * @var   $data 取得データ
         * 
         * @return  array $data
         */
        public static function getSearchTop($client_id,$search){

            $data = DB::select('SELECT kb1.client_id,kb1.board_id,kb1.name,kb1.status,kb1.management_personnel_id,
                    kb1.remarks,kb1.created_at,kb1.updated_at,kb2.name AS high_name,high_id,dcji01.name AS management_name FROM dckb01 as kb1
                    left join dccmks on kb1.board_id = dccmks.lower_id 
                    left join dckb01 as kb2 on dccmks.high_id = kb2.board_id
                    left join dcji01 on kb1.management_personnel_id = dcji01.personnel_id
                    where kb1.client_id = ? and kb1.name like ? order by kb1.board_id',[$client_id,'%'.$search.'%']);

            //登録日・修正日のフォーマットを変換
            $date = new Date();
            $date->date($data);

            return $data;
        }

        /**
         * 上位IDの取得
         * @param $client_id 顧客ID
         * @param $select_id 選択ID
         * 
         * @var   $id 取得id
         * 
         * @return  array $id
         */
        public static function getHigh($client_id,$select_id){

            $id = DB::select('SELECT high_id FROM `dccmks` WHERE client_id = ? and lower_id = ?;',[$client_id,$select_id]);

            return $id;
        }
    }