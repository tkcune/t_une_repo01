<?php

namespace App\Libraries\php\Service;

use App\Models\Date;

 /**
  * 稼働開始日、稼働終了日を決定するクラス
  */
 class StatusCheck
  {
      /**
       * プロパティ
       * var string $status ステータス
       */
      private $status;

      /**
       * 部署・人員の状態を判別し、データベースに稼働開始日及び終了日を登録するメソッド
       * @param string $status ステータス
       * @param App\Models\Date $date 時刻
       * @param string $operation_start_date 稼働開始日
       * @param string $operation_end_date 稼働開始日
       * 
       * return array [$operation_start_date,$operation_end_date]
       */
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