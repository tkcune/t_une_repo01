<?php

namespace App\Libraries\php;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

//メッセージクラス
//エラーID=9001
Class Message{

    //メッセージ番号からメッセージを取得する
    //@param string $message_code メッセージ番号
    //@param array $message_string メッセージに挿入する文字,可変長引数
    //@return array メッセージ、種別、機能
    public static function get_message(string $message_code, $message_string){

        //@var array 文字を挿入しないメッセージ
        $message_list = Config::get('message');

        //config/message.phpがない場合は、エラーメッセージを返す
        if($message_list == null){
            //ない場合のID=9001
            $message = sprintf('エラーです。エラーID【%s】', '9001');
            return [$message, 'er', 'cm'];
        }
        
        //@var int 引数の挿入するメッセージの数
        $message_string_count = count($message_string);

        //@var string メッセージリストからメッセージを取得する
        $message = Arr::get($message_list, $message_code, '');

        //メッセージ番号が一致しない場合
        if($message == ''){
            
            //メッセージの引数が間違っている場合のID=9001
            $message = sprintf(Arr::get($message_list, 'mhcmer0001'), '9001');
            return [$message, 'er', 'cm'];
        
        }

        //@var int %sの数を数える
        $count_percent_s = mb_substr_count($message, '%s');
        //メッセージに挿入する文字がひとつの場合
        if($count_percent_s == 1){
            //引数の挿入する文字の数がひとつで挿入する文字が文字の場合
            if($message_string_count == 1 && isset($message_string[0]) && is_string($message_string[0])){
                $message = sprintf($message, $message_string[0]);
            }else{
                //引数の挿入する文字が合わない場合ID=9001
                $message = sprintf(Arr::get($message_list, 'mhcmer0001'), '9001');
                return [$message, 'er', 'cm'];
            }
        }else if($count_percent_s == 2){
            //メッセージに挿入する文字がふたつでかつ挿入するふたつの文字が文字の場合
            if($message_string_count == 2 && isset($message_string[0]) && isset($message_string[1]) && is_string($message_string[0]) && is_string($message_string[1])){
                //引数の挿入する文字の数がひとつの場合
                $message = sprintf($message, $message_string[0], $message_string[1]);
            }else{
                //引数の挿入する文字が合わない場合ID=9001
                $message = sprintf(Arr::get($message_list, 'mhcmer0001'), '9001');
                return [$message, 'er', 'cm'];
            }
        }

        //メッセージ番号が一致する場合
        //@var string メッセージ番号から種別を取得する
        $type = mb_substr($message_code, 4, 2);
        //@var string メッセージ番号から機能を取得する
        $function = mb_substr($message_code, 2, 2);

        return [$message, $type, $function];
    }

    //メッセージ番号からメッセージと対処メッセージを取得する
    //@param string $message_code メッセージ番号
    //@param array $message_string メッセージに挿入する文字,可変長引数
    //@return array メッセージ、種別、機能, 対処メッセージ
    public static function get_message_handle(string $message_code, $message_string){
        //@var array メッセージ、種別、機能
        [$message, $type, $function] = Message::get_message($message_code, $message_string);
        
        //@var array 対処メッセージリスト
        $handle_message_list = Config::get('handlemessage');

        //config/handlemessage.phpがない場合は、エラーメッセージを返す
        if($handle_message_list == null){
            //ない場合のID=9001
            $message = sprintf('エラーです。エラーID【%s】', '9001');
            return [$message, 'er', 'cm', ''];
        }

        //@var string メッセージリストからメッセージを取得する
        $handle_message = Arr::get($handle_message_list, $message_code, '');

        return [$message, $type, $function, $handle_message];
    }
}

?>