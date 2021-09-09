<?php

    namespace App\Librarys\php;

    use Illuminate\Support\Facades\DB;

    //階層構造機能クラス
    class Hierarchical{
    private array $name_list;

    /**
     * 上位階層の名前を取得するメソッド
     * @param array $array
     * @param string $code //機能コード
     * @return array $list
     */
    public function upperHierarchyName($array){

        $name_list = [];

        foreach($array as $value){

        //頭2文字を判定
        $code = substr($value->high_id,0,2);

            if ($code == "bs"){
                $name_data = DB::select('select name from dcbs01 where client_id = ? 
                and department_id = ?',[$value->client_id,$value->high_id]);
            }elseif($code == "ji"){
                $name_data = DB::select('select name from dcji01 where client_id = ? 
                and personnel_id = ?',[$value->client_id,$value->high_id]);
            }else{

            }
        array_push($name_list,$name_data[0]);
        }
        return $name_list;
    }
}