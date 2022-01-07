<?php

namespace App\Libraries\php;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Facades\OutputLog;
use Exception;
use App\Libraries\php\POPClient;
use App\Libraries\php\IMAPClient;
use App\Libraries\php\SMTPclient;

//error_code 7007
class NetworkClient {

    private $send_client;
    private $receive_client;

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

        public static function get_send_server(string $client_id){
            
            try {
                $user_info = Db::select('select email, sending_server, sending_port_number from dcnw01 where client_id = ?', [$client_id])[0];
            } catch (Exception $e) {
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '7007');
            }
            return $user_info;
        }

        private static function create_send_client(string $sending_server,string $sending_port_number,string $email){
            $client = new SMTPclient($sending_server, $sending_port_number, $email);
            return $client;
        }

        public static function send_test_mail(string $sending_server,string $sending_port_number,string $email,string $name){
            $client = NetworkClient::create_send_client($sending_server, $sending_port_number, $email);
            if($client != null){
                $client->send($name);
            }else{
                HeaderMessage::set_header_message('mmnwer0001');
            }
        }

        private static function create_receive_auth_client(string $name,string $password,string $recieving_server,string $recieving_port_number,string $recieving_server_way){
            
            if($recieving_server_way == 1){
                return POPClient::createPOPClient($name, $password, $recieving_server, $recieving_port_number);
            }else if($recieving_server_way == 2){
                return IMAPClient::createIMAPClient($name, $password, $recieving_server, $recieving_port_number);
            }else{
                return null;
            }
        }

        public static function get_latest_mail(string $name,string $password,string $recieving_server,string $recieving_port_number,string $recieving_server_way){
            $client = NetworkClient::create_receive_auth_client($name, $password, $recieving_server, $recieving_port_number, $recieving_server_way);
            if($client != null){
                $mail = $client->get_latest_mail();
            }else{
                $mail = '';
                HeaderMessage::set_header_message('mmnwer0002');
            }
            return $mail;
        }

        //imap,popのネットワーククライアントのデータベースを取得する
        //@param string $client_id クライアントのid
        //@return IMAPClient
        public static function get_receive_data(string $client_id){
            try {
                $user_info = Db::select('select name, password, recieving_server, recieving_server_way, recieving_port_number from dcnw01 where client_id = ?', [$client_id])[0];
            } catch (Exception $e) {
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '7007');
            }
            
            return $user_info;
        }
    }