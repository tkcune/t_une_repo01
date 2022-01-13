<?php

namespace App\Libraries\php\Service;

use Exception;

//POP方式のメール受信クラス
//telnetコマンドを送信して、メールを取得する
class POPClient {

    //@var resource ストリームソケットクラス(https://www.php.net/manual/ja/function.stream-socket-client.phpを参照)
    private $client;
    //@var string メールアカウント名
    private $name;
    //@var string パスワード
    private $password;
    
    //コンストラクタ
    //@param string $name メールアカウント名
    //@param string $password パスワード
    //@param int recieving_server 受信サーバー名
    //@param string $recieving_port_number 受信サーバーのポート番号
    private function __construct($name, $password, $recieving_server, $recieving_port_number){

        //メールアカウント名とパスワードをプロパティにセットする
        $this->name = $name;
        $this->password = $password;

        try {
            //ストリームソケットクラスの作成
            $this->client = stream_socket_client('ssl://'.$recieving_server.':'.$recieving_port_number, $err, $errno, 10);
        } catch (Exception $e) {
            //作成失敗したら、nullをセットする
            $this->client = null;
        }
    }

    //POP方式で認証、接続をする
    private function connect(){
        try{
            //@var string 受信サーバーからの返信を取得する
            $r = fgets($this->client, 1024);

            //OKの文字がなければ、失敗
            if(mb_substr($r, 1, 2) != "OK"){
                return false;
            }

            //ユーザー名を送信する
            fputs($this->client, sprintf("USER %s\r\n", $this->name));
            //返信を取得する
            $r = fgets($this->client, 1024);
        
            //OKの文字がなければ、失敗
            if(mb_substr($r, 1, 2) != "OK"){
                return false;
            }

            //パスワードを送信する
            fputs($this->client, sprintf("PASS %s\r\n", $this->password));
            //返信を取得する
            $r = fgets($this->client, 1024);
        
            //OKの文字がなければ、失敗
            if(mb_substr($r, 1, 2) != "OK"){
                return false;
            }
        }catch(Exception $e){
            //なんらかのエラーであれば、失敗
            return false;
        }
        return true;
    }

    //最新のメールを取得する
    //@return array メールの配列
    public function get_latest_mail(){
        try{
            //メールの総数を取得するコマンドを送信する
            fputs($this->client, "STAT\r\n");
            //@var string 返信
            $r = fgets($this->client, 1024);
            //@var string メールの総数
            $number = mb_substr($r, 4, 2);
        
            //メールを取得するコマンドを送信する
            fputs($this->client, "RETR ".$number."\r\n");
            //返信
            $r = fgets($this->client, 512);
            //@var int メールの大きさ
            $size = mb_substr($r, 4, 4);
            //@var int 受信メールの大きさを数える
            $count = 0;
            //@var array メールの仮保存の変数
            $mail_array = [];
            
            //メールの大きさと数えたメールの大きさを合致させる
            while($count != $size){
                //@var string 受信メールの一行
                $line = fgets($this->client);
                //一行をセットする
                $mail_array[] = $line;
                //メールの大きさを数える
                $count += strlen($line);
            }
            // $s = file_get_contents('mail_struct');
            // $a = unserialize($s);

            // $this->get_body($a);
            // dd($mail_array);
            $mail = $mail_array;
            //@var array メールの本文を取得する
            // $mail = $this->trim_latest($mail_array);
        }catch(Exception $e){
            //なんらかのエラーがあれば、メールを空にする
            $mail = [];
        }
        return $mail;
    }

    private function get_body($mail_array){
        [$header_array, $body_array] = $this->divide_header_body($mail_array);
        $header_array = $this->parse_header($header_array);
        $this->parse_body($header_array);
        dd($header_array);
    }

