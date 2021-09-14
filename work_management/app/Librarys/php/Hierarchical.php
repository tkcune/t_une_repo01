<?php

    namespace App\Librarys\php;

    use Illuminate\Support\Facades\DB;

    //階層構造機能クラス
    class Hierarchical{
    private array $name_list;
    private array $subordinates_list;


    public function __construct()
    {
        $this->subordinates_list = [];
    }
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

    /**
     * 配下IDを取得するメソッド
     * @param array $lists 選択したIDを格納した配列
     * @param string $client 顧客ID
     * @param array $subordinates_id_lists 直下の配下IDを格納した配列
     * @param array $subordinates 直下の配下データを格納した配列
     * @return array $this->subordinates_list 配下データをまとめた配列
     */
    public function subordinateSearch($lists,$client){
        //直下の配下IDのリセット
        $subordinates_id_lists = [];

        //直下の配下データを取得
        foreach($lists as $list){
            
            $subordinates = DB::select('select lower_id from dccmks where client_id = ? 
            and high_id = ?',[$client,$list]);

            //配下データを格納
            array_push($this->subordinates_list,$list);

            //直下の配下データが存在する場合、配下IDを格納
            if(!empty($subordinates)){
                for($i=0;$i<count($subordinates);$i++){
                array_push($subordinates_id_lists,$subordinates[$i]->lower_id);
                }
            }
        }
            //直下の配下データが存在する場合、再帰する
            if(!empty($subordinates_id_lists)){
            $this->subordinateSearch($subordinates_id_lists,$client);
            }

        return $this->subordinates_list;
    }
}