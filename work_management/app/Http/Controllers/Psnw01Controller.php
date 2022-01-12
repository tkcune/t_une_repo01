<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\PtcmtrController;
use App\Libraries\php\NetworkClient;
use App\Http\Requests\CreateNetworkRequest;

//ネットワーク設定のコントローラークラス
class Psnw01Controller extends Controller
{
    //ネットワーク設定の画面
    //@return View
    public function index()
    {
        
        //ツリーデータの設定
        PtcmtrController::set_view_treedata();

        //@var arrat デフォルトのデータ
        $default_client = NetworkClient::get_default_client_data();

        return view('psnw01/psnw01')->with('client', $default_client);
    }

    //ネットワーク設定のデータをデータベースに保存する
    //@param CreateNetworkRequest $request
    //@return View
    public function create(CreateNetworkRequest $request)
    {
        //データベースに保存する
        NetworkClient::create_network_client($request);

        //ツリーデータの設定
        PtcmtrController::set_view_treedata();
        
        return view('psnw01/psnw01')->with('client', $request->all());
    }

    //SMTP方式でメールを送信する
    //@param Request $request
    public function send(Request $request){
        
        //メールを送る
        NetworkClient::send_test_mail($request->sending_server, $request->sending_port_number, $request->test_email);
        // ツリーデータの取得
        $tree = PtcmtrController::set_view_treedata();
        
        return view('psnw01/psnw01')->with('client', $request->all());
    }

    //POP,IMAP方式でメールを受信する
    //@param Request $request
    public function receive(Request $request){
        
        //@var array 受信したメール
        $mail = NetworkClient::get_latest_mail(
            $request->name, $request->password, $request->recieving_server, $request->recieving_port_number, $request->recieving_server_way
        );

        //ツリーデータの設定
        PtcmtrController::set_view_treedata();
        
        return view('psnw01/psnw01')->with('client', $request->all())->with('mail', $mail);
    }
}
