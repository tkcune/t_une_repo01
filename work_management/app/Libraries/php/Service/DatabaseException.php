<?php

    namespace App\Libraries\php\Service;

    use App\Facades\OutputLog;
    use App\Libraries\php\Message;
    use App\Http\Controllers\PtcmtrController;

    /**
     * 作業管理システムデータベースの例外処理クラス
     */

    class DatabaseException {

        /**
         * 汎用メッセージ処理
         * @param Illuminate\Database\QueryException $e エラー内容
         * 
         * @var array $message メッセージ
         */
        public static function common($e){

            OutputLog::log(__CLASS__, 'sy', '00', $e->getMessage());
            $message = Message::get_message_handle('mhcmer0001',[0=>'01']);
            session(['message'=>$message[0],'handle_message'=>$message[3]]);
            
        }

        /**
         * データ取得ミスメッセージ処理
         * @param Illuminate\Database\QueryException $e エラー内容
         * 
         * @var array $message メッセージ
         */
        public static function dataCatchMiss($e){

            OutputLog::log(__CLASS__, 'sy', '00', $e->getMessage());
            $message = Message::get_message_handle('mhcmer0001',[0=>'02']);
            session(['message'=>$message[0],'handle_message'=>$message[3]]);

        }

        /**
         * 汎用エラーメッセージ処理
         * @param $e エラー内容
         * @param $num エラー番号
         * 
         * @var array $message メッセージ
         */
        public static function commonError($e,$num){

            OutputLog::log(__CLASS__, 'sy', '00', $e);
            $message = Message::get_message_handle('mhcmer0001',[0=>$num]);
            session(['message'=>$message[0],'handle_message'=>$message[3]]);
            
        }
    }