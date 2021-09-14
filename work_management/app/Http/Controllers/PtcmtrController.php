<?php

namespace App\Http\Controllers;

use App\Librarys\php\TreeData;
use Illuminate\Support\Facades\View;
use App\Librarys\php\OutputLog;

class PtcmtrController extends Controller
{
    static function set_view_treedata(){
        $tree_data = TreeData::generate_tree_data();
        View::share('tree_chain', $tree_data[0]);
        View::share('projection_chain', $tree_data[1]);
        // OutputLog::log(__FUNCTION__, 'si', '共通機能', 'ツリーデータを生成');
        // OutputLog::message_log(explode(DIRECTORY_SEPARATOR, __CLASS__)[count(explode(DIRECTORY_SEPARATOR, __CLASS__)) - 1].'/'.__FUNCTION__, 'mhcmok0009');
    }
}
