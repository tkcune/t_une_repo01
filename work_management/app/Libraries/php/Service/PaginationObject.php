<?php

    namespace App\Libraries\php\Service;

    use App\Libraries\php\Service\Pagination;

    /**
     *部署・人員ページネーションオブジェクト機能クラス
     */
    
    class PaginationObject{

        //bladeに渡す変数
        public int $department_max;
        public int $count_department;
        public int $total_department;
        public array $departments;
        public int $personnel_max;
        public int $count_personnel;
        public int $total_personnel;
        public array $names;

        //ページネーション変数をセットする
        //@var  int $department_max 部署データページネーションの最大値
        //@var  array $departments ページネーション後の部署データ
        //@var  int $personnel_max 人員データページネーションの最大値
        //@var  array $names ページネーション後の人員データ
        public function set_pagination($department_data, $count_department, $personnel_data, $count_personnel){

            $this->count_department = $count_department;
            $this->count_personnel = $count_personnel;
            $this->total_department = $department_data['count'];
            $this->total_personnel = $personnel_data['count'];
            $this->department_max = $department_data['max'];
            $this->departments = $department_data['data'];
            $this->personnel_max = $personnel_data['max'];
            $this->names = $personnel_data['data'];
        }
    }