<?php

namespace App\Libraries\php;

use Illuminate\Support\Facades\DB;
use App\Libraries\php\Message;
use App\Libraries\php\FunctionCode;

//ログ出力クラス
class OutputLogClass{
    //@var stirng デバックモード
    private $debug_mode;
    //@var string クライアントid
    private $client_id;
    //@var string ユーザーメールアドレス
    private $user;
    
    //@param string $client_id クライアントid
    //@param string $user ユーザーメールアドレス
    //@pram string $debug_mode デバックモード
    public function __construct(string $client_id,string $user,string $debug_mode){
        //初期値を代入する
        $this->client_id = $client_id;
        $this->user = $user;
        $this->debug_mode = $debug_mode;
    }

    //ログ出力メソッド
    //@param string $program_pass プログラムのパス
    //@param string $type 種別
    //@param string $function 機能
    //@param string $log ログ
    public function log(string $program_pass,string $type,string $function,string $log){
        
        //@var string 機能コード
        $function = FunctionCode::get_functioncode($function);
        //@var string クラス名とメソッド名
        $program_pass = $this->get_program_path($program_pass);
        //ログを出力する
        $this->output_log($type, $function, $log, $program_pass);
    }

    //メッセージログ出力メソッド
    //@param string $program_pass プログラムのパス
    //@param string $message_code メッセージ番号
    //@param array $message_string メッセージに挿入する文字列の配列
    public function message_log(string $program_pass,string $message_code, ...$message_string){
        //種別でなければ、メッセージ番号とみなす
        //@var array ログメッセージと種別と機能
        $message_array = Message::get_message($message_code, $message_string);
        //@var string ログ
        $log = $message_array[0];
        //@var string 種別
        $type = $message_array[1];
        //@var string 機能
        $function = $message_array[2];
        //@var string クラス名とメソッド名
        $program_pass = $this->get_program_path($program_pass);
        //ログを出力する
        $this->output_log($type, $function, $log, $program_pass);
    }
    //@param string $type 状態
    //@param string $function 機能
    //@param string $program_pass パス
    //@param string $log 任意の文字列
    //ログ出力メソッド
    private function output_log($type, $function, $log, $program_pass){

        //@var date 現在時刻,データベースのデータとログファイルの作成時刻を合わせる
        $created_at = date("Y/m/d h/i/s");

        if(!in_array($type, ['nm', 'wn', 'er', 'ok', 'si', 'sy'])){
            $type = "er";
        }

        //ログテーブルに出力する
        //@var boolean データベースの結果
        $result = false;
        try {
            //@var int 次のログid
            $log_id = $this->increment_log_id();
            $result = DB::insert('insert into dclg01 (client_id, log_id, created_at, type, user, function, program_pass, log) values (?, ?, ?, ?, ?, ?, ?, ?)', [$this->client_id, $log_id, $created_at, $type, $this->user, $function, $program_pass, $log]);
        } catch (Exception $e) {
            //エラーが発生した場合
            $type = 'er';
            $log = 'dclg01に不具合があります';
            $result = true;
        }
        if($result){
            try {
                //種別により、ログファイルを出力するか、判断する
                if($this->is_output_log_csv($type)){
                    //@var array ログファイルに出力するcsv
                    $log_csv = $this->create_log_csv($created_at, $type, $function, $log);
                    //ログファイルを出力
                    $this->write_log_csv($log_csv);
                }
                //種別により、システムログファイルを出力するか、判断する
                if($this->is_output_systemlog_csv($type)){
                    //@var array システムログファイルに出力するcsv
                    $system_log_csv = $this->create_system_log_csv($created_at, $type, $program_pass, $function, $log);
                    //システムログファイルに出力する
                    $this->write_system_log_csv($system_log_csv);
                }else if($log == '処理開始'){
                    //webサーバーの処理開始メッセージは、デバックモードでなくてもシステムログに残す。
                
                    //@var array システムログファイルに出力するcsv
                    $system_log_csv = $this->create_system_log_csv($created_at, $type, $program_pass, $function, $log);
                    //システムログファイルに出力する
                    $this->write_system_log_csv($system_log_csv);
                }
            } catch (Exception $e) {
                //
            }
        }
    }

    //@param array $log_csv ログファイルに出力するcsv
    //ログファイルを出力する
    private function write_log_csv($log_csv){
        //@var boolean ログファイルが存在するか判断
        $isfile = is_file('./fclg'.substr($this->client_id, -8).'.csv');
        if($isfile){
            //@var File ログファイルのファイルポインタ
            $facmlg_csv = fopen('./fclg'.substr($this->client_id, -8).'.csv', 'a');
            //ログを追加出力する
            fputcsv($facmlg_csv, $log_csv);
        }else{
            //ログファイルが存在しなければ、新規作成
            //@var array ログファイルのヘッダー
            $head = ['時間', '類別', 'アクセスユーザー', '機能', 'ログ'];
            //@var File ログファイルを作成し、ポインタを取得する
            $facmlg_csv = fopen('./fclg'.substr($this->client_id, -8).'.csv', 'w');
            
            //ヘッダーを出力する
            fputcsv($facmlg_csv, $head);
            //ログを出力する
            fputcsv($facmlg_csv, $log_csv);
        }
    }

