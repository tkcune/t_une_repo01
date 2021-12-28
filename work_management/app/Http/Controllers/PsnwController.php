<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\PtcmtrController;
use App\Libraries\php\OutputLog;
use App\Libraries\php\Network;
use App\Http\Requests\CreateNetworkRequest;

class PsnwController extends Controller
{
    public function index()
    {
        // ツリーデータの取得
        $tree = PtcmtrController::set_view_treedata();

        return view('psnw01/psnw01');
    }

    public function create(CreateNetworkRequest $request)
    {
        // dd($request);
        Network::create_network_client($request);

        // ツリーデータの取得
        $tree = PtcmtrController::set_view_treedata();
        
        return view('psnw01/psnw01');
    }
}
