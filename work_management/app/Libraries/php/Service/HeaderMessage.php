<?php

    namespace App\Libraries\php\Service;

    class HeaderMessage {

        public static function set_header_message(string $message_code, ...$message)
        {
            session()->forget('message');
            $message = Message::get_message($message_code, $message);
            session(['message'=>$message[0]]);
        }
    }