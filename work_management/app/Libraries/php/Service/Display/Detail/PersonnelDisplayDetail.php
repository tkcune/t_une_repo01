<?php

    namespace App\Libraries\php\Service\Display\Detail;

    use App\Libraries\php\Domain\DepartmentDataBase;
    use App\Libraries\php\Domain\PersonnelDataBase;
    use App\Libraries\php\Service\OperationCheck;

    /**
     * 部署詳細表示クラス
     */

    class PersonnelDisplayDetail extends AbstractDisplayDetail{

        /**
         * ディスプレイ表示メソッド
         * 
         * @var $client_id 顧客ID
         * @var $select_id 選択ID
         * 
         * @var $personnel_data 詳細人員データ
         * 
         * @return $personnel_data]
         */
        public static function get($client_id,$select_id){

            //インスタンス化
            $personnel_db = new PersonnelDataBase();

            //詳細画面のデータ取得
            $click_personnel_data = $personnel_db->get($client_id,$select_id);

            //運用状況の確認
            $operation_check = new OperationCheck();
            $operation_check->check($click_personnel_data,$select_id);

            return $click_personnel_data;
        }

    }