<?php

namespace App\Librarys\php;

use App\Models\Date;

 /**
  * 稼働開始日、稼働終了日を決定するクラス
  */
 class StatusCheck
  {
      private $status;

      public function statusCheck($status)
      {
        //日付クラスの取得
        $date = new Date();

        //状態によって時刻を挿入するのかどうかの判定
        if($status == "13")
        {
            $operation_start_date = $date->today();
        }else{
            $operation_start_date = null ;
        }
 
        if($status == "18")
        {
            $operation_end_date = $date->today();
        }else{
            $operation_end_date = null ;
        } 

        return [$operation_start_date,$operation_end_date];
      }
  }