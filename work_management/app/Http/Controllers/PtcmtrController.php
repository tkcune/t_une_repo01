<?php

namespace App\Http\Controllers;

use App\Librarys\php\TreeData;
use Illuminate\Support\Facades\View;

class PtcmtrController extends Controller
{
    //ツリーのデータをblade側にセットする
    static function set_view_treedata(){
        $tree_data = TreeData::generate_tree_data();
        View::share('tree_chain', $tree_data[0]);
        View::share('projection_chain', $tree_data[1]);
    }

    //ツリーの削除情報をblade側にセットする
    static function delete_node($id){
        View::share('treeaction_chain', array('action' => 'delete', 'id' => $id));
    }

    //ツリーの展開情報をblade側にセットする
    static function open_node($id){
        View::share('treeaction_chain', array('action' => 'open', 'id' => $id));
    }
}
