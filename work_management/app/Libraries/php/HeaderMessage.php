<?php

    namespace App\Libraries\php;

    class HeaderMessage {

        public static function clear_header_message(){
            session()->forget('message');
        }
        public static function set_header_message(string $message_code, ...$message)
        {
            $message = Message::get_message($message_code, $message);
            session(['message'=>$message[0]]);
        }
    }