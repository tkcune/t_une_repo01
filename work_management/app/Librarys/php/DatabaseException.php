<?php

    namespace App\Librarys\php;

    use App\Librarys\php\Message;
    use App\Http\Controllers\PtcmtrController;

    /**
     * 作業管理システムデータベースの例外処理クラス
     */

    class DatabaseException {

        /**
         * 汎用メッセージ処理
         * @param Illuminate\Database\QueryException $e エラー内容
         * @param array $message メッセージ
         */
        public static function common($e){

            OutputLog::log(__CLASS__, 'sy', '00', $e->getMessage());
            $message = Message::get_message('mhcmer0001',[0=>'01']);
            session(['message'=>$message[0]]);
            
        }

        /**
         * データ取得ミスメッセージ処理
         * @param Illuminate\Database\QueryException $e エラー内容
         * @param array $message メッセージ
         */
        public static function dataCatchMiss($e){

            OutputLog::log(__CLASS__, 'sy', '00', $e->getMessage());
            $message = Message::get_message('mhcmer0001',[0=>'02']);
            session(['message'=>$message[0]]);

        }
    }