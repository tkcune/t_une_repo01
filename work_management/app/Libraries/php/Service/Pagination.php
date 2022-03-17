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

        //bladeに渡す変数
        public int $department_max;
        public int $count_department;
        public int $total_department;
        public array $departments;
        public int $personnel_max;
        public int $count_personnel;
        public int $total_personnel;
        public array $names;

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

        //ページネーション変数をセットする
        //@var  int $department_max 部署データページネーションの最大値
        //@var  array $departments ページネーション後の部署データ
        //@var  int $personnel_max 人員データページネーションの最大値
        //@var  array $names ページネーション後の人員データ
        public function set_pagination($department_data, $count_department, $personnel_data, $count_personnel){
            $this->count_department = $count_department;
            $this->count_personnel = $count_personnel;
            $this->total_department = count($department_data);
            $this->total_personnel = count($personnel_data);
            $this->department_max = $this->pageMax($department_data,count($department_data));
            $this->departments = $this->pagination($department_data,count($department_data),$count_department);
            $this->personnel_max = $this->pageMax($personnel_data,count($personnel_data));
            $this->names = $this->pagination($personnel_data,count($personnel_data),$count_personnel);
        }
    }