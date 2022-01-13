<?php

    namespace App\Libraries\php\Logic;

    use Illuminate\Support\Facades\DB;

    /**
     * 責任者を取得する共通クラス
     */
    class ResponsiblePerson{
    
    private array $responsible_lists = [] ;//責任者データ配列

    /**
     * 責任者を取得するメソッド
     * @param int $client_id 顧客ID
     * @param array $departments 与えられた部署データ配列
     * @param array $responsible_lists 責任者データ配列
     * 
     * return $responsible_lists
     */
    public static function getResponsibleLists($client_id,$departments){

        $responsible_lists = [];

        foreach($departments as $department){
            try{
                $responsible = DB::select('select name from dcji01 where client_id = ? and personnel_id = ?',[$client_id,$department->responsible_person_id]);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
            }
            array_push($responsible_lists,$responsible[0]->name);
        }

        return $responsible_lists;
    }

    /**
     * 管理者を取得するメソッド
     * @param int $client_id 顧客ID
     * @param array $departments 与えられた部署データ配列
     * @param array $management_lists 責任者データ配列
     * 
     * return $management_lists
     */
    public static function getManagementLists($client_id,$departments){

        $management_lists = [];
        
        foreach($departments as $department){
            try{
                $management = DB::select('select name from dcji01 where client_id = ? and personnel_id = ?',[$client_id,$department->management_personnel_id]);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
            }
            array_push($management_lists,$management[0]->name);
        }

        return $management_lists;
    }
}
?>