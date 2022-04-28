<?php

    namespace App\Libraries\php\Service\Display\List;

    use App\Libraries\php\Domain\BoardDataBase;
    use App\Libraries\php\Domain\ProjectionDataBase;

    /**
     * 掲示板の一覧表示クラス
     */

    class BoardDisplayList extends AbstractDisplayList{

        /**
         * @param $client_id 顧客ID
         * @param $select_id 選択ID
         * 
         * @var App\Libraries\php\Domain\BoardDataBase $board_db
         * @var $board_data 一覧データ
         * @var App\Libraries\php\Domain\ProjectionDataBase $projection_db
         * @var $projection_board 一覧投影データ
         * 
         * @return $board_data
         */
        public static function get($client_id,$select_id){

            //一覧に記載する掲示板データの取得
            $board_db = new BoardDataBase();

            //概要画面かどうかの判別
            if($select_id == 'kb00000000'){
                $board_data = $board_db->getAll($client_id);
            }else{
                //一覧に記載する掲示板データの取得
                $board_data = $board_db->getList($client_id,$select_id);

                //一覧の投影部署データの取得
                $projection_db = new ProjectionDataBase();
                $projection_board = $projection_db->getBoardList($client_id,$select_id);

                //投影データを一覧に追加
                $board_data = array_merge($board_data,$projection_board);
            }

            return $board_data;
        }

        /**
         * @param $client_id 顧客ID
         * @param $select_id 選択ID
         * @param $search 検索語
         * 
         * @var App\Libraries\php\Domain\BoardDataBase $board_db
         * @var $board_data 一覧データ
         * @var App\Libraries\php\Domain\ProjectionDataBase $projection_db
         * @var $projection_board 一覧投影データ
         * 
         * @return $board_data
         */
        public static function search($client_id,$select_id,$search){

            $board_db = new BoardDataBase();

            //一覧に記載する掲示板データの取得
            if($select_id == 'kb00000000'){
                $board_data = $board_db->getSearchTop($client_id,$search);
            }else{
                $board_data = $board_db->getSearchList($client_id,$select_id,$search);
            }

            //一覧の投影部署データの取得
            $projection_db = new ProjectionDataBase();
            $projection_board = $projection_db->getBoardList($client_id,$select_id);

            //投影データを一覧に追加
            $board_data = array_merge($board_data,$projection_board);

            return $board_data;
        }
    }