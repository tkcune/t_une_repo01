<?php

    namespace App\Libraries\php\Service\Display\Detail;

    use App\Libraries\php\Domain\PersonnelDataBase;
    use App\Libraries\php\Domain\ProjectionDataBase;

    /**
     * 詳細表示の抽象クラス
     */

    abstract class AbstractDisplayDetail{

        /**
         * ディスプレイ表示メソッド
         * 
         * @var $client_id 顧客ID
         * @var $select_id 選択ID
         * 
         * @var $data 取得したデータ
         * 
         */
        public static function display($client_id,$select_id){

            $data = static::get($client_id,$select_id);

            //システム管理者のリストを取得
            $personnel_db = new PersonnelDataBase();
            $system_management_lists = $personnel_db->getSystemManagement($client_id);

            return ['data' => $data,'system_management_lists'=> $system_management_lists];
        }

        abstract public static function get($client_id,$select_id);

    }