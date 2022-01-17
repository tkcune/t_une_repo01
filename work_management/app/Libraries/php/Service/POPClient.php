<?php

namespace App\Libraries\php\Service;

use Exception;

//POP方式のメール受信クラス
//telnetコマンドを送信して、メールを取得する
class POPClient {

    //@var resource ストリームソケットクラス(https://www.php.net/manual/ja/function.stream-socket-client.phpを参照)
    private $client;
    //@var string メールアカウント名
    private $name;
    //@var string パスワード
    private $password;
    
    //コンストラクタ
    //@param string $name メールアカウント名
    //@param string $password パスワード
    //@param int recieving_server 受信サーバー名
    //@param string $recieving_port_number 受信サーバーのポート番号
    private function __construct($name, $password, $recieving_server, $recieving_port_number){

        //メールアカウント名とパスワードをプロパティにセットする
        $this->name = $name;
        $this->password = $password;

        try {
            //ストリームソケットクラスの作成
            $this->client = stream_socket_client('ssl://'.$recieving_server.':'.$recieving_port_number, $err, $errno, 10);
        } catch (Exception $e) {
            //作成失敗したら、nullをセットする
            $this->client = null;
        }
    }

    //POP方式で認証、接続をする
    private function connect(){
        try{
            //@var string 受信サーバーからの返信を取得する
            $r = fgets($this->client, 1024);

            //OKの文字がなければ、失敗
            if(mb_substr($r, 1, 2) != "OK"){
                return false;
            }

            //ユーザー名を送信する
            fputs($this->client, sprintf("USER %s\r\n", $this->name));
            //返信を取得する
            $r = fgets($this->client, 1024);
        
            //OKの文字がなければ、失敗
            if(mb_substr($r, 1, 2) != "OK"){
                return false;
            }

            //パスワードを送信する
            fputs($this->client, sprintf("PASS %s\r\n", $this->password));
            //返信を取得する
            $r = fgets($this->client, 1024);
        
            //OKの文字がなければ、失敗
            if(mb_substr($r, 1, 2) != "OK"){
                return false;
            }
        }catch(Exception $e){
            //なんらかのエラーであれば、失敗
            return false;
        }
        return true;
    }

    //最新のメールを取得する
    //@return array メールの配列
    public function get_latest_mail(){
        try{
            //メールの総数を取得するコマンドを送信する
            fputs($this->client, "STAT\r\n");
            //@var string 返信
            $r = fgets($this->client, 1024);
            //@var string メールの総数
            $number = mb_substr($r, 4, 2);
        
            //メールを取得するコマンドを送信する
            fputs($this->client, "RETR ".$number."\r\n");
            //返信
            $r = fgets($this->client, 512);
            //@var int メールの大きさ
            $size = mb_substr($r, 4, 4);
            //@var int 受信メールの大きさを数える
            $count = 0;
            //@var array メールの仮保存の変数
            $mail_array = [];
            
            //メールの大きさと数えたメールの大きさを合致させる
            while($count != $size){
                //@var string 受信メールの一行
                $line = fgets($this->client);
                //一行をセットする
                $mail_array[] = $line;
                //メールの大きさを数える
                $count += strlen($line);
            }

            //@var array メールの本文を取得する
            $mail = $this->parse_mail($mail_array);
            
        }catch(Exception $e){
            //なんらかのエラーがあれば、メールを空にする
            $mail = [];
        }
        return $mail;
    }

    //メールを解析する
    //@param array $mail_array 取得したメールの配列
    //@return array 出力用のメール配列
    private function parse_mail(array $mail_array){

        //@var array ヘッダーとボディーの部分を分ける
        [$header_array, $body_array] = $this->divide_header_body($mail_array);
        //@var array ヘッダーを科目ごとにわけた連想配列
        $header_array = $this->parse_header($header_array);
        
        //@var array 本文
        $body_array = $this->parse_body($body_array, $header_array);
        
        //@var array 出力用のメール配列
        $mail = $this->create_show_mail($header_array, $body_array);
        return $mail;
    }

