<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\php\Service\NetworkClient;
use App\Http\Requests\CreateNetworkRequest;
use App\Http\Requests\SendTestMailRequest;

//ネットワーク設定のコントローラークラス
class Psnw01Controller extends Controller
{
    //ネットワーク設定の画面
    //@return View
    public function index(Request $request)
    {
        //ツリーデータの設定
        PtcmtrController::set_view_treedata();

        if($request->old() == []){
            //@var arrat デフォルトのデータ
            $default_client = NetworkClient::get_default_client_data();
            if(session('device') != 'mobile'){
                return view('psnw01/psnw01')->with('client', $default_client);
            }else{
                return view('psnw01/psnw02')->with('client', $default_client);
            }
        }else{
            if(session('device') != 'mobile'){
                return view('psnw01/psnw01')->with('client', $request->old());
            }else{
                return view('psnw01/psnw02')->with('client', $request->old());
            }
        }
    }

    //ネットワーク設定のデータをデータベースに保存する
    //@param CreateNetworkRequest $request
    //@return View
    public function create(CreateNetworkRequest $request)
    {
        //データベースに保存する
        NetworkClient::create_network_client($request);
        
        return redirect()->route('psnw01.index')->withInput($request->all());
    }

    //SMTP方式でメールを送信する
    //@param Request $request
    public function send(SendTestMailRequest $request){
        
        //メールを送る
        NetworkClient::send_test_mail(session('sending_server', ''), session('sending_port_number', ''), $request->test_email);
        
        return redirect()->route('psnw01.index')->withInput($request->all());
    }

    //POP,IMAP方式でメールを受信する
    //@param Request $request
    public function receive(Request $request){
        
        //@var array 受信したメール
        $mail = NetworkClient::get_latest_mail(
            session('name', ''), session('password', ''), session('recieving_server', ''), session('recieving_port_number', ''), session('recieving_server_way')
        );
        
        return redirect()->route('psnw01.index')->withInput(array_merge($request->all(), array('mail' => $mail)));
    }
}
