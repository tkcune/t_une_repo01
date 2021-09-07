<?php

namespace App\Librarys\php;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

//ツリーのデータの作成
class TreeData{
    
    //公開するメソッド
    //@return array 上下関係のオブジェクトのデータと投影データ
    public static function generate_tree_data(){

        //@var array 投影データを代入する
        $projection_chain = self::create_projection();
        //@var array データのidと名称の配列
        $database_id_name = self::change_id_name(self::create_db_table());
        //@var array 階層テーブルの配列
        $database_chain = self::create_chain();
        //@var array 投影データのidと名称
        $projection_id_name = self::change_projection_name($projection_chain, $database_id_name);
        //@var array 階層テーブルを分ける
        $tree_id_chain = self::divide_hierarchy($database_chain);
        //@var array ツリー生成用の上下関係のオブジェクトのデータ
        $tree_chain = self::create_hierarchy($tree_id_chain, array_merge($database_id_name, $projection_id_name));
        //返す
        return [$tree_chain, $projection_chain];
    }

    //@param array $database_chain 階層テーブルのデータ
    //@return array 階層テーブルを分けた配列
    //階層テーブルのデータを分ける
    private static function divide_hierarchy($database_chain){
        //@var array 返り値の配列
        $tree_chain = [];
        //@var array データを分ける基準
        $database_index_idname = ['bs'];
        
        foreach($database_index_idname as $idname){
            //@var array 基準で分けたデータを入れる
            $chain_block = [];
            foreach($database_chain as $row){
                //階層テーブルのhigh_idの先頭と基準の文字列が合致していれば、
                if(Str::startsWith($row['high_id'], $idname)){
                    //代入する
                    $chain_block[] = $row;
                }
            }
            //@var boolean 下位のデータが上位のデータにあるか判断する。
            $is_chain = true;
            //上位のデータは取っても、下位のデータが階層テーブルのデータの上位にあれば、
            //そのデータも$chain_blockに入れる
            while($is_chain){
                //falseにして、下位のデータが上位になければ、ループをぬけるようにする
                $is_chain = false;
                //@var array データを探すようの配列はコピーした配列
                $copy_chain_block = $chain_block;
                
                foreach($copy_chain_block as $block_row){
                    foreach($database_chain as $chain_row){
                        //下位のデータが上位にある場合
                        if($block_row['lower_id'] == $chain_row['high_id']){
                            //@var boolean 上位にあるデータが$chain_blockに存在しているか判断する
                            $is_same = false;
                            foreach($chain_block as $chain_block_row){
                                //上位と下位が等しいなら、取得したデータ。新しくデータを代入しない
                                if($chain_block_row['high_id'] == $chain_row['high_id'] and $chain_block_row['lower_id'] == $chain_row['lower_id']){
                                    $is_same = true;
                                }
                            }
                            if(!$is_same){
                                //下位のデータが上位にあり、まだ取得していないなら、データを代入する
                                $chain_block[] = $chain_row;
                                $is_chain = true;
                            }
                        }
                    }
                }
            }
            $tree_chain[] = $chain_block;
        }
        return $tree_chain;
    }


