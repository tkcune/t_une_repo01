<?php

    namespace App\Libraries\php\Service;

    /**
     *ページネーション機能クラス
     */
    
    class Pagination{

        /**　
         * プロパティ
         * var array $array 与えられた配列
         * var int $array_num 配列の総合データ数
         * var const MAX 1ページの記事の表示数
         */
        private array $array;
        private int $arrayNum; 
        const MAX = 20;

        /**
         * ページネーションの最大ページ数を計算
         * @param array $array ページネーションされる配列
         * @param int $array_num 配列の数
         * @param int $max_page 最大ページ数
         * 
         * return int $max_page
         */
        public static function pageMax(array $array,int $array_num){

            $max_page = ceil($array_num / self::MAX);

            return $max_page;
        }

        /**
         * ページネーションで表示する配列の要素を求めるメソッド
         * @param array $array ページネーションされる配列
         * @param array $array_num 配列の数
         * @param int $page_num ページネーションのページ数
         * @param int $start_no 取得を始める配列の番号
         * @param array $disp_data 表示するデータ
         * 
         * return array $disp_data
         */
        public static function pagination(array $array,int $array_num,int $page_num){
        

            // 配列の何番目から取得すればよいか
            $start_no = ($page_num -1) * self::MAX;

            // array_sliceは、配列の何番目($start_no)から何番目(MAX)まで切り取る関数
            $data = array_slice($array, $start_no, self::MAX, true);

            $disp_data = array_values($data);

            return $disp_data;
        }
    }