    //@param array $log_csv システムログファイルに出力するcsv
    //システムログファイルを出力する
    private function write_system_log_csv($system_log_csv){
        //@var boolean システムログファイルが存在するか判断
        $isfile = is_file('./falg01.csv');
        if($isfile){
            //@var File システムログファイルのファイルポインタ
            $facmsl_csv = fopen('./falg01.csv', 'a');
            //システムログを追加出力する
            fputcsv($facmsl_csv, $system_log_csv);
        }else{
            //システムログファイルが存在しなければ、新規作成
            //@var array システムログファイルのヘッダー
            $head = ['時間', '類別', '顧客名', 'アクセスユーザー', '機能', 'プログラムパス', 'ログ'];
            //@var File システムログファイルを作成し、ポインタを取得する
            $facmsl_csv = fopen('./falg01.csv', 'w');
            
            //ヘッダーを出力する
            fputcsv($facmsl_csv, $head);
            //システムログを出力する
            fputcsv($facmsl_csv, $system_log_csv);
        }
    }

    //@param string $type 種別
    //@return boolean ログファイルを出力するか、論理値
    //ログファイルに出力するか、判断する
    private function is_output_log_csv($type){
        //種別がnm,wn,er,okなら出力する
        if(in_array($type, ['nm', 'wn', 'er', 'ok'])){
            return true;
        }else{
            //種別がsi,syなら出力しない
            return false;
        }
    }

    //@param string $type 種別
    //@return boolean システムログファイルを出力するか、論理値
    //システムログファイルに出力するか、判断する
    private function is_output_systemlog_csv($type){
        //種別がsiの場合
        if($type == 'si'){
            //デバックモードなら、システムログファイルに出力する
            if($this->debug_mode == '1'){
                return true;
            }else{
                //デバックモードでなければ、システムログファイルに出力しない
                return false;
            }
        }else{
            //種別がsi以外(nm,wn,er,ok,sy)なら、システムログファイルに出力する
            return true;
        }
    }

    //@param date $created_at 現在時刻
    //@param string $type 種別
    //@param string $function 機能
    //@param string $log ログ
    //@return array ログファイルに出力するcsv
    //ログファイルに出力するcsvの作成
    private function create_log_csv($created_at, $type, $function, $log){
        //ログの中のダブルクォーテーションを取り除く
        $log = str_replace('"', '', $log);
        return [$created_at, $type, $this->user, $function, $log];
    }

    //@param date $created_at 現在時刻
    //@param string $type 種別
    //@param string $program_pass プログラムのパス
    //@param string $function 機能
    //@param string $log ログ
    //@return array システムログファイルに出力するcsv
    //システムログファイルに出力するcsvの作成
    private function create_system_log_csv($created_at, $type, $program_pass, $function, $log){
        //ログの中のダブルクォーテーションを取り除く
        $log = str_replace('"', '', $log);
        return [$created_at, $type, $this->client_id, $this->user, $function, $program_pass, $log];
    }

    //@return int idを増加した数字
    //次のテーブルに入れるidを今までのidから増加して取得する。
    private function increment_log_id(){
        //@var array ログテーブルのidの配列
        $result = DB::select('select log_id from dclg01 order by log_id desc limit 1');

        //データがなければ、1を返す
        if($result == []){
            return 1;
        }else{
            //@var int 現在のログidの最高値
            $max = $result[0]->log_id;
            //最高値を増加する
            $max++;
            return $max;
        }
    }

    //関数名から呼び出し元のクラス名と関数名を取得する
    //@param string $program_row プログラムのパス
    //@return string プログラムのパス
    private function get_program_path($program_pass){

        //@var array デバックトレース
        $trace = debug_backtrace();
        //デバックトレースを探索する
        //例外処理以外の場合
        for($i = 0; $i < count($trace); $i++){
            //functionの名前が__callStaticの場合
            if($trace[$i]['function'] == $program_pass){
                //@var sring ファイルフルパス
                $file_fullpass = $trace[$i - 1]['file'];
                //@var string 行番号
                $line = $trace[$i - 1]['line'];
                //ファイルフルパスと行番号を結合
                return $file_fullpass . ':' . $line;
            }
        }
        for($i = 0; $i < count($trace); $i++){
            //例外処理の場合
            //classのプロパティが存在して、classの名前が、App\Exceptions\Handler
            if(array_key_exists('class', $trace[$i]) && $trace[$i]['class'] == $program_pass){
                if($program_pass == 'App\Exceptions\Handler'){
                    //@var array エラーデータ
                    $error_row = $trace[$i]['args'][0];
                    //@var string ファイルフルパス
                    $file_fullpass = $error_row->getFile();
                    //@var string 行番号
                    $line = $error_row->getLine();
                    //ファイルフルパスと行番号を結合
                    return $file_fullpass . ':' . $line;
                }
            }
        }
        
        //クラス名とメソッド名を結合して返す
        return $program_pass;
    }

}

?>