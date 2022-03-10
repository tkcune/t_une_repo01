<?php

    namespace App\Libraries\php\Service;

    /**
     *人員詳細画面に必要な変数を渡すクラス
     */

    class PersonnelDetailsObject{

        /**　
         * プロパティ
         * @var $name 部署名
         * @var $client_id 顧客ID
         * @var $personnel_id 人員ID
         * @var $high_id 上位ID
         * @var $high_name 上位部署名
         * @var $management_personnel_id 管理者番号
         * @var $management_name 管理者名
         * @var $status 状態
         * @var $login_authority ログイン権限
         * @var $system_management システム管理者
         * @var $responsible 責任者
         * @var $operation_start_date 運用開始日
         * @var $operation_finish_date 運用終了日
         * @var $remarks 備考
         * @var $created_at 登録日
         * @var $updated_at 修正日
         * @var $registered_person 登録者
         * 
         */
        private $name;
        private $client_id; 
        private $personnel_id;
        private $high_id;
        private $high_name;
        private $email;
        private $management_personnel_id;
        private $management_name;
        private $status;
        private $login_authority;
        private $system_management;
        private $operation_start_date;
        private $operation_finish_date; 
        private $remarks;
        private $created_at;
        private $updated_at;
        private $registered_person;


        /** 
         * 部署詳細画面に必要な変数をセットする
         * @param $department_data 部署データ
         * @param $department_high 上位部署
         * 
         */
        public function setPersonnelObject($personnel_data,$responsible,$management,$operation_date){

            $this->name = $personnel_data[0]->name;
            $this->personnel_id = $personnel_data[0]->personnel_id;
            $this->high_id = $personnel_data[0]->high_id;
            $this->high_name = $personnel_data[0]->high_name;
            $this->email = $personnel_data[0]->email;
            $this->management_personnel_id = $personnel_data[0]->management_personnel_id;
            $this->management_name = $management[0];
            $this->status = $personnel_data[0]->status;
            $this->login_authority = $personnel_data[0]->login_authority;
            $this->system_management = $personnel_data[0]->system_management;
            $this->operation_start_date = $operation_date["operation_start_date"];
            $this->operation_finish_date = $operation_date["operation_end_date"];
            $this->remarks = $personnel_data[0]->remarks;
            $this->created_at = $personnel_data[0]->created_at;
            $this->updated_at = $personnel_data[0]->updated_at;
        }
    }
?>