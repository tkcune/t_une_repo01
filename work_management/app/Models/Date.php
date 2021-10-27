<?php

namespace App\Models;

use Carbon\Carbon;



    //日付に関連するクラス
    class Date {

    /**
     * プロパティ
     * @param   $today 今日の日付
     * 
     */
    private $today;

    /** 
     * 今日の日付を判定するメソッド
     * @param   $today 今日の日付
     * 
     * @return　$today
    */
    public function today() {

     $today = now()->format("Y-m-d H:i:s");

     return $today;
    }

    /**
     * 登録日と更新日の日付のフォーマットを変換するメソッド
     * @param array $datas DBデータ 
     * 
     * 
     */
    public function formatDate(&$datas){
        foreach($datas as $data){
            $data->created_at = Carbon::parse($data->created_at)->format('ymd');
            $data->updated_at = Carbon::parse($data->updated_at)->format('ymd');
            if(isset($data->operation_start_date)){
                $data->operation_start_date = Carbon::parse($data->operation_start_date)->format('ymd');
            }
            if(isset($data->operation_end_date)){
                $data->operation_end_date = Carbon::parse($data->operation_end_date)->format('ymd');
            }
            if(isset($data->password_update_day)){
                $data->password_update_day = Carbon::parse($data->password_update_day)->format('ymd');
            }
        }
    }
}