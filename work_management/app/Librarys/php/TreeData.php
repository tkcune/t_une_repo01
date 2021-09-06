<?php

namespace App\Librarys\php;
use Illuminate\Support\Facades\DB;
use Traversable;

//ツリーのデータの作成
class TreeData{
    
    //公開するメソッド
    public static function generate_tree_data(){

        //上下関係のオブジェクトのデータを代入する
        $tree_chain = self::create_hierarchy(self::create_chain());
        //投影データを代入する
        $projection_chain = self::create_projection();
        
        //返す
        return [$tree_chain, $projection_chain];
    }


    //@param PDO $dbh データベースのドライバー
    //@param array $database_chain データベースの上下関係のオブジェクトのデータ(idだけ)
    //@return array 上下関係のオブジェクトのデータの配列
    //上下関係のオブジェクトのデータを作成する
    private static function create_hierarchy($database_chain){
        //@var array 返すデータの配列
        $tree_chain = [];
        //@var array データベースのテーブル名
        $database_array = ['dcji01', 'dcbs01', 'notitle'];
        //@var array ツリーの第一階層のidとタイトル
        $database_index_chain = ['ji.人事', 'bs.部署', '0.notitle'];
        //@var array データベースのidを取得するコラム名(notitleのidは書かない)
        $database_colmns_id = ['personnel_id', 'department_id'];
    
        //一つ一つのテーブルから上下関係のオブジェクトのデータを作成する
        //@var int $index ループのインデックス
        foreach($database_array as $index => $database_name){
            //@var array ひとつのテーブルの上下関係のオブジェクトのデータの配列
            $chain = [];
            //@var array キーがデータベースのidでバリューがデータベースの名称
            $database_id_name = [];
            //第一階層のデータを代入する
            $chain[] = array('1.chaintree' => $database_index_chain[$index]);
            //notitle以外はデータベースのデータを取得する
            if($database_name != 'notitle'){
                //@var array データベースのデータ
                $query_result = DB::select('select * from '.$database_name);
                //@var array stdClassキーとバリューに変換する
                $stmt = TreeData::change_key_value($query_result);

                foreach($stmt as $row){
                    //@var string データベースのidのカラム名
                    $colmns_id_name = $database_colmns_id[$index];
                    //@var string データベースのid
                    $key_name = $row[$colmns_id_name];
                    //$key_nameをインデックスとして名称を代入する
                    $database_id_name[$key_name] = $row['name'];
                }
            }

            //ひとつのテーブルの上下関係のオブジェクトのデータを代入していく
            if($database_index_chain[$index] == 'mt.マイツリー'){
                foreach($database_id_name as $key => $value){
                    //マイツリーの場合
                    //'mt.マイツリー'をキーの名前。データベースのidと名前をバリューとする
                    $chain[] = array($database_index_chain[$index] => $key.'.'.$value);
                }
            }else if($database_index_chain[$index] == '0.notitle'){
                //notitleの場合
                //'0.notitle'をキーの名前。指定の文字列をバリューとする
                $chain[] = array($database_index_chain[$index] => 'ur.ユーザ情報');
                $chain[] = array($database_index_chain[$index] => 'lg.ログアウト');
            }else{
                //マイツリーでもnotitleでもない場合
                //第一階層の下のデータを探して代入していく
                foreach($database_id_name as $key =>$value){
                    //@var string データベースのid
                    $search = $key;
                    //@var boolean下位の方にデータがないか判断するフラグ
                    $top_flag = true;
                    //上下関係のオブジェクトのデータ(idだけ)をループする
                    foreach($database_chain as $search_row){
                        //下位に、データが存在すれば、フラグをfalseにする
                        if($search == $search_row['lower_id']){
                            $top_flag = false;
                        }
                    }
                    //下位に、データが存在しない上位にしかデータが存在しない場合、
                    if($top_flag){
                        //配列にidと名称を結合して代入する
                        $chain[] = array($database_index_chain[$index] => $search.'.'.$database_id_name[$search]);
                    }
                }
                //第一階層と第一階層の下以外の上下関係のオブジェクトのデータを作成する
                foreach($database_chain as $row){
                    foreach($database_id_name as $key =>$value){
                        //上位のidが等しいデータを代入していく。
                        if($key == $row['high_id'] && $row['lower_id'] != ''){
                            //@var string キーの名前
                            $object_key = $row['high_id'].'.'.$database_id_name[$row['high_id']];
                            //@var string バリュー
                            $object_value = $row['lower_id'].'.'.$database_id_name[$row['lower_id']];
                            //キー：バリューを代入する
                            $chain[] = array($object_key => $object_value);
                        }
                    }
                }
            }
            //返り値の変数にひとつのテーブルの上下関係のオブジェクトのデータを代入する
            $tree_chain[] = $chain;
        }
        //返す
        return $tree_chain;
    }

    //@param PDO $dbh データベースのドライバー
    //@return array 投影データの配列
    //投影データの作成
    private static function create_projection(){
        //@var array 返り値の投影データの配列
        $projection_chain = [];
        //@var array テーブルの投影データ
        $query_result = DB::select('select * from dccmta');
        //@var array stdClassキーとバリューに変換する
        $stmt = TreeData::change_key_value($query_result);
        foreach($stmt as $row){
            //@var string キーの名前
            $object_key = $row['projection_id'].'.'.$row['projection_id'];
            //@var string バリュー
            $object_value = $row['projection_source_id'].'.'.$row['projection_source_id'];
            //キー：バリューを代入する
            $projection_chain[] = array($object_key => $object_value);
        }
        return $projection_chain;
    }

    //@param PDO $dbh データベースのドライバー
    //@return array 上下関係のオブジェクトのデータ(idだけ)
    //上下関係のオブジェクトのデータのidの配列を返す
    private static function create_chain(){
        
        //@var array 上下関係のオブジェクトのデータのid
        $query_result = DB::select('select * from dccmks');
        //@var array 返り値の配列
        $database_chain = TreeData::change_key_value($query_result);
        //返す
        return $database_chain;
    }

    //@param array $query_result DB::selectで取得した配列
    //@return array 連想配列にした配列
    //stdClassを連想配列に変換する
    private static function change_key_value($query_result){
        //@var array $key_value_array 結果を返す配列
        $key_value_array = [];
        //引数をループする
        foreach($query_result as $row){
            //@var array $line キーとバリューを代入する
            $line = [];
            //キーとバリューに変換する
            foreach($row as $key => $value){
                $line[$key] = $value;
            }
            //結果に代入する
            $key_value_array[] = $line;
        }
        //返す
        return $key_value_array;
    }
}
?>