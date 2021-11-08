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
       
      
        dd($name_data);

        // return view('pslg01.pslg01', $name_data);
        return view('pslg01.pslg01', ['name_data' => $name_data]);
    }

    public function create(Request $request)
    {
        // ツリーのデーターを宣言する
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        $items = DB::table('dclg01')
        ->join('dcji01', 'dclg01.user', '=', 'dcji01.email')
        ->select('dclg01.*','dcji01.name','dcji01.personnel_id')
        ->where('dclg01.user','=','yamada@itirou.com')                                  
        ->get();
       
        $count = count($items);

        // dd($count);

        return view('pslg01.pslg01', [
            'items' => $items,
            'count' => $count
        ]);
    }
}
