<?php

namespace App\Http\Controllers;

use App\Librarys\php\TreeData;
use Illuminate\Support\Facades\View;

//ツリーデータをblade側に渡すクラス
class PtcmtrController extends Controller
{
    //ツリーのデータをblade側にセットする
    static function set_view_treedata(){
        //@var array ツリーのデータを生成する
        $tree_data = TreeData::generate_tree_data();
        //ツリー生成データをセットする
        View::share('tree_chain', $tree_data[0]);
        //投影データをセットする
        View::share('projection_chain', $tree_data[1]);
    }

    //ツリーの削除情報をblade側にセットする
    //@param string $id 削除したid
    static function delete_node($id){
        View::share('treeaction_chain', array('action' => 'delete', 'id' => $id));
    }

    //ツリーの展開情報をblade側にセットする
    //@param string $id 追加、表示するid
    static function open_node($id){
        View::share('treeaction_chain', array('action' => 'open', 'id' => $id));
    }
}
