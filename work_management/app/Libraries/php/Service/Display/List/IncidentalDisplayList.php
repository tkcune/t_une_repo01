<?php

    namespace App\Libraries\php\Service\Display\List;

    use App\Libraries\php\Domain\IncidentalDataBase;

    /**
     * 掲示板の一覧表示クラス
     */

    class IncidentalDisplayList extends AbstractDisplayList{

        /**
         * @param $client_id 顧客ID
         * @param $select_id 選択ID
         * 
         * @var App\Libraries\php\Domain\IncidentalDataBase $board_db
         * @var $board_data 一覧データ
         * 
         * @return $board_data
         */
        public static function get($client_id,$select_id){

            //一覧に記載するデータの取得
            $incidental_db = new IncidentalDataBase();
            $incidental_data = $incidental_db->getList($client_id,$select_id);

            return $incidental_data;
        }

        /**
         * @param $client_id 顧客ID
         * @param $select_id 選択ID
         * @param $search 検索語
         * 
         * @var App\Libraries\php\Domain\BoardDataBase $board_db
         * @var $board_data 一覧データ
         * 
         * @return $board_data
         */
        public static function search($client_id,$select_id,$search){

            return $incidental_data;
        }
    }