<?php

    namespace App\Librarys\php;

    use Illuminate\Support\Facades\DB;

    /**
     * 階層構造機能クラス
     */ 
    class Hierarchical{

        /**
         * プロパティ
         * var array $name_list 上位階層の名前リスト
         * var array $array $subordinates_list 責任者リスト
         */
        private array $name_list;
        private array $subordinates_list;

        /**
         * コンストラクタ
         * @param array $this->subordinates_list 責任者リスト
         */
        public function __construct(){
            $this->subordinates_list = [];
        }

        /**
         * 上位階層を取得するメソッド
         * @param array $array 下位階層データ
         * @param string $code 機能コード
         * @param array $name_list 上位階層のリスト 
         * @param array $name_data 上位階層のデータ
         * 
         * @return array $name_list
         */
        public function upperHierarchyName($array){
            $name_list = [];
            foreach($array as $value){
            //頭2文字を判定
            $code = substr($value->high_id,0,2);
            
                if ($code == "bs"){
                    $name_data = DB::select('select * from dcbs01 where client_id = ? 
                    and department_id = ?',[$value->client_id,$value->high_id]);
                }elseif($code == "ji"){
                    $name_data = DB::select('select * from dcji01 where client_id = ? 
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
         * @param Illuminate\Database\QueryException $e エラー内容
         * 
         * @return array $this->subordinates_list 配下データをまとめた配列
         */
        public function subordinateSearch($lists,$client){
            //直下の配下IDのリセット
            $subordinates_id_lists = [];

            //直下の配下データを取得
            foreach($lists as $list){
            
                try{
                    $subordinates = DB::select('select lower_id from dccmks where client_id = ? 
                    and high_id = ?',[$client,$list]);
                }catch(\Exception $e){
                    OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                    return redirect()->route('index');
                }

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

        /**
         * 配下を取得するメソッド
         * @param array $select_lists 配下ID
         * @param string $client 顧客ID
         * 
         * @var array $department_data 部署データ
         * @var array $personnel_data 人員データ 
         * @var array $subordinates_id_lists 直下の配下IDを格納した配列
         * @var array $subordinates 直下の配下データを格納した配列
         * @var Illuminate\Database\QueryException $e エラー内容
         * 
         * @return array list 配下データをまとめた配列
         */
        public function subordinateGet($select_lists,$client){
            $department_data = [];
            $personnel_data = [];
            foreach($select_lists as $select_list){
                //機能コードの判定
                $code = substr($select_list,0,2);
 
                //対応したデータの取得
                if ($code == "bs"){
                    try{
                        $data = DB::select('select * from dcbs01 inner join dccmks on dcbs01.department_id = dccmks.lower_id where dcbs01.client_id = ?
                        and dcbs01.department_id = ?',[$client,$select_list]);
                    }catch(\Exception $e){

                        OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                        DatabaseException::common($e);
                        return redirect()->route('index');
                    }
                    array_push($department_data,$data[0]);
 
                }elseif($code == "ji"){
                    try{
                        $data = DB::select('select * from dcji01 inner join dccmks on dcji01.personnel_id = dccmks.lower_id where dcji01.client_id = ?
                        and dcji01.personnel_id = ?',[$client,$select_list]);
                    }catch(\Exception $e){

                        OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                        DatabaseException::common($e);
                        return redirect()->route('index');
                    }
                    array_push($personnel_data,$data[0]);
                }else{

                }
                
            }
            $lists = [$department_data,$personnel_data];

            return $lists;
        }
    }