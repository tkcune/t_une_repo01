<?php

namespace App\Libraries\php;

use Exception;
use Webklex\IMAP\Facades\Client;
use App\Libraries\php\HeaderMessage;

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
            $mail = $mail_array;
            //@var array メールの本文を取得する
            // $mail = $this->trim_latest($mail_array);
        }catch(Exception $e){
            //なんらかのエラーがあれば、メールを空にする
            $mail = [];
        }
        return $mail;
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