<?php

namespace App\Libraries\php;

use Exception;
use Webklex\IMAP\Facades\Client;
use App\Libraries\php\HeaderMessage;

//IMAPメールクライアント
class IMAPClient {

    //@var IMAP
    private $client;
    
    //コンストラクタ
    //@param string $name メールアカウント名
    //@param string $password パスワード
    //@param string recieving_server 受信サーバー名
    //@param int $recieving_port_number 受信サーバーのポート番号
    //@return IMAPClient
    private function __construct($name, $password, $recieving_server, $recieving_port_number){

        //@var array IMAPクラスを作成するアカウント
        $account = [
            'host' => $recieving_server,
            'port' => $recieving_port_number,
            'protocol'  => "imap",
            'encryption' => 'ssl',
            'validate_cert' => true,
            'username' => $name,
            'password' => $password,
            'timeout' => 10
        ];

        //IMAPクラスの作成
        $this->client = Client::make($account);
    }

    //認証接続をする
    //@return boolean 認証の結果
    private function connect(){
        try {
            $this->client->connect();
        } catch (Exception $e) {
            //なんらかのエラーであれば、falseを返す
            return false;
        }
        //認証の結果を返す
        return $this->client->isConnected();
    }

    //最新メールを取得する
    //@return array メール本文の配列
    public function get_latest_mail(){
        try{
            //@var array 全てのメッセージの配列
            $all_messages = $this->client->getFolder("INBOX")->messages()->all();
            //@var int 全てのメッセージの数
            $latest_number = $all_messages->count();
            
            //@var string メール本文
            $mail = $all_messages->get()[$latest_number - 1]->getTextBody();
            //@var array メールを改行で分割し、配列にする
            $mail = preg_split("/\r\n/", $mail);
            //メールの半角空白をhtmlの空白に変換する
            $mail = preg_replace('/ /', '&nbsp;', $mail);
        }catch(Exception $e){
            //なんらかのエラーであれば、メールを空にする
            $mail = [];
        }
        return $mail;
    }

    //IMAPClientクラスの作成
    //@param string $name メールアカウント名
    //@param string $password パスワード
    //@param string recieving_server 受信サーバー名
    //@param int $recieving_port_number 受信サーバーのポート番号
    //@return mix IMAPClient,null
    public static function createIMAPClient(string $name,string $password,string $recieving_server,string $recieving_port_number){
        //@var IMAPClient IMAPClientクラスの作成
        $client = new static($name, $password, $recieving_server, $recieving_port_number);
        if($client->connect() == false){
            //認証接続に失敗すれば、nullを返す
            return null;
        }
        return $client;
    }
}

?>