    //出力用のメールを作成
    //@param array $header_array 解析したヘッダー
    //@param array $body_array 解析した本文
    //@return array 出力用のメール
    private function create_show_mail(array $header_array,array $body_array){
        //@var array 出力用のメール
        $mail = [];
        
        //時間を格納
        $mail[] = 'date:'.$header_array['date'].'<br />';
        //宛先を格納
        $mail[] = 'to:'.$header_array['toaddress'].'<br />';
        //送信元を格納
        $mail[] = 'from:'.$header_array['fromaddress'].'<br />';
        //科目を格納
        $mail[] = 'subject:'.mb_decode_mimeheader($header_array['subject']).'<br />';
        $mail[] = '<br />';
        
        //メールの本文を格納
        foreach($body_array as $line){
            $mail[] = $line.'<br />';
        }
        
        return $mail;
    }

    //ヘッダーを解析する
    //@var array $header_array  ヘッダーの配列
    //@return array 項目ごとに分けたヘッダー配列
    private function parse_header(array $header_array){
        
        //@var array 項目ごとに分けるヘッダー配列
        $headers = array();
        //@var string 改行、タブにまたがる項目をキーとして保存する
        $prev_header = null;

        //ヘッダーを項目ごとに分ける
        for($i = 0; $i < count($header_array); $i++){
            //@var string ヘッダーの一行
            $line = $header_array[$i];

            //空行があるなら抜ける
            if($line == ""){
                break;
            }
            
            //先頭がタブの場合は、前行からの文字を連結する
            if (substr($line, 0, 1) === "\t") {
                //タブ文字を削除する
                $line = substr($line, 1);
                //先頭と最後の空白などを削除する
                $line = trim(rtrim($line));
                
                //$prev_headerが存在するなら、行を格納する
                if ($prev_header !== null) {
                    $headers[$prev_header][] = $line;
                }
            }elseif (substr($line, 0, 1) === " ") {
                //先頭が空の場合は、前行からの文字を連結する
                //先頭の空文字を削除する
                $line = substr($line, 1);
                //先頭と最後の空白などを削除する
                $line = trim(rtrim($line));

                //$prev_headerが存在するなら
                if ($prev_header !== null) {
                    //キー($prev_header)のバリューが存在しないなら
                    if (!isset($headers[$prev_header])) {
                        $headers[$prev_header] = "";
                    }
                    //配列ならバリューを格納
                    if (is_array($headers[$prev_header])) {
                        $headers[$prev_header][] = $line;
                    }else{
                        //配列でないなら、連結する
                        $headers[$prev_header] .= $line;
                    }
                }
            }else{
                //先頭が空白、タブでないなら、新しい項目の行と思われる
                //@var int 行中の:の位置
                if (($pos = strpos($line, ":")) > 0) {
                    //@var string 項目の名前、小文字にして、先頭と最後の空白などを削除する
                    $key = trim(rtrim(strtolower(substr($line, 0, $pos))));
                    //-を_に変換する
                    $key = str_replace("-", "_", $key);

                    //@var string 項目の値、先頭と最後の空白などを削除する
                    $value = trim(rtrim(substr($line, $pos + 1)));
                    //$headersにキーが存在するなら、配列に追加
                    if (isset($headers[$key])) {
                        $headers[$key][]  = $value;
                    }else{
                        //配列に格納する
                        $headers[$key] = [$value];
                    }
                    //前行のキーを保存する
                    $prev_header = $key;
                }
            }
        }
        
        //項目ごとに分けたヘッダーを整理する
        foreach($headers as $key => $values) {
            //@var string キーに保存するバリュー
            $value = null;
            switch($key){
                case 'from':
                case 'to':
                case 'cc':
                case 'bcc':
                case 'reply_to':
                case 'sender':
                    //キーの最後にaddressを付けて、バリューの配列を,で連結する
                    $headers[$key."address"] = implode(", ", $values);
                    break;
                case 'subject':
                    //科目は、空白で連結する
                    $value = implode(" ", $values);
                    break;
                default:
                    if (is_array($values)) {
                        //$valuesの中の空文字を削除する
                        foreach($values as $k => $v) {
                            if ($v == "") {
                                unset($values[$k]);
                            }
                        }
                        //@var int $valuesの数を数える
                        $available_values = count($values);
                        
                        //$valuesの個数が1の場合は、$valueに格納する
                        if ($available_values === 1) {
                            $value = array_pop($values);
                        } elseif ($available_values === 2) {
                            //個数が2の場合は、空白で連結する
                            $value = implode(" ", $values);
                        } elseif ($available_values > 2) {
                            //個数が2より大きい場合は、配列事態を格納する
                            $value = array_values($values);
                        } else {
                            //それ以外は、空文字にする
                            $value = "";
                        }
                    }
                    break;
            }
            //キー、バリューを再格納する
            $headers[$key] = $value;
        }
        //content-typeの解析
        $headers = $this->parse_content_type($headers);
        //receivedの整理
        if(isset($headers['received'])){
            $headers['received'] = implode(' ', $headers['received']);
        }

        return $headers;
    }

