<?php

    namespace App\Libraries\php\Service\Display\Detail;

    use App\Libraries\php\Domain\BoardDataBase;
    use App\Libraries\php\Service\OperationCheck;

    /**
     * 掲示板詳細表示クラス
     */

    class BoardDisplayDetail extends AbstractDisplayDetail{

        /**
         * ディスプレイ表示メソッド
         * 
         * @var $client_id 顧客ID
         * @var $select_id 選択ID
         * 
         * @var $board_db 
         * @var $board_details 掲示板詳細データ
         * 
         * @return [$department_data,$personnel_data]
         */
        public static function get($client_id,$select_id){

            //インスタンス化
            $board_db = new BoardDataBase();
            $board_details = $board_db->get($client_id,$select_id);

            return $board_details;
        }

    }