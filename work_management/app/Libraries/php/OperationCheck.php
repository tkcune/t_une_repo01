<?php

namespace App\Libraries\php;

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
            if($data->status == "13" && ($data->operation_start_date < $today || $data->operation_end_date < $today) ){
                OutputLog::message_log(__FUNCTION__,'mhbswn0001');
                $message = Message::get_message('mhbswn0001',[0=>'01']);
                session(['message'=>$message[0]]);
            }elseif($data->status == "18" && ($today < $data->operation_start_date || $today < $data->operation_end_date) ){
                OutputLog::message_log(__FUNCTION__, 'mhbswn0002');
                $message = Message::get_message('mhbswn0002',[0=>'02']);
                session(['message'=>$message[0]]);
            }else{
                
            }

        }
    }
 }