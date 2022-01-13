<?php

namespace App\Libraries\php\Service;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;

//SMTPクライアントクラス
class SMTPclient {

    //@var PHPMAiler メール送信のクライアント
    private $client;
    
    //コンストラクタ
    //@param string $sending_server 送信サーバー名
    //@param int $sending_port_number 送信サーバーのポート番号
    //@param string $email メールアドレス
    public function __construct(string $sending_server,string $sending_port_number,string $email){

        //@var PhpMailerクラス メーラークラス
        $this->client = new PHPMailer();
        
        //smtp使用を設定する
        $this->client->isSMTP();
        //送信ホストサーバー
        $this->client->Host = $sending_server;
        //ポート番号(smtp.muumuu-mail.comのポート番号:587)
        $this->client->Port = $sending_port_number;
        //文字セットutf8
        $this->client->CharSet = PHPMailer::CHARSET_UTF8;
        //送信先を設定する
        $this->client->addAddress($email);
    }

    //テスト用のメールを設定する
    //@param string $to 送信先の名前
    //@param string $email 送信元メールアドレス
    private function set_test_mail_info($to, $email){
        //@var string 送信元の名前
        $from_name = $to;
        //@var string 送信元のメールアドレス
        $from_email = $email;
        //差出人を設定する
        $this->client->setFrom($from_email);
        //件名を設定する
        $this->client->Subject = '作業管理システムメール送信試験';
        
        //本文の設定をする
        $this->client->Body =   $to ."様。\n本メールは".'作業管理システム'."からの、メール送信試験です。\n  試験者:". $from_name ."\n"."  メールアドレス:". $from_email ."\n"."受信できた旨を上記メールアドレスに送信してください。\n本メールに返信していただいても受信できません。\n";
    }

    //メールを送信する
    //@param string $to 送信先の名前
    //@param string $email 送信元メールアドレス
    //@return boolean メール送信成功か、判断する
    public function send(string $to, string $email){
        //テスト用のメールを設定する
        $this->set_test_mail_info($to, $email);
        try {
            //@var boolean メールを送信し、結果を確認する
            $is_send = $this->client->send();
        } catch (Exception $e) {
            //何らかの異常が起これば、失敗
            $is_send = false;
        }
        
        return $is_send;
    }
}

?>