<?php

    namespace App\Libraries\php\Service\Display\Detail;

    use App\Libraries\php\Domain\DepartmentDataBase;
    use App\Libraries\php\Domain\PersonnelDataBase;
    use App\Libraries\php\Service\OperationCheck;

    /**
     * 部署詳細表示クラス
     */

    class DepartmentDisplayDetail extends AbstractDisplayDetail{

        /**
         * ディスプレイ表示メソッド
         * 
         * @var $client_id 顧客ID
         * @var $select_id 選択ID
         * 
         * @var $department_data 詳細画面データ
         * @var $personnel_data 所属人員データ
         * 
         * @return [$department_data,$personnel_data]
         */
        public static function get($client_id,$select_id){

            //インスタンス化
            $department_db = new DepartmentDataBase();
            $personnel_db = new PersonnelDataBase();

            //詳細画面のデータ取得
            $department_data = $department_db->get($client_id,$select_id);

            //所属人員データの取得
            $personnel_data = $personnel_db->getSelectList($client_id,$select_id);
            
            //運用状況の確認
            $operation_check = new OperationCheck();
            $operation_check->check($department_data,$select_id);

            return [$department_data,$personnel_data];
        }

    }