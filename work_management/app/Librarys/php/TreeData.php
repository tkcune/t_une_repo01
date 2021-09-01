<?php

namespace App\Librarys\php;
use PDO;

//ツリーのデータの作成
class TreeData{
    
    //公開するメソッド
    public static function generate_tree_data(){
        //@var array 上下関係のオブジェクトのデータの配列
        $tree_chain = [];
        //@var array 投影のデータ
        $projection_chain = [];
        //@var string データベースの接続情報
        $dsn = 'mysql:dbname=sagyokanri;host=localhost';
        //@var string データベースのユーザ名
        $user = 'root';
        //@var string データベースのパスワード
        $password = '';

        //PDOクラスを取得する
        try{
            //@var PDOクラス データベースのドライバー
            $dbh = new PDO($dsn, $user, $password);
        }catch (PDOException $e){
        }
    
        //上下関係のオブジェクトのデータを代入する
        $tree_chain = self::create_hierarchy($dbh, self::create_chain($dbh));
        //投影データを代入する
        $projection_chain = self::create_projection($dbh);
        
        //返す
        return [$tree_chain, $projection_chain];
    }


    //@param PDO $dbh データベースのドライバー
    //@param array $database_chain データベースの上下関係のオブジェクトのデータ(idだけ)
    //@return array 上下関係のオブジェクトのデータの配列
    //上下関係のオブジェクトのデータを作成する
    private static function create_hierarchy($dbh, $database_chain){
        //@var array 返すデータの配列
        $tree_chain = [];
        //@var array データベースのテーブル名
        $database_array = ['dcbs01', 'notitle'];
        //@var array ツリーの第一階層のidとタイトル
        $database_index_chain = ['bs.部署', '0.notitle'];
        //@var array データベースのidを取得するコラム名(notitleのidは書かない)
        $database_colmns_id = ['department_id'];
    
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
                $stmt = $dbh->query("SELECT * FROM ".$database_name);
        
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
    private static function create_projection($dbh){
        //@var array 返り値の投影データの配列
        $projection_chain = [];
        //@var array テーブルの投影データ
        $stmt = $dbh->query("SELECT * FROM dccmta");
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
    private static function create_chain($dbh){
        //@var array 返り値の配列
        $database_chain = [];
        //@var array 上下関係のオブジェクトのデータのid
        $stmt = $dbh->query("SELECT * FROM dccmks");
        foreach($stmt as $row){
            //返り値の変数に代入する
            $database_chain[] = $row;
        }
        //返す
        return $database_chain;
    }
}
?>