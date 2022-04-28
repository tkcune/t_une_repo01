<?php

    namespace App\Libraries\php\Service\Display\List;

    use App\Libraries\php\Domain\PersonnelDataBase;
    use App\Libraries\php\Domain\ProjectionDataBase;

    /**
     * 部署詳細画面の人員一覧表示クラス
     */

    class PersonnelDisplayList extends AbstractDisplayList{

        /**
         * @param $client_id 顧客ID
         * @param $select_id 選択ID
         * 
         * @var App\Libraries\php\Domain\PersonnelDataBase $personnel_db
         * @var $personnel_data 一覧データ
         * @var App\Libraries\php\Domain\ProjectionDataBase $projection_db
         * @var $projection_personnel 一覧投影データ
         * 
         * @return $personnel_data
         */
        public static function get($client_id,$select_id){

            //一覧の人員データの取得
            $personnel_db = new PersonnelDataBase();

            if($select_id == 'bs00000000'){
                $personnel_data = $personnel_db->getList($client_id);
            }else{
                $personnel_data = $personnel_db->getSelectList($client_id,$select_id);
            }
            //一覧の投影人員データの取得
            $projection_db = new ProjectionDataBase();
            $projection_personnel = $projection_db->getPersonnelList($client_id,$select_id);

            //投影データを一覧人員に追加
            $personnel_data = array_merge($personnel_data,$projection_personnel);

            return $personnel_data;
        }

        /**
         * @param $client_id 顧客ID
         * @param $select_id 選択ID
         * @param $search 検索語
         * 
         * @var App\Libraries\php\Domain\PersonnelDataBase $personnel_db
         * @var $personnel_data 一覧データ
         * @var App\Libraries\php\Domain\ProjectionDataBase $projection_db
         * @var $projection_personnel 一覧投影データ
         * 
         * @return $personnel_data
         */
        public static function search($client_id,$select_id,$search){

            //一覧の人員データの取得
            $personnel_db = new PersonnelDataBase();
            if($select_id == 'bs00000000'){
                $personnel_data = $personnel_db->searchTop($client_id,$search);
            }else{
                $personnel_data = $personnel_db->search($client_id,$select_id,$search);
            }

            //一覧の投影人員データの取得
            $projection_db = new ProjectionDataBase();
            $projection_personnel = $projection_db->getPersonnelSearch($client_id,$select_id,$search);

            $personnel_data = array_merge($personnel_data,$projection_personnel);

            return $personnel_data;
        }
    }