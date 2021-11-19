<?php

    namespace App\Libraries\php;

    use Illuminate\Support\Facades\DB;
    use App\Models\Date;
    use App\Libraries\php\DatabaseException;
    use App\Libraries\php\OutputLog;

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
                        //データの取得ミスの際の分岐
                        if(empty($name_data)){
                            throw new \Exception("データの取得に失敗しました");
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
                    
                    $data = DB::select('select 
                    dcbs01.client_id, department_id,responsible_person_id,name,status,management_personnel_id,operation_start_date,operation_end_date,lower_id, high_id, dcbs01.created_at, dcbs01.updated_at
                    from dcbs01 inner join dccmks on dcbs01.department_id = dccmks.lower_id where dcbs01.client_id = ?
                    and dcbs01.department_id = ?',[$client,$select_list]);
  
                    if(empty($data)){
                        throw new \Exception("データの取得に失敗しました");
                    }
                    array_push($department_data,$data[0]);
 
                }elseif($code == "ji"){
                    $data = DB::select('select * from dcji01 inner join dccmks on dcji01.personnel_id = dccmks.lower_id where dcji01.client_id = ?
                    and dcji01.personnel_id = ?',[$client,$select_list]);
                    if(empty($data)){
                        throw new \Exception("データの取得に失敗しました");
                    }
                    array_push($personnel_data,$data[0]);
                }else{

                }
                
            }
            $lists = [$department_data,$personnel_data];

            return $lists;
        }


        /**
         * 配下を複製するメソッド
         * @param string $copy_id 配下ID
         * @param string $client_id 顧客ID
         * @param string $high コピー先の上位ID
         * @param string $number 複製直前の最新の部署ID
         * @param string $number2 複製直前の最新の人員ID
         * 
         * @var array  $copy_department 複製するデータ
         * @var array  $lists　配下リスト 
         * @var array  $id 存在する部署内での最新の部署ID
         * @var string $department_id 登録する部署ID
         * @var App\Libraries\php\ZeroPadding $padding
         * @var App\Models\Date $date
         * @var string $code コード
         */
        public function subordinateCopy($copy_id,$client_id,$high,$number,$number2){

            //複製するデータの取得
            try{
                $copy_department = DB::select('select * from dcbs01 where client_id = ? 
                and department_id = ?',[$client_id,$copy_id]);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('index');
            }
            //複製開始前の状態で、複製するデータに直下配下があるかどうかの確認
            try{
                $lists = DB::select('select * from dccmks where client_id = ? and substring(lower_id, 1, 2) = "bs" and substring(lower_id, 3, 10) <= ? and high_id = ? 
                or client_id = ? and substring(lower_id, 1, 2) = "ji" and substring(lower_id, 3, 10) <= ? and high_id = ?',[$client_id,$number,$copy_id,$client_id,$number2,$copy_id]);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('index');
            }

            //顧客IDに対応した最新の部署IDを取得
            try{
                $id = DB::select('select department_id from dcbs01 where client_id = ? 
                order by department_id desc limit 1',[$client_id]);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('index');
            }

            //登録する番号を作成
            $padding = new ZeroPadding();
            $department_id = $padding->padding($id[0]->department_id);

            //データベースに部署情報を登録
            try{
                DB::insert('insert into dcbs01
                (client_id,
                department_id,
                responsible_person_id,
                name,
                status,
                management_personnel_id,
                operation_start_date,
                operation_end_date)
                VALUE (?,?,?,?,?,?,?,?)',
                [$client_id,
                $department_id,
                $copy_department[0]->responsible_person_id,
                $copy_department[0]->name,
                $copy_department[0]->status,
                $copy_department[0]->management_personnel_id,
                $copy_department[0]->operation_start_date,
                $copy_department[0]->operation_end_date]);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('index');
            }

            //データベースに階層情報を登録
            try{
                DB::insert('insert into dccmks
                (client_id,lower_id,high_id)
                VALUE (?,?,?)',
                [$client_id,$department_id,$high]);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('index');
            }

            if(isset($lists)){
                foreach($lists as $list){
                    $code = substr($list->lower_id,0,2);
                    //人員配下がある場合複写
                    if($code == "ji"){

                        $date = new Date();

                        try{
                            $copy_personnel = DB::select('select * from dcji01 where client_id = ? 
                            and personnel_id = ?',[$client_id,$list->lower_id]);
                        }catch(\Exception $e){
                            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                            DatabaseException::common($e);
                            return redirect()->route('index');
                        }
            
                        //顧客IDに対応した最新の人員IDを取得
                        try{
                            $id = DB::select('select personnel_id from dcji01 where client_id = ? 
                            order by personnel_id desc limit 1',[$client_id]);
                        }catch(\Exception $e){
                            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                            DatabaseException::common($e);
                            return redirect()->route('index');
                        }
            
                        //登録する番号を作成
                        $padding = new ZeroPadding();
                        $personnel_id = $padding->padding($id[0]->personnel_id);
            
                        //データベースに登録
                        try{
                            DB::insert('insert into dcji01
                            (client_id,
                            personnel_id,
                            name,
                            email,
                            password,
                            password_update_day,
                            status,
                            management_personnel_id,
                            login_authority,
                            system_management,
                            operation_start_date,
                            operation_end_date)
                            VALUE (?,?,?,?,?,?,?,?,?,?,?,?)',
                            [$client_id,
                            $personnel_id,
                            $copy_personnel[0]->name,
                            $copy_personnel[0]->email,
                            $copy_personnel[0]->password,
                            $date->today(),
                            $copy_personnel[0]->status,
                            $personnel_id,
                            $copy_personnel[0]->login_authority,
                            $copy_personnel[0]->system_management,
                            $copy_personnel[0]->operation_start_date,
                            $copy_personnel[0]->operation_end_date]);
                        }catch(\Exception $e){
                            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                            DatabaseException::common($e);
                            return redirect()->route('index');
                        }
                        //データベースに階層情報を登録
                        try{
                            DB::insert('insert into dccmks
                            (client_id,lower_id,high_id)
                            VALUE (?,?,?)',
                            [$client_id,$personnel_id,$department_id]);
                        }catch(\Exception $e){
                            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                            DatabaseException::common($e);
                            return redirect()->route('index');
                        }
                    }elseif($code == "bs"){
                        $copy_id = $list->lower_id;
                        $high = $department_id;
                        $this->subordinateCopy($copy_id,$client_id,$high,$number,$number2);
                    }
            }
        }
    }
}