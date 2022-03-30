<?php

    namespace App\Libraries\php\Service;

    /**
     *詳細画面に必要な変数を渡すクラス
     */

    class DepartmentDetailsObject{

        /**　
         * プロパティ
         * @var $name 部署名
         * @var $client_id 顧客ID
         * @var $high_id 上位ID
         * @var $high_name 上位部署名
         * @var $lower_id 下位ID
         * @var $management_personnel_id 管理者番号
         * @var $management_name 管理者名
         * @var $status 状態
         * @var $responsible 責任者
         * @var $operation_start_date 運用開始日
         * @var $operation_finish_date 運用終了日
         * @var $remarks 備考
         * @var $created_at 登録日
         * @var $updated_at 修正日
         * @var $registered_person 登録者
         * 
         */
        public $department_data;
        public $name;
        public $department_id;
        public $high_id;
        public $high_name;
        public $lower_id;
        public $management_personnel_id;
        public $management_name;
        public $status;
        public $responsible;
        public $responsible_person_id;
        public $operation_start_date;
        public $operation_end_date; 
        public $remarks;
        public $created_at;
        public $updated_at;
        public $registered_person;


        /** 
         * 部署詳細画面に必要な変数をセットする
         * 
         * @param $department_data 部署データ
         * @param $responsible 責任者データ
         * @param $management 管理者データ
         * @param $operation_date 運用日時データ
         * 
         */
        public function setDepartmentObject($department_data){

            $this->client_id = $department_data[0]->client_id;
            $this->name = $department_data[0]->name;
            $this->department_id = $department_data[0]->department_id;
            $this->high_id = $department_data[0]->high_id;
            $this->high_name = $department_data[0]->high_name;
            $this->lower_id = $department_data[0]->lower_id;
            $this->management_personnel_id = $department_data[0]->management_personnel_id;
            $this->management_name = $department_data[0]->management_name;
            $this->status = $department_data[0]->status;
            $this->responsible_id = $department_data[0]->responsible_person_id;
            $this->responsible = $department_data[0]->responsible_name;
            $this->responsible_person_id = $department_data[0]->responsible_person_id;
            $this->operation_start_date = $department_data[0]->operation_start_date;
            $this->operation_end_date = $department_data[0]->operation_end_date;
            $this->remarks = $department_data[0]->remarks;
            $this->created_at = $department_data[0]->created_at;
            $this->updated_at = $department_data[0]->updated_at;
            $this->registered_person = $department_data[0]->responsible_person_id;
        }
    }
?>