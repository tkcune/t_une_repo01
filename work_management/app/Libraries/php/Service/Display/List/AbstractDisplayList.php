<?php

    namespace App\Libraries\php\Service\Display\List;

    use App\Facades\OutputLog;
    use App\Libraries\php\Service\Pagination;
    use App\Libraries\php\Service\Message;

    /**
     * 一覧表示の抽象クラス
     */

    abstract class AbstractDisplayList{

        /**
         * ディスプレイ表示メソッド
         * 
         * @param $client_id 顧客ID
         * @param $select_id 選択ID
         * @param $num ページネーション番号
         * @param $search 検索語
         * 
         * @var $data 取得したデータ
         * @var $pagination Paginationクラス
         * @var $max 最大ページ
         * @var $count データの個数
         * 
         * @return ['data' => $data,'count' => $count,'max' => $max]
         */
        public static function display($client_id,$select_id,$num,...$search){

            if(empty($search)){
                $data = static::get($client_id,$select_id);
            }else{
                $data = static::search($client_id,$select_id,$search[0]);
            }

            $pagination = new Pagination();

            $max = $pagination->pageMax($data,count($data));
            $count = count($data);
            $data = $pagination->pagination($data,count($data),$num);

            return ['data' => $data,'count' => $count,'max' => $max];
        }

        abstract public static function get($client_id,$select_id);

        abstract public static function search($client_id,$select_id,$search);
    }