    //@param PDO $dbh データベースのドライバー
    //@param array $database_chain データベースの上下関係のオブジェクトのデータ(idだけ)
    //@return array 上下関係のオブジェクトのデータの配列
    //上下関係のオブジェクトのデータを作成する
    private static function create_hierarchy($tree_id_chain, $database_id_name){
        //@var array 返すデータの配列
        $tree_chain = [];

        //@var array ツリーの第一階層のidとタイトル
        $database_index_chain = ['bs.部署', '0.notitle'];
    
        //一つ一つのテーブルから上下関係のオブジェクトのデータを作成する
        //@var int $index ループのインデックス
        foreach($database_index_chain as $index => $tree_title){
            //@var array ひとつのテーブルの上下関係のオブジェクトのデータの配列
            $chain = [];
            
            //第一階層のデータを代入する
            $chain[] = array('1.chaintree' => $database_index_chain[$index]);

            //ひとつのテーブルの上下関係のオブジェクトのデータを代入していく
            if($database_index_chain[$index] == '0.notitle'){
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
                    foreach($tree_id_chain[$index] as $search_row){
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
                foreach($tree_id_chain[$index] as $row){
                    $chain[] = array($row['high_id'].'.'.$database_id_name[$row['high_id']] => $row['lower_id'].'.'.$database_id_name[$row['lower_id']]);
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
            $object_key = $row['projection_id'];
            //@var string バリュー
            $object_value = $row['projection_source_id'];
            //キー：バリューを代入する
            $projection_chain[] = array($object_key => $object_value);
        }
        return $projection_chain;
    }

    //テーブルを一つに結合して配列にする
    //@return array テーブルのデータを1つにまとめた配列
    private static function create_db_table(){
        //@var array データベースのテーブル名
        $database_array = ['dcbs01', 'dcji01'];
        //@var array テーブルのデータをため込む配列
        $query_stack = [];
        //@var array 返り値の配列
        $db_table = [];
        foreach($database_array as $db_name){
            //@var array データベースからデータを取得する
            $query_result = DB::select('select * from '.$db_name);
            //キーとバリューにして代入する
            $query_stack[] = TreeData::change_key_value($query_result);
        }
        foreach($query_stack as $stack){
            //配列を一つにまとめる
            $db_table = array_merge($db_table, $stack);
        }
        return $db_table;
    }

    //@param PDO $dbh データベースのドライバー
    //@return array 上下関係のオブジェクトのデータ(idだけ)
    //上下関係のオブジェクトのデータのidの配列を返す
    private static function create_chain(){
        
        //@var array 上下関係のオブジェクトのデータのid
        $query_result = DB::select('select * from dccmks');
        $query_key_value = TreeData::change_key_value($query_result);;
        //@var array 返り値の配列,high_idとlower_idがキーの連想配列
        $database_chain = TreeData::change_high_low($query_key_value);
        //返す
        return $database_chain;
    }

    //データベースのデータをidと名称の形式にする
    //@param array データベースのデータを一つにまとめた配列
    private static function change_id_name($db_table){
        //@var array データベースのidのカラム名
        $database_colmns_id = ['department_id', 'personnel_id'];
        //@var array 返り値の配列
        $database_id_name = [];
        foreach($db_table as $row){
            //@var string データベースのidを代入
            $key_name = '';
            foreach($row as $key => $value){
                //データベースのidのカラム名が存在すれば
                if(in_array($key, $database_colmns_id)){
                    //idを代入する
                    $key_name = $value;
                }
            }
            if($key_name != ''){
                //カラム名が存在すれば、idと名称の形式にする
                $database_id_name[$key_name] = $row['name']; 
            }
        }
        return $database_id_name;

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

    //投影データをidと名称にする
    //@param array 投影データ
    //@param array テーブルのidと名前
    //@return array 投影データのidと名称
    private static function change_projection_name($projection_chain, $database_id_name){
        //@var array 返り値の配列
        $projection_id_name = [];
        foreach($projection_chain as $row){
            foreach($row as $key => $value){
                //idがキーで名称がバリューの連想配列
                $projection_id_name[$key] = $database_id_name[$value];
            }
        }
        return $projection_id_name;
    }

    //階層テーブルのデータをhigh_idとlower_idがキーの連想配列にする
    //@param array データベースのキーとバリュー
    //@return array 階層テーブルのデータを連想配列にした配列
    private static function change_high_low($query_key_value){
        //@var array 返り値の配列
        $database_chain = [];
        foreach($query_key_value as $row){
            //high_idとlower_idをキーにして、データを取得する
            $database_chain[] = array(
                'high_id' => $row['high_id'],
                'lower_id' => $row['lower_id']
            );
        }
        return $database_chain;
    }
}
?>