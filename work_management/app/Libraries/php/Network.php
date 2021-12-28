<?php

    namespace App\Libraries\php;

    use Illuminate\Support\Facades\DB;
    use PHPMailer\PHPMailer\PHPMailer;
    use Illuminate\Support\Str;

    class Network {

        public static function create_network_client($request){
            
            // ネットワーク設定のフォームから値を取得
            $client_id = "aa".Str::random(8);
            $name = $request->name;
            $email = $request->email;
            $password = $request->password;
            $recieving_server = $request->recieving_server;
            $recieving_server_way = $request->recieving_server_way;
            $recieving_port_number = $request->recieving_port_number;
            $sending_server = $request->sending_server;
            $sending_port_number = $request->sending_port_number;
            
            DB::insert('insert into dcnw01 (client_id, name, email, password, recieving_server, recieving_server_way, recieving_port_number, sending_server, sending_server_way, sending_port_number)
            VALUE (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [$client_id, $name, $email, $password, $recieving_server, $recieving_server_way, $recieving_port_number, $sending_server, '1', $sending_port_number]);
        }

        public static function crete_send_mail_client($name){

            $user_info = Db::select('select email, sending_server, sending_port_number from dcnw01 where name = ?', [$name])[0];
            //@var PhpMailerクラス メーラークラス
            $mail = new PHPMailer();
            //ログを詳しく出力するように設定する
            $mail->SMTPDebug = 3;
            //smtp使用を設定する
            $mail->isSMTP();
            //送信ホストサーバー
            $mail->Host = $user_info->sending_server;
            //ポート番号(smtp.muumuu-mail.comのポート番号:587)
            $mail->Port = $user_info->sending_port_number;
            //文字セットutf8
            $mail->CharSet = PHPMailer::CHARSET_UTF8;
            //差出人を設定する
            $mail->setFrom('roma@roma.com');
            //件名を設定する
            $mail->Subject = '作業管理システムメール送信試験';
            //送信先を設定する
            $mail->addAddress($user_info->email);
            //本文の設定をする
            $mail->Body =   $name ."様。\n本メールは".'作業管理システム'."からの、メール送信試験です。\n  試験者:". $name ."\n"."  メールアドレス:". $user_info->email ."\n"."受信できた旨を上記メールアドレスに送信してください。\n本メールに返信していただいても受信できません。\n";
        
            return $mail;
        }
    }