    //ボディーを解析する
    //@param array $body_array ボディーの配列
    //@param array $header_array 解析したヘッダーの配列
    //@return array 出力用のボディー(本文)の配列
    private function parse_body($body_array, $header_array){
        //@var array 出力用の配列
        $mail_body = [];

        //content-typeにmultipartの文字が存在するなら
        if(preg_match('/multipart/', $header_array['content_type'])){
            //@var array ボディーのバウンダリー文字で分けた配列
            $part_array = $this->get_multipart_part($body_array, $header_array);

            //パートを解析する
            foreach($part_array as $part){
            
                //@var array パートのヘッダーとボディー
                [$header, $body] = $this->divide_header_body($part);
                //ヘッダーの解析
                $header = $this->parse_header($header);
            
                //@var array パートの解析したボディーの配列
                $body = $this->parse_part($body, $header);
                //return用の変数に格納する
                $mail_body[] = $body;
            }
        }else{
            //multipartがなければ、そのまま格納する
            $mail_body = $body_array;
        }
        return $mail_body;
    }

    //パートを解析する
    //@param array $body パートのボディー
    //@param array $header パートのヘッダー
    //@return mix 解析したパートのボディー
    private function parse_part($body, $header){
        
        //ヘッダーのcontent-typeがmultipart/alternativeに一致すれば
        if(preg_match('/multipart\/alternative/', $header['content_type'])){
            //@var array パートの配列。alternativeの場合は、パートの中に二つのパートがあるので。
            $part_array = $this->get_multipart_part($body, $header);
            foreach($part_array as $part){
                //新しくヘッダーとボディーを代入する
                [$header, $body] = $this->divide_header_body($part);
                //ヘッダーを解析する
                $header = $this->parse_header($header);
                
                //content-typeがtext/plainなら抜ける
                if($header['content_type'] == 'text/plain'){
                    break;
                }
            }

            //ボディーが配列なら、空白で連結する
            $body = (is_array($body)) ? implode(' ', $body) : $body;
            if(is_array($body)){
                //@var array デコードした文字を格納する配列
                $temp_body = [];
                foreach($body as $line){
                    //base64エンコーディングされているなら
                    if($header['content_transfer_encoding'] == 'base64'){
                        //@var string base64デコードした文字
                        $base64_decode_body = base64_decode($line, true);
                        //エンコーディングされた文字をUTF-8にデコードする
                        $temp_body[] = mb_convert_encoding($base64_decode_body, "UTF-8", $header['charset']);
                    }
                }
                //ボディーに再格納する
                $body = [$temp_body];
            }else{
                //$bodyが文字なら
                //base64デコードとUTF-8にデコード
                if($header['content_transfer_encoding'] == 'base64'){
                    $base64_decode_body = base64_decode($body, true);
                    $body = mb_convert_encoding($base64_decode_body, "UTF-8", $header['charset']);
                }
            }
        }else if(preg_match('/image/', $header['content_type'])){
            //content-typeが画像形式なら、連結する
            $body = implode('', $body);
            //base64形式で画像を表示する
            $body = '<img src="data:image/png;base64,' .$body. '">';
        }else if(preg_match('/text\/plain/', $header['content_type'])){
            //text/plainなら、文字を連結する
            $body = implode('', $body);
        }
        
        return $body;
    }

