<?php

    namespace App\Libraries\php\Service;

    use App\Facades\OutputLog;
    use App\Libraries\php\Service\Message;
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

        /**
         * 複写エラーメッセージ処理
         * @param PDOException $pe PDOException
         * @param string $file エラー元のファイル名
         * 
         * @var string $line エラー行
         * @var array $message メッセージ
         */
        public static function PDOError($pe, $file){

            $line = self::get_error_line($pe, $file);
            //ログとヘッダーメッセージの設定
            OutputLog::log($file, 'sy', '00', $file.'->'.$line.'行->'.$pe->getMessage());
            $message = Message::get_message_handle('mhcmer0001',[0 => '01']);
            session(['message'=>$message[0], 'handle_message'=>$message[3]]);
            
        }

        /**
         * PDOExceptionのエラー行を取得する。
         * @param PDOException $pe PDOException
         * @param string $file エラー元のファイル名
         * 
         * @var array $trace エラー情報の呼び出しのトレース行
         * 
         * @return string エラー行
         */
        private static function get_error_line($pe, $file){
            $trace = $pe->getTrace();
            foreach($trace as $line){
                if($line['file'] == $file){
                    return $line['line'];
                }
            }
            return '';
        }
    }