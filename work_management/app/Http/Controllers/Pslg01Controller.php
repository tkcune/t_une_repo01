<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PtcmtrController;

class Pslg01Controller extends Controller
{

    //
    public function index()
    {
        // ツリーのデーターを宣言する
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        $name_data = DB::table('dcji01')->get();

      session()->put('name_data',$name_data);
      $session_names = session()->get('name_data');

    //   dd($session_names);

        return view('pslg01.pslg01', ['session_names' => $session_names]);
    }

    public function create(Request $request)
    {
        // ツリーのデーターを宣言する
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        // $check = $request->input('check');

        $check = $request->all();

      


        // $items = DB::table('dclg01')
        //     ->join('dcji01', 'dclg01.user', '=', 'dcji01.email')
        //     ->select('dclg01.*', 'dcji01.name', 'dcji01.personnel_id')
        //     ->where('dclg01.user', '=', 'yamada@itirou.com')
        //     ->get();


        $items = DB::table('dclg01')
        ->join('dcji01', 'dclg01.user', '=', 'dcji01.email')
        ->select('dclg01.*','dcji01.name','dcji01.personnel_id')
        ->where('dclg01.user','=','yamada@itirou.com')                                  
        ->orwhere('dclg01.check','=','$request->check')                                  
        ->orwhere('dclg01.log','like',"%$request->log%")                                  
        ->get();

        $count = count($items);

        $name_data = DB::table('dcji01')->get();

        $session_names = session()->get('name_data');
        $select_name = session()->get('select_name');


        return view('pslg01.pslg01', [
            'items' => $items,
            'count' => $count,
            'name_data' => $name_data,
            'session_names' => $session_names,
            'select_name' => $select_name
        ]);
    }

public function client(Request $request)
{
        // ツリーのデーターを宣言する
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();
       
        $client_all = DB::table('dcji01')->where('client_id','=',$request->client_id)->get();

        session()->put('client_id',$client_all[0]->personnel_id);
        session()->put('client_name',$client_all[0]->name);
}

    public function select(Request $request)
    {
        // ツリーのデーターを宣言する
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();
       
       
        $select_all = DB::table('dcji01')->where('personnel_id', '=', $request->one_answer)->get();
         

        session()->put('select_id',$select_all[0]->personnel_id);
        session()->put('select_name',$select_all[0]->name);
        $select_id = session()->get('select_id');
        $select_name = session()->get('select_name');
       

        $session_names = session()->get('name_data');
     
    

        return view('pslg01.pslg01',[
            'select_id'=>$select_id,
            'select_name'=>$select_name,
            'session_names' => $session_names
        ]);
    }
}
