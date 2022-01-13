<?php

    namespace App\Libraries\php\Service;

    /**
     * 作業管理システムIDの0埋め機能クラス
     */

    Class ZeroPadding {

        /**
         * 最新の番号を作成
         * @param string $old_number 直前のID
         * @param array  $pieces　分離したIDの格納先
         * @param string $number  最新の番号
         * @param string $id      最新のID
         * 
         * @return string $id
         */
        public static function padding($old_number){

            //英字と数字の分割
            $pieces[0] = substr($old_number,0,2);
            $pieces[1] = substr($old_number,3);
            //一番最新の番号にする
            $pieces[1] = $pieces[1] + "1";

            //0埋め
            $number = str_pad($pieces[1], 8, '0', STR_PAD_LEFT);
            $id = $pieces[0].$number;

            return $id;
        }
    }
