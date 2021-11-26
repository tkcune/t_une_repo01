<?php

namespace App\Libraries\php;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

//メッセージクラス
Class Message{

    //メッセージ番号からメッセージを取得する
    //@param string $message_code メッセージ番号
    //@param array $message_string メッセージに挿入する文字
    //@return array メッセージ、種別、機能
    public static function get_message($message_code, $message_string){

        //@var array 文字を挿入しないメッセージ
        $message_list = Config::get('message');
        
        //@var int 引数の挿入するメッセージの数
        $message_string_count = count($message_string);

        //@var string メッセージリストからメッセージを取得する
        $message = Arr::get($message_list, $message_code, '');

        //メッセージ番号が一致しない場合
        if($message == ''){
            
            return ['message_code_error', 'er', '00'];
        
        }else{

            //@var int %sの数を数える
            $count_percent_s = mb_substr_count($message, '%s');
            //メッセージに挿入する文字がひとつの場合
            if($count_percent_s == 1){
                //引数の挿入する文字の数がひとつの場合
                if($message_string_count == 1){
                    $message = sprintf($message, $message_string[0]);
                }else{
                    //引数の挿入する文字の数が合わない場合
                    return [$message, 'er', '00'];
                }
            }else if($count_percent_s == 2){
                //メッセージに挿入する文字がふたつの場合
                if($message_string_count == 2){
                    //引数の挿入する文字の数がひとつの場合
                    $message = sprintf($message, $message_string[0], $message_string[1]);
                }else{
                    //引数の挿入する文字の数が合わない場合
                    return [$message, 'er', '00'];
                }
            }
            //メッセージ番号が一致する場合
            //@var string メッセージ番号から種別を取得する
            $type = mb_substr($message_code, 4, 2);
            //@var string メッセージ番号から機能を取得する
            $function = mb_substr($message_code, 2, 2);

            return [$message, $type, $function];
        }
    }
}

?>