    //パートの部分を取得する
    //@param array $body_array ボディーの配列
    //@param array $header_array ヘッダーの配列
    //@return array パートの配列
    private function get_multipart_part($body_array, $header_array){
        //@var string パートを分けるバウンダリー文字
        $boundary_string = str_replace('"', '', $header_array['boundary']);
        //@var array 複数あるパートの配列
        $part_array = [];
        //@var boolean バウンダリーの範囲にあるか、判断する
        $is_boundary = false;
        //@var array パート内の文字の配列
        $part = [];
        foreach($body_array as $line){
            //@var string 改行を空文字にした行
            $line = str_replace("\r\n", '', $line);
            
            //$is_boundaryがfalseでかつ、バウンダリー文字と一致したら、パート内と判断する
            if(substr($line, 2) == $boundary_string && $is_boundary == false){
                $is_boundary = true;
                continue;
            }
            //$is_boundaryがtrueでかつ、バウンダリーの終わり文字と一致するか、新しいバウンダリーの範囲と一致したら、パートを格納する
            if((substr($line, 0, -2) == "--".$boundary_string || substr($line, 2) == $boundary_string) && $is_boundary == true){
                $part_array[] = $part;
                $part = [];
            }
            //バウンダリー内であれば、パートに一行を格納する
            if($is_boundary == true){
                if(substr($line, 2) != $boundary_string){
                    $part[] = $line;
                }
            }
        }
        return $part_array;
    }

    //content-typeを解析する
    //@var array $header_array
    //@return array content-typeを整理したヘッダー
    private function parse_content_type($header_array){
        //@var mix content-type
        $content_type = $header_array['content_type'];
        //content_typeが配列なら、空白で連結する。配列以外ならそのまま代入
        $content_type = (is_array($content_type)) ? implode(' ', $content_type) : $content_type;
        
        //@var int 最初に現れる;の位置
        $pos = strpos($content_type, ';');
        //@var string ;までの文字がcontent-typeのバリュー
        $value = substr($content_type, 0, $pos);
        //content_typeに;までの文字を代入する
        $header_array['content_type'] = $value;
        //@var string ;以降の文字を取得する
        $values = substr($content_type, $pos + 1);
        if($values != ''){
            //@var array $valuesを;で分ける
            $values = explode(';', $values);
            foreach($values as $line){
                //@var int =が現れる最初の位置
                if (($pos = strpos($line, "=")) > 0) {
                    //@var string 項目のキー、小文字にして、先頭と最後の空白などを削除する
                    $key = trim(rtrim(strtolower(substr($line, 0, $pos))));
                    $key = str_replace("-", "_", $key);

                    //@var string 項目のバリュー、先頭と最後の空白などを削除する
                    $value = trim(rtrim(substr($line, $pos + 1)));
                    //キー、バリューを格納する
                    $header_array[$key] = $value;
                }
            }
        }
        return $header_array;
    }

    //ヘッダーとボディーを分ける
    //@param array $mail_array
    //@return array ヘッダーとボディーの配列
    private function divide_header_body($mail_array){
        //@var array ヘッダーの配列
        $header_array = [];
        //@var array ボディーの配列
        $body_array = [];
        //@var boplean ボディーの行か、判断する
        $is_body = false;
        foreach($mail_array as $line){
            //$is_bodyがfalseかつ、$lineが改行または、空文字の場合に次の行からボディーの部分と判断する
            if(($line == "\r\n" || $line == "") && $is_body == false){
                $is_body = true;
                continue;
            }
            //ボディーの行を格納する
            if($is_body == true){
                $body_array[] = $line;
            }
            //ボディーでないから、ヘッダーの配列に格納する
            if($is_body == false){
                $header_array[] = $line;
            }
        }
        return [$header_array, $body_array];
    }

    //POPClientを作成する
    //@param string $name メールアカウント名
    //@param string $password パスワード
    //@param int recieving_server 受信サーバー名
    //@param string $recieving_port_number 受信サーバーのポート番号
    //@return POPClient
    public static function createPOPClient(string $name,string $password,string $recieving_server,string $recieving_port_number){
        //@var POPClient POPClientの作成
        $client = new static($name, $password, $recieving_server, $recieving_port_number);
        //認証接続が失敗すれば、nullを返す
        if($client->connect() == false){
            return null;
        }
        return $client;
    }
}

?>