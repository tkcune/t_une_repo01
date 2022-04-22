<?php

    namespace App\Libraries\php\Service\Display\List;

    use App\Libraries\php\Domain\DepartmentDataBase;
    use App\Libraries\php\Domain\ProjectionDataBase;

    /**
     * 人員詳細の部署一覧表示クラス
     */

    class PersonnelDetailsList extends AbstractDisplayList{

        public static function get($client_id,$select_id){

            //一覧の部署データの取得
            $department_db = new DepartmentDataBase();
            $department_data = $department_db->getDepartmentList($client_id,$select_id);

            //一覧の投影部署データの取得
            $projection_db = new ProjectionDataBase();
            $projection_department = $projection_db->getDepartmentList($client_id,$select_id);

            //投影データを一覧部署に追加
            $department_data = array_merge($department_data,$projection_department);

            return $department_data;
        }

        public static function search($client_id,$select_id,$search){

            try{
                $department_db = new DepartmentDataBase();
                $department_data = $department_db->searchDetailPersonnel($client_id,$select_id,$search);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('pa0001.errormsg');
            }

            //一覧の投影部署データの取得
            try{
                $projection_db = new ProjectionDataBase();
                $projection_department = $projection_db->getDepartmentList($client_id,$select_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('pa0001.errormsg');
            }

            //投影データを一覧部署に追加
            $department_data = array_merge($department_data,$projection_department);

            return $department_data;
        }
    }