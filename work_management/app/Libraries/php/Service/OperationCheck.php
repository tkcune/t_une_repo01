<?php

namespace App\Libraries\php\Service;

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
     * @param $data 判別するID
     * 
     * @var   App\Models\Date $date 日付クラス
     * @var   $today 今日の日付
     * @var   $message_code 稼働中の場合のエラーメッセージコード
     * @var   $message_code2 廃止の場合のエラーメッセージコード
     */
    public static function check($datas,$id){

        //日付クラスの取得
        $date = new Date();
        $today = Carbon::parse($date->today())->format('Y-m-d');

        //該当エラーメッセージの判別
        $code = substr($id,0,2);

        if($code == 'bs'){
            $message_code = 'mhbswn0001';
            $message_code2 = 'mhbswn0002';
        }else{
            $message_code = 'mhjiwn0001';
            $message_code2 = 'mhjiwn0002';
        }

        foreach($datas as $data){
            if($data->status == "13" && (Carbon::parse($data->operation_start_date)->format('Y-m-d') > $today || Carbon::parse($data->operation_end_date)->format('Y-m-d') < $today) ){
                OutputLog::message_log(__FUNCTION__,$message_code);
                $message = Message::get_message_handle($message_code,[0=>'01']);
                session(['message'=>$message[0],'handle_message'=>$message[3]]);
            }
            
            if($data->status == "18" && (Carbon::parse($data->operation_start_date)->format('Y-m-d') < $today && $today < Carbon::parse($data->operation_end_date)->format('Y-m-d')) ){
                OutputLog::message_log(__FUNCTION__, $message_code2);
                $message = Message::get_message_handle($message_code2,[0=>'02']);
                session(['message'=>$message[0],'handle_message'=>$message[3]]);
            }

        }
    }

 }