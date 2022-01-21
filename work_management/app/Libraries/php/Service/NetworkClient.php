<?php

namespace App\Libraries\php\Service;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Facades\OutputLog;
use Exception;
use App\Libraries\php\Service\POPClient;
use App\Libraries\php\Service\IMAPClient;
use App\Libraries\php\Service\SMTPClient;

//error_code 7007
//ネットワーク設定クラス
//メールの送受信などを管理する
class NetworkClient {

    //@var SMTPClinet 送信クライアントクラス
    private $send_client;
    //@var mix(POPClient, SMTPClient) 受信クライアントクラス
    private $receive_client;


    //テスト用のメソッド。bladeにセットするデフォルトのデータを返す
    //@return array デフォルトのデータ
    public static function get_default_client_data(){
        return array(
            'name' => 'sagyotest@b-forme.net',
            'email' => 'sagyotest@b-forme.net',
            'password' => 'sagyopass',
            'recieving_server' => 'pop3.muumuu-mail.com',
            'recieving_server_way' => '1',
            'recieving_port_number' => '995',
            'sending_server' => 'smtp.muumuu-mail.com',
            'sending_port_number' => '587',
            'test_email' => 'hioki@b-forme.net'
        );
    }

    //ネットワーク設定のデータを保存する
    //@param Request $request
    public static function create_network_client($request){
            
        // ネットワーク設定のフォームから値を取得
        //@var string クライアントのid,今は仮の値
        $client_id = "aa".Str::random(8);
        //@var string メールアカウント名
        $name = $request->name;
        //@var string メールアドレス
        $email = $request->email;
        //@var string パスワード
        $password = $request->password;
        //@var string 受信サーバードメイン名
        $recieving_server = $request->recieving_server;
        //@var int 受信サーバーのプロトコル
        $recieving_server_way = $request->recieving_server_way;
        //@var int 受信サーバーのポート番号
        $recieving_port_number = $request->recieving_port_number;
        //@var string 送信サーバーのドメイン名
        $sending_server = $request->sending_server;
        //@var int 送信サーバーのポート番号
        $sending_port_number = $request->sending_port_number;

        try {
            session([
                'client_id' => 'aa00000001',
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'recieving_server' => $recieving_server,
                'recieving_server_way' => $recieving_server_way,
                'recieving_port_number' => $recieving_port_number,
                'sending_server' => $sending_server,
                'sending_port_number' => $sending_port_number
            ]);
            //ログ出力とメッセージ領域表示
            HeaderMessage::set_header_message('mmnwok0001');
            OutputLog::message_log(__FUNCTION__, 'mmnwok0001');
        } catch (Exception $e) {
            //ログ出力とメッセージ領域表示
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '7007');
            HeaderMessage::set_header_message('mhcmer0001', '7007');
        }
        

        // try {
        //     //dcnw01にデータを保存する
        //     DB::insert('insert into dcnw01 (client_id, name, email, password, recieving_server, recieving_server_way, recieving_port_number, sending_server, sending_server_way, sending_port_number)
        //     VALUE (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
        //     [$client_id, $name, $email, $password, $recieving_server, $recieving_server_way, $recieving_port_number, $sending_server, '1', $sending_port_number]);
            
