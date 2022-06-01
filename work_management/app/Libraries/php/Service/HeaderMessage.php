<?php

    namespace App\Libraries\php\Service;

    //ヘッダーに，メッセージをセットする。
    class HeaderMessage {

        //ヘッダーにメッセージをセットする
        //@param string $message_code メッセージ番号
        //@param array $message メッセージ
        public static function set_header_message(string $message_code, ...$message)
        {
            session()->forget('message');
            $message = Message::get_message($message_code, $message);
            session(['message'=>$message[0]]);
        }
    }