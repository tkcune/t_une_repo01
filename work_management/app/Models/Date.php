<?php

namespace App\Models;



    //日付に関連するクラス
class Date
{
    private $today;//今日の日付

    /** 
     * 今日の日付を判定するメソッド
     * @param   $today 今日の日付
     * @return　$today
    */
    public function today() {

     $today = now()->format("Y-m-d H:i:s");

     return $today;
    }
}