        //     //ログ出力とメッセージ領域表示
        //     HeaderMessage::set_header_message('mmnwok0001');
        //     OutputLog::message_log(__FUNCTION__, 'mmnwok0001');
        // } catch (Exception $e) {
        //     //ログ出力とメッセージ領域表示
        //     OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '7007');
        //     HeaderMessage::set_header_message('mhcmer0001', '7007');
        // }
    }

    //first送信コード

    //送信サーバーのデータを取得する
    //@param string $client_id クライアントのid
    //@return object ユーザーの送信サーバーのデータ
    private static function get_send_server(string $client_id){
            
        try {
            throw new Exception();
            //@var object ユーザーの送信サーバーのデータ
            $user_info = Db::select('select email, sending_server, sending_port_number from dcnw01 where client_id = ?', [$client_id])[0];
        } catch (Exception $e) {
            //ログ出力とメッセージ領域表示
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '7007');
            HeaderMessage::set_header_message('mhcmer0001', '7007');
        }
        return $user_info;
    }

    //SMTPクライアントクラスの作成
    //@param string $sending_server 送信サーバー名
    //@param int $sending_port_number 送信サーバーのポート番号
    //@param string $email メールアドレス
    //@return SMTPClient
    private static function create_send_client(string $sending_server,string $sending_port_number, $email){
        //@var SMTPClient
        $client = new SMTPclient($sending_server, $sending_port_number, $email);
        return $client;
    }

    //テストメールの送信をする
    //@param string $sending_server 送信サーバー名
    //@param int $sending_port_number 送信サーバーのポート番号
    //@param string $email メールアドレス
    public static function send_test_mail(string $sending_server,string $sending_port_number, $email){
        //@var boolean メール送信成功を判断するフラグ(初期値はfalse)
        $is_send = false;
        //@var SMTPClient
        $client = NetworkClient::create_send_client($sending_server, $sending_port_number, $email);
        
        if($client != null){
            //メールを送信する
            $is_send = $client->send('アウグストゥス', 'roma@roma.com');
        }

        //ログ出力とメッセージ領域表示
        if($is_send == true){
            //メール送信が成功しているなら
            OutputLog::message_log(__FUNCTION__, 'mmnwok0002');
            HeaderMessage::set_header_message('mmnwok0002');
        }else{
            //メール送信が失敗したなら
            OutputLog::message_log(__FUNCTION__, 'mmnwer0001');
            HeaderMessage::set_header_message('mmnwer0001');
        }
    }

    //人事画面からテストメールの送信をする
    //@param string $client_id クライアントID
    //@param string $name 名前
    //@param string $email メールアドレス
    public function send_ji_test_mail(string $client_id,string $name,string $email){
        //@var boolean メール送信成功を判断するフラグ(初期値はfalse)
        $is_send = false;
        //@var array 送信サーバーの情報
        $user_info = NetworkClient::get_send_server($client_id);
        if($user_info != null){
            //@var SMTPClient
            $client = NetworkClient::create_send_client($user_info->sending_server, $user_info->sending_port_number, $user_info->email);
            if($client != null){
                //メールを送信する
                $is_send = $client->send($name, $email);
            }
        }

        //ログ出力とメッセージ領域表示
        if($is_send == true){
            //メール送信が成功しているなら
            OutputLog::message_log(__FUNCTION__, 'mmnwok0002');
            HeaderMessage::set_header_message('mmnwok0002');
        }else{
            //メール送信が失敗したなら
            OutputLog::message_log(__FUNCTION__, 'mmnwer0001');
            HeaderMessage::set_header_message('mmnwer0001');
        }
    }

    //end送信コード

    //first受信コード

    //認証接続を行ったPOPClient,IMAPClientクラスの作成
    //@param string $name メールアカウント名
    //@param string $password パスワード
    //@param string recieving_server 受信サーバー名
    //@param int $recieving_port_number 受信サーバーのポート番号
    //@param string $recieving_server_way 受信サーバー方法
    //@return mix POPClient,IMAPClient,nullのどれかを返す
    private static function create_receive_auth_client(string $name,string $password,string $recieving_server,string $recieving_port_number,string $recieving_server_way){
        
        //受信サーバーの方法が1であれば、POP
        if($recieving_server_way == 1){
            return POPClient::createPOPClient($name, $password, $recieving_server, $recieving_port_number);
        }else if($recieving_server_way == 2){
            //受信サーバーの方法が2であれば、IMAP
            return IMAPClient::createIMAPClient($name, $password, $recieving_server, $recieving_port_number);
        }else{
            //1,2以外なら、nullを返す
            return null;
        }
    }

    //最新のメールを取得する
    //@param string $name メールアカウント名
    //@param string $password パスワード
    //@param string recieving_server 受信サーバー名
    //@param int $recieving_port_number 受信サーバーのポート番号
    //@param string $recieving_server_way 受信サーバー方法
    //@return mix array,string 取得したメール
    public static function get_latest_mail(string $name,string $password,string $recieving_server,string $recieving_port_number,string $recieving_server_way){
        //@var mix POPclientまたはIMAPClient
        $client = NetworkClient::create_receive_auth_client($name, $password, $recieving_server, $recieving_port_number, $recieving_server_way);
        
        if($client != null){
            //@var array メール
            $mail = $client->get_latest_mail();
        }else{
            //POPClientまたは、IMAPClientの作成ができなかったら
            //メールを空にする
            $mail = [];
        }

        //ログ出力とメッセージ領域表示
        if($mail != []){
            //メール受信が成功しているなら
            OutputLog::message_log(__FUNCTION__, 'mmnwok0003');
            HeaderMessage::set_header_message('mmnwok0003');
        }else{
            //メール受信が失敗したなら
            OutputLog::message_log(__FUNCTION__, 'mmnwer0002');
            HeaderMessage::set_header_message('mmnwer0002');
        }
        return $mail;
    }

    //現在のスパイラルでは使用しないので、削除
    //imap,popのネットワーククライアントのデータベースを取得する
    //@param string $client_id クライアントのid
    //@return IMAPClient
    // private static function get_receive_data(string $client_id){
    //     try {
    //         //@var array ユーザーの情報
    //         $user_info = Db::select('select name, password, recieving_server, recieving_server_way, recieving_port_number from dcnw01 where client_id = ?', [$client_id])[0];
    //     } catch (Exception $e) {
    //         //ログ出力とメッセージ領域表示
    //         OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '7007');
    //         HeaderMessage::set_header_message('mhcmer0001', '7007');
    //     } 

    //     return $user_info;
    // }

    //end受信コード
}