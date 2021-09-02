<?php

    namespace App\Librarys\php;

    //ページネーション機能クラス
    class Pagination{

    private array $array;//与えられた配列
    private int $array_num; //配列の総合データ数
    const MAX = 5; // 1ページの記事の表示数

    public static function pageMax(array $array,int $array_num)
    {
        $max_page = ceil($array_num / self::MAX);

        return $max_page;
    }

    public static function pagination(array $array,int $array_num,int $page_num){
        

        // 配列の何番目から取得すればよいか
        $start_no = ($page_num -1) * self::MAX;

        // array_sliceは、配列の何番目($start_no)から何番目(MAX)まで切り取る関数
        $disp_data = array_slice($array, $start_no, self::MAX, true);

        return $disp_data;
    }
}