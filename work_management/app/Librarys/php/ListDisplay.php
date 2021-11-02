<?php

    namespace App\Librarys\php;

    use Illuminate\Support\Facades\DB;
    use App\Models\Date;
    use App\Librarys\php\Pagination;
    use Illuminate\Support\Facades\View;

    /**
     * 一覧表示機能クラス
     */ 
    class ListDisplay{

        /**
         * 部署・人員の一覧表示に必要なデータをviewに渡すメソッド
         * @param array $department_data　部署データ
         * @param array $personnel_data 人員データ
         * @param int $count_department 部署データのページ数
         * @param int $count_personnel 人員データのページ数
         * @param string $client_id　顧客ID
         * 
         * @var  App\Models\Date $date 
         * @var  App\Librarys\php\Pagination $pagination
         * @var  int $department_max 部署データページネーションの最大値
         * @var  array $departments ページネーション後の部署データ
         * @var  int $personnel_max 人員データページネーションの最大値
         * @var  array $names ページネーション後の人員データ
         * @var  App\Librarys\php\ResponsiblePerson $responsible
         * @var  array $responsible_lists 責任者リスト
         * @var  App\Librarys\php\Hierarchical $hierarchical
         * @var  array $department_high 部署データの上位階層
         * @var  array $personnel_high 人員データの上位階層
         */
        public function listDisplay($department_data,$personnel_data,$count_department,$count_personnel,$client_id){

            //日付フォーマットを6桁にする
            $date = new Date();
            $date->formatDate($department_data);
            $date->formatDate($personnel_data);

            //基本ページネーション設定
            $pagination = new Pagination();
            $department_max = $pagination->pageMax($department_data,count($department_data));
            $departments = $pagination->pagination($department_data,count($department_data),$count_department);
            $personnel_max= $pagination->pageMax($personnel_data,count($personnel_data));
            $names = $pagination->pagination($personnel_data,count($personnel_data),$count_personnel);
            View::share('department_max', $department_max);
            View::share('departments', $departments);
            View::share('personnel_max', $personnel_max);
            View::share('names', $names);

            //責任者を名前で取得
            $responsible = new ResponsiblePerson();
            $responsible_lists = $responsible->getResponsibleLists($client_id,$departments);
            View::share('responsible_lists', $responsible_lists);

            //上位階層取得
            $hierarchical = new Hierarchical();
            $department_high = $hierarchical->upperHierarchyName($departments);
            $personnel_high = $hierarchical->upperHierarchyName($names);
            View::share('department_high', $department_high);
            View::share('personnel_high', $personnel_high);

        }
}