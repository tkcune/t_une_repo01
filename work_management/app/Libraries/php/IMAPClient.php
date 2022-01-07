<?php

namespace App\Libraries\php;

use Exception;
use Webklex\IMAP\Facades\Client;
use App\Libraries\php\HeaderMessage;

class IMAPclient {

    private $client;
    
    private function __construct($name, $password, $recieving_server, $recieving_port_number){

        $account = [
            'host' => $recieving_server,
            'port' => $recieving_port_number,
            'protocol'  => "imap",
            'encryption' => 'ssl',
            'validate_cert' => true,
            'username' => $name,
            'password' => $password,
        ];

        $this->client = Client::make($account);
    }

    private function connect(){
        $this->client->connect();
        return $this->client->isConnected();
    }

    public function get_latest_mail(){
        try{
            $all_messages = $this->client->getFolder("INBOX")->messages()->all();
            $latest_number = $all_messages->count();
            $mail = $all_messages->get()[$latest_number - 1]->getTextBody();
            HeaderMessage::set_header_message('mmnwok0003');
        }catch(Exception $e){
            $mail = '';
            HeaderMessage::set_header_message('mmnwer0002');
        }
        return $mail;
    }

    public static function createIMAPClient(string $name,string $password,string $recieving_server,string $recieving_port_number){
        $client = new static($name, $password, $recieving_server, $recieving_port_number);
        if($client->connect() == false){
            return null;
        }
        return $client;
    }
}

?>