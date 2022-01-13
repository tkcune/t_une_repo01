<?php

namespace App\Http\Controllers;

use App\Libraries\php\Domain\TreeData;
use Illuminate\Support\Facades\View;

//ツリーデータをblade側に渡すクラス
class PtcmtrController extends Controller
{
    //ツリーのデータをblade側にセットする
    public static function set_view_treedata(){
        //@var array ツリーのデータを生成する
        $tree_data = TreeData::generate_tree_data();
        //ツリー生成データをセットする
        View::share('tree_chain', $tree_data[0]);
        //投影データをセットする
        View::share('projection_chain', $tree_data[1]);

        //セッション情報のrecent_treeactionがnullの場合は、初期設定
        if(session()->get('recent_treeaction', null) != null){
            //削除または、登録のメソッドを通過したか、判断する
            if(session()->get('recent_treeaction') == 'delete' || session()->get('recent_treeaction') == 'open'){
                //前回のツリーアクション(back_treeaction)をdeleteか、openとする
                session(['back_treeaction' => session()->get('recent_treeaction')]);
                session(['recent_treeaction' => 'show']);
            }else{
                //削除や登録メソッドではない場合、show(表示メソッド)とする
                session(['back_treeaction' => 'show']);
                session(['recent_treeaction' => 'show']);
                //表示メソッドなので、idを消去する
                session()->forget('action_node_id');
            }
        }else{
            //初期設定
            session(['recent_treeaction' => 'show']);
            session()->forget('back_treeaction');
            session()->forget('action_node_id');
        }
    }

    //ツリーの削除情報をblade側にセットする
    //@param string $id 削除したid
    public static function delete_node(string $id){
        //親のidとアクション情報をセッション情報にセットする
        session(['action_node_id'=>$id]);
        session(['recent_treeaction' => 'delete']);
    }

    //ツリーの展開情報をblade側にセットする
    //@param string $id 追加、表示するid
    public static function open_node(string $id){
        //登録先などのidとアクション情報をセッション情報にセットする
        session(['action_node_id' => $id]);
        session(['recent_treeaction' => 'open']);
    }
}
