<?php

namespace App\Http\Controllers;

use App\Librarys\php\TreeData;
use Illuminate\Support\Facades\View;

class PtcmtrController extends Controller
{
    static function set_view_treedata(){
        $tree_data = TreeData::generate_tree_data();
        View::share('tree_chain', $tree_data[0]);
        View::share('projection_chain', $tree_data[1]);
    }
}
