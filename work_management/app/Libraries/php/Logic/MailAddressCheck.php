<?php 

namespace App\Libraries\php\Logic;

use Illuminate\Support\Facades\DB;

 /**
  * メールアドレスのチェッククラス
  */ 
 class MailAddressCheck{

    /**
     * メールアドレスチェック
     * 
     * @param $client_id 顧客ID
     * @param $email　メールアドレス
     * 
     * @var $email
     * 
     * @return boolean
     */
    public function check($client_id,$personnel_id,$email){

        $check = DB::select('SELECT * FROM `dcji01` WHERE NOT (personnel_id = ? AND client_id = ?) AND email = ?',[$personnel_id,$client_id,$email]);

        if(empty($check)){
            return true;
        }
        else{
            return false;
        }
    }
}