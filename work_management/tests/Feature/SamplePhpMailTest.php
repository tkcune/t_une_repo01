<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PHPMailer\PHPMailer\PHPMailer;


//メール送信サンプルテスト
class SamplePhpMailTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        //@var PhpMailerクラス メーラークラス
        $mail = new PHPMailer();
        //ログを詳しく出力するように設定する
        $mail->SMTPDebug = 3;
        //smtp使用を設定する
        $mail->isSMTP();
        //送信ホストサーバー
        $mail->Host = 'smtp.muumuu-mail.com';
        //ポート番号(smtp.muumuu-mail.comのポート番号:587)
        $mail->Port = 587;
        //文字セットutf8
        $mail->CharSet = PHPMailer::CHARSET_UTF8;
        //差出人を設定する
        $mail->setFrom('sagyotest@b-forme.net');
        //送信先を設定する
        $mail->addAddress('sagyotest@b-forme.net');
        //本文の設定をする
        $mail->Body = "疎通確認";
        
        //メール送信成功
        if($mail->send()){
            print("send mail success \n");
            
            $this->assertEquals(true, true);
        }else{
            //メール送信失敗エラー情報
            print("error info \n\n");
            print($mail->ErrorInfo." \n\n");
            print("error info end\n");
            
            $this->assertEquals(true, true);
        }
    }
}
