<?php

    namespace App\Libraries\php\Service\Display\Detail;

    use App\Libraries\php\Domain\BoardDataBase;

    /**
     * 掲示板詳細表示クラス
     */

    class BoardDisplayDetail extends AbstractDisplayDetail{

        /**
         * ディスプレイ表示メソッド
         * 
         * @param $client_id 顧客ID
         * @param $select_id 選択ID
         * 
         * @var App\Libraries\php\Domain\BoardDataBase $board_db 
         * @var $board_details 掲示板詳細データ
         * 
         * @return $board_details
         */
        public static function get($client_id,$select_id){

            //インスタンス化
            $board_db = new BoardDataBase();
            $board_details = $board_db->get($client_id,$select_id);

            return $board_details;
        }

    }