    private function parse_header($header_array){
        
        $headers = array();
        $prev_header = null;
        for($i = 0; $i < count($header_array); $i++){
            $line = $header_array[$i];

            if($line == ""){
                break;
            }
            
            if (substr($line, 0, 1) === "\t") {
                $line = substr($line, 1);
                $line = trim(rtrim($line));
                if ($prev_header !== null) {
                    $headers[$prev_header][] = $line;
                }
            }elseif (substr($line, 0, 1) === " ") {
                $line = substr($line, 1);
                $line = trim(rtrim($line));
                if ($prev_header !== null) {
                    if (!isset($headers[$prev_header])) {
                        $headers[$prev_header] = "";
                    }
                    if (is_array($headers[$prev_header])) {
                        $headers[$prev_header][] = $line;
                    }else{
                        $headers[$prev_header] .= $line;
                    }
                }
            }else{
                if (($pos = strpos($line, ":")) > 0) {
                    $key = trim(rtrim(strtolower(substr($line, 0, $pos))));
                    $key = str_replace("-", "_", $key);

                    $value = trim(rtrim(substr($line, $pos + 1)));
                    if (isset($headers[$key])) {
                        $headers[$key][]  = $value;
                    }else{
                        $headers[$key] = [$value];
                    }
                    $prev_header = $key;
                }
            }
        }
        
        foreach($headers as $key => $values) {
            if (isset($imap_headers[$key])) continue;
            $value = null;
            switch($key){
                case 'from':
                case 'to':
                case 'cc':
                case 'bcc':
                case 'reply_to':
                case 'sender':
                    $headers[$key."address"] = implode(", ", $values);
                    break;
                case 'subject':
                    $value = implode(" ", $values);
                    break;
                default:
                    if (is_array($values)) {
                        foreach($values as $k => $v) {
                            if ($v == "") {
                                unset($values[$k]);
                            }
                        }
                        $available_values = count($values);
                        if ($available_values === 1) {
                            $value = array_pop($values);
                        } elseif ($available_values === 2) {
                            $value = implode(" ", $values);
                        } elseif ($available_values > 2) {
                            $value = array_values($values);
                        } else {
                            $value = "";
                        }
                    }
                    if($key == 'content_type'){
                        $value = str_replace(';', '', $values[0]);
                        for($i = 1; $i < count($values); $i++){
                            $pos = strpos($values[$i], "=");
                            $temp_key = trim(rtrim(strtolower(substr($values[$i], 0, $pos))));
                            $temp_key = str_replace("-", "_", $temp_key);

                            $temp_value = trim(rtrim(substr($values[$i], $pos + 1)));
                            $temp_value = str_replace(';', '', $temp_value);
                            $temp_value = str_replace('"', '', $temp_value);
                            $headers[$temp_key] = $temp_value;
                        }
                    }
                    if($key == 'received'){
                        $value = implode(',', $values);
                    }
                    break;
            }
            $headers[$key] = $value;
        }
        return $headers;
    }

    private function get_main_boundary($body_array, $boundary_string){
        $main_boundary = [];
        $is_boundary = false;
        foreach($body_array as $line){
            $line = str_replace("\r\n", '', $line);
            
            if(substr($line, 2) == $boundary_string && $is_boundary == false){
                $is_boundary = true;
                continue;
            }
            if(substr($line, 0, -2) == "--".$boundary_string){
                break;
            }
            if($is_boundary == true){
                $main_boundary[] = $line;
            }
        }
        return $main_boundary;
    }

    private function divide_header_body($mail_array){
        $header_array = [];
        $body_array = [];
        $is_body = false;
        foreach($mail_array as $line){
            if($line == "\r\n" && $is_body == false){
                $is_body = true;
                continue;
            }
            if($is_body == true){
                $body_array[] = $line;
            }
            if($is_body == false){
                $header_array[] = $line;
            }
        }
        return [$header_array, $body_array];
    }

    //POPClientを作成する
    //@param string $name メールアカウント名
    //@param string $password パスワード
    //@param int recieving_server 受信サーバー名
    //@param string $recieving_port_number 受信サーバーのポート番号
    //@return POPClient
    public static function createPOPClient(string $name,string $password,string $recieving_server,string $recieving_port_number){
        //@var POPClient POPClientの作成
        $client = new static($name, $password, $recieving_server, $recieving_port_number);
        //認証接続が失敗すれば、nullを返す
        if($client->connect() == false){
            return null;
        }
        return $client;
    }
}

?>