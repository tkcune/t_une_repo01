<?php

    namespace App\Libraries\php\Service\Display\Detail;

    use App\Libraries\php\Domain\PersonnelDataBase;

    /**
     * 詳細表示の抽象クラス
     */

    abstract class AbstractDisplayDetail{

        /**
         * ディスプレイ表示メソッド
         * 
         * @param $client_id 顧客ID
         * @param $select_id 選択ID
         * 
         * @var array $data 取得したデータ
         * @var App\Libraries\php\Domain\PersonnelDataBase $personnel_db
         * @var array $system_management_lists 管理者リスト
         * 
         * @return array ['data' => $data,'system_management_lists'=> $system_management_lists]
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