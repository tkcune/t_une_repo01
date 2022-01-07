<?php

namespace App\Libraries\php;

use Exception;
use Webklex\IMAP\Facades\Client;
use App\Libraries\php\HeaderMessage;

class POPClient {

    private $client;
    private $name;
    private $password;
    
    private function __construct($name, $password, $recieving_server, $recieving_port_number){

        $this->name = $name;
        $this->password = $password;

        $this->client = stream_socket_client('ssl://'.$recieving_server.':'.$recieving_port_number, $err, $errno, 10);
        stream_set_timeout($this->client, 10);
    }

    private function connect(){
        try{
            $r = fgets($this->client, 1024);

            if(mb_substr($r, 1, 2) != "OK"){
                return false;
            }

            fputs($this->client, sprintf("USER %s\r\n", $this->name));
            $r = fgets($this->client, 1024);
        
            if(mb_substr($r, 1, 2) != "OK"){
                return false;
            }

            fputs($this->client, sprintf("PASS %s\r\n", $this->password));
            $r = fgets($this->client, 1024);
        
            if(mb_substr($r, 1, 2) != "OK"){
                return false;
            }
        }catch(Exception $e){
            return false;
        }
        return true;
    }

    public function get_latest_mail(){
        try{
            fputs($this->client, "STAT\r\n");
            $r = fgets($this->client, 1024);
            $number = mb_substr($r, 4, 2);
        
            fputs($this->client, "RETR ".$number."\r\n");
            $r = fgets($this->client, 512);
            
            $size = mb_substr($r, 4, 4);
            $count = 0;
            $mail_array = [];
            while($count != $size){
                $line = fgets($this->client);
                $mail_array[] = $line;
                $count += strlen($line);
            }
            $mail = $this->trim_latest($mail_array);
            HeaderMessage::set_header_message('mmnwok0003');
        }catch(Exception $e){
            $mail = '';
            HeaderMessage::set_header_message('mmnwer0002');
        }
        return $mail;
    }

    private function trim_latest($mail_array){
        $latest_mail = [];
        $is_mail_body = false;
        foreach($mail_array as $line){
            if($line == "\r\n"){
                $is_mail_body = true;
            }
            if($is_mail_body == true){
                $line = preg_replace("/ /", '&nbsp;',$line);
                $latest_mail[] = $line;
            }
        }
        array_shift($latest_mail);
        array_pop($latest_mail);
        return $latest_mail;
    }

    public static function createPOPClient(string $name,string $password,string $recieving_server,string $recieving_port_number){
        $client = new static($name, $password, $recieving_server, $recieving_port_number);
        if($client->connect() == false){
            return null;
        }
        return $client;
    }
}

?>