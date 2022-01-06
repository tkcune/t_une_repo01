<?php

namespace App\Libraries\php;

use App\Facades\OutputLog;
use Carbon\Carbon;
use App\Models\Date;

 /**
  * 運用状況を確認するクラス
  */
 class OperationCheck{

    /**
     * 運用状況が適切かどうかを判定するメソッド
     * @param $data 判別する部署、及び人員データ
     * 
     * @var   App\Models\Date $date 日付クラス
     * @var   $today 今日の日付
     */
    public static function check($datas){

        //日付クラスの取得
        $date = new Date();
        $today = Carbon::parse($date->today())->format('Y-m-d');
        

        foreach($datas as $data){
            if($data->status == "13" && (Carbon::parse($data->operation_start_date)->format('Y-m-d') > $today || Carbon::parse($data->operation_end_date)->format('Y-m-d') < $today) ){
                OutputLog::message_log(__FUNCTION__,'mhbswn0001');
                $message = Message::get_message_handle('mhbswn0001',[0=>'01']);
                session(['message'=>$message[0],'handle_message'=>$message[3]]);
            }
            
            if($data->status == "18" && (Carbon::parse($data->operation_start_date)->format('Y-m-d') < $today && $today < Carbon::parse($data->operation_end_date)->format('Y-m-d')) ){
                OutputLog::message_log(__FUNCTION__, 'mhbswn0002');
                $message = Message::get_message_handle('mhbswn0002',[0=>'02']);
                session(['message'=>$message[0],'handle_message'=>$message[3]]);
            }

        }
    }
 }