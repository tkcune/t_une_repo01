<?php

namespace App\Librarys\php;

use Illuminate\Support\Facades\DB;

//ログ出力クラス
class OutputLogClass{
    //@var string クライアントid
    private $client_id;
    //@var string ユーザーメールアドレス
    private $user;

    //@param string $client_id クライアントid
    //@param string $user ユーザーメールアドレス
    //idとメールアドレスを登録する
    public function setup($client_id, $user){
        $this->client_id = $client_id;
        $this->user = $user;
    }

    //@param string $type 状態
    //@param string $function 機能
    //@param string $program_pass パス
    //@param string $log 任意の文字列
    public function db_insert($type, $function, $program_pass, $log){
        //@var array ログテーブルのidの配列
        $result = DB::select('select log_id from dclg01');
        //@var int 次のログid
        $log_id = $this->increment_log_id($result);
        //ログテーブルに出力する
        DB::insert('insert into dclg01 (client_id, log_id, type, user, function, program_pass, log) values (?, ?, ?, ?, ?, ?, ?)', [$this->client_id, $log_id, $type, $this->user, $function, $program_pass, $log]);
    }

    //@param array $result ログテーブルのidの配列
    //@return int idを増加した数字
    //次のテーブルに入れるidを今までのidから増加して取得する。
    private function increment_log_id($result){
        //データがなければ、1を返す
        if($result == []){
            return 1;
        }else{
            //@var int 最も高いidを保存する
            $max = -1;
            foreach($result as $row){
                if($max < $row->log_id){
                    //今までの最高値より高ければ、代入する
                    $max = $row->log_id;
                }
            }
            //最高値を増加する
            $max++;
            return $max;
        }
    }
}

?>