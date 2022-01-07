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
    public function index()
    {
        //ツリーデータの設定
        PtcmtrController::set_view_treedata();

        return view('psnw01/psnw01');
    }

    public function create(CreateNetworkRequest $request)
    {
        NetworkClient::create_network_client($request);

        //ツリーデータの設定
        PtcmtrController::set_view_treedata();
        
        return view('psnw01/psnw01');
    }

    public function send(Request $request){
        NetworkClient::send_test_mail($request->sending_server, $request->sending_port_number, $request->email, $request->name);
        // ツリーデータの取得
        $tree = PtcmtrController::set_view_treedata();
        
        return view('psnw01/psnw01');
    }

    public function receive(Request $request){
        
        $mail = NetworkClient::get_latest_mail(
            $request->name, $request->password, $request->recieving_server, $request->recieving_port_number, $request->recieving_server_way
        );

        //ツリーデータの設定
        PtcmtrController::set_view_treedata();
        
        return view('psnw01/psnw01')->with('mail', $mail);
    }
}
