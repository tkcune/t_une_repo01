<?php

namespace App\Libraries\php;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use App\Libraries\php\HeaderMessage;

class SMTPclient {

    private $client;
    
    public function __construct(string $sending_server,string $sending_port_number,string $email){

        //@var PhpMailerクラス メーラークラス
        $this->client = new PHPMailer();

        //ログを詳しく出力するように設定する。デバッグ用
        // $this->client->SMTPDebug = 3;
        
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

    private function set_mail_info($to){
        $from_name = 'アウグストゥス';
        $from_email = 'roma@roma.com';
        //差出人を設定する
        $this->client->setFrom($from_email);
        //件名を設定する
        $this->client->Subject = '作業管理システムメール送信試験';
        
        //本文の設定をする
        $this->client->Body =   $to ."様。\n本メールは".'作業管理システム'."からの、メール送信試験です。\n  試験者:". $from_name ."\n"."  メールアドレス:". $from_email ."\n"."受信できた旨を上記メールアドレスに送信してください。\n本メールに返信していただいても受信できません。\n";
    }

    public function send(string $to){
        $this->set_mail_info($to);
        $is_send = $this->client->send();
        if($is_send){
            HeaderMessage::set_header_message('mmnwok0002');
        }else{
            HeaderMessage::set_header_message('mmnwer0001');
        }
    }
}

?>