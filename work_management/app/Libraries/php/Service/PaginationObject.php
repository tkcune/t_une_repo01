<?php

namespace App\Libraries\php\Service;

use App\Libraries\php\Service\Pagination;

/**
 *部署・人員・作業場所のページネーションオブジェクト機能クラス
 */

class PaginationObject
{

    //bladeに渡す変数
    public int $department_max;
    public int $count_department;
    public int $total_department;
    public array $departments;

    public int $personnel_max;
    public int $count_personnel;
    public int $total_personnel;
    public array $names;

    public int $space_max;
    public int $count_space;
    public int $total_space;
    public array $spaces;

    //ページネーション変数をセットする
    //@var  int $department_max 部署データページネーションの最大値
    //@var  array $departments ページネーション後の部署データ
    //@var  int $personnel_max 人員データページネーションの最大値
    //@var  array $names ページネーション後の人員データ
    public function set_pagination($department_data, $count_department, $personnel_data, $count_personnel)
    {

        $this->count_department = $count_department;
        $this->count_personnel = $count_personnel;
        $this->total_department = $department_data['count'];
        $this->total_personnel = $personnel_data['count'];
        $this->department_max = $department_data['max'];
        $this->departments = $department_data['data'];
        $this->personnel_max = $personnel_data['max'];
        $this->names = $personnel_data['data'];
    }

    //作業場所用：ページネーション変数をセットする
    //@var  int $space_max 作業場所データページネーションの最大値
    //@var  array $space_details ページネーション後の作業場所データ
    public function space_set_pagination($space_data, $count_space)
    {
        $this->count_space =  $count_space;
        $this->total_space = $space_data['count'];
        $this->space_max = $space_data['max'];
        $this->space_details = $space_data['data'];
    }
}
