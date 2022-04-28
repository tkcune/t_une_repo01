<?php

    namespace App\Libraries\php\Service\Display\List;

    use App\Libraries\php\Domain\DepartmentDataBase;
    use App\Libraries\php\Domain\ProjectionDataBase;

    /**
     * 部署詳細画面の部署一覧表示クラス
     */

    class DepartmentDisplayList extends AbstractDisplayList{

        /**
         * @param $client_id 顧客ID
         * @param $select_id 選択ID
         * 
         * @var App\Libraries\php\Domain\DepartmentDataBase $department_db
         * @var $department_data 一覧データ
         * @var App\Libraries\php\Domain\ProjectionDataBase $projection_db
         * @var $projection_department 一覧投影データ
         * 
         * @return $department_data
         */
        public static function get($client_id,$select_id){

            $department_db = new DepartmentDataBase();

            //一覧の部署データの取得
            if($select_id == 'bs00000000'){
                $department_data = $department_db->getList($client_id);
            }else{
                $department_data = $department_db->getSelectList($client_id,$select_id);
            }

            //一覧の投影部署データの取得
            $projection_db = new ProjectionDataBase();
            $projection_department = $projection_db->getDepartmentList($client_id,$select_id);

            //投影データを一覧部署に追加
            $department_data = array_merge($department_data,$projection_department);

            return $department_data;
        }

        /**
         * @param $client_id 顧客ID
         * @param $select_id 選択ID
         * @param $search 検索語
         * 
         * @var App\Libraries\php\Domain\DepartmentDataBase $department_db
         * @var $department_data 一覧データ
         * @var App\Libraries\php\Domain\ProjectionDataBase $projection_db
         * @var $projection_department 一覧投影データ
         * 
         * @return $department_data
         */
        public static function search($client_id,$select_id,$search){

            //一覧の部署データの検索取得
            $department_db = new DepartmentDataBase();
            if($select_id == 'bs00000000'){
                $department_data = $department_db->searchTop($client_id,$search);
            }else{
                $department_data = $department_db->search($client_id,$select_id,$search);
            }

            //一覧の投影部署データの取得
            $projection_db = new ProjectionDataBase();
            $projection_department = $projection_db->getDepartmentSearch($client_id,$select_id,$search);

            //投影データを一覧部署に追加
            $department_data = array_merge($department_data,$projection_department);

            return $department_data;
        }
    }