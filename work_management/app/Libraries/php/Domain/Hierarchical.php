<?php

    namespace App\Libraries\php\Domain;

    use Illuminate\Support\Facades\DB;
    use App\Facades\OutputLog;
    use App\Models\Date;
    use App\Libraries\php\Service\ZeroPadding;
    use App\Libraries\php\Service\DatabaseException;
    use App\Libraries\php\Domain\ProjectionDataBase;
    use Illuminate\Support\Arr;
use PhpParser\Node\Stmt\TryCatch;

    /**
     * 階層構造に関係する機能クラス
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
         * 上位階層を新規登録するメソッド
         * @param string $client_id 顧客ID
         * @param string $lower_id 下位ID
         * @param string $high 上位ID
         * 
         */
        public function insert($client_id,$lower_id,$high){

            DB::insert('insert into dccmks
            (client_id,lower_id,high_id)
            VALUE (?,?,?)',
            [$client_id,$lower_id,$high]);

        }

        /**
         * 階層情報を更新するメソッド
         * @param string $client_id 顧客ID
         * @param string $department_id 部署ID
         * @param string $high 上位ID
         * 
         */
        public function update($high_id,$client_id,$lower_id){

            DB::update('update dccmks set high_id = ? where client_id = ? and lower_id = ?',
            [$high_id,$client_id,$lower_id]);

        }

        /**
         * 階層情報を削除するメソッド
         * @param string $client_id 顧客ID
         * @param string $lower_id 下位ID
         * 
         */
        public function delete($client_id,$lower_id){

            DB::delete('delete from dccmks where client_id = ? 
            and lower_id = ?',[$client_id,$lower_id]);

        }

        /**
         * 上位階層を取得するメソッド
         * @param array $array 下位階層データ
         * 
         * @var array $name_list 上位階層のリスト 
         * @var string $code 機能コード
         * @var array $name_data 上位階層のデータ
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
         * 
         * @var array $subordinates_id_lists 直下の配下IDを格納した配列
         * @var array $subordinates 直下の配下データを格納した配列
         * @var Illuminate\Database\QueryException $e エラー内容
         * 
         * @return array $this->subordinates_list 配下データをまとめた配列
         */
        public function subordinateSearch($lists,$client){
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
                foreach($subordinates as $subordinate){
                    array_push($this->subordinates_list,$subordinate->lower_id);
                }
            }
            return $this->subordinates_list;
        }

        /**
         * 配下IDを取得するメソッド
         * @param array $lists 選択したIDを格納した配列
         * @param string $client 顧客ID
         * @param array $delete_id 配下データをまとめた配列
         * 
         * @var array $lowers 下位IDを格納した配列
         * @var array $subordinates 直下の配下データを格納した配列
         * @var Illuminate\Database\QueryException $e エラー内容
         * 
         * @return  array $delete_id 
         */
        public function subordinateSearchRoop($lists,$client,$delete_id){

            $lowers = [];
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
                if(isset($subordinates)){
                    foreach($subordinates as $subordinate){
                        array_push($delete_id,$subordinate->lower_id);
                        array_push($lowers,$subordinate->lower_id);
                    }
                }
            }

            $lists = $lowers;
            
            if(!empty($lists)){
                return $this->subordinateSearchRoop($lists,$client,$delete_id);
            }
            return $delete_id;
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
                }elseif($code == "ta"){
                    $projection_db = new ProjectionDataBase();
                    $projection_data = $projection_db->getId($select_list);
                    if(substr($projection_data[0]->projection_source_id,0,2) == "bs"){

                        $data = DB::select('select 
                        dcbs01.client_id, department_id,responsible_person_id,name,status,management_personnel_id,operation_start_date,operation_end_date,lower_id, high_id, dcbs01.created_at, dcbs01.updated_at
                        from dcbs01 inner join dccmks on dcbs01.department_id = dccmks.lower_id where dcbs01.client_id = ?
                        and dcbs01.department_id = ?',[$client,$projection_data[0]->projection_source_id]);

                        array_push($department_data,$data[0]);
                    }elseif(substr($projection_data[0]->projection_source_id,0,2 == "ji")){
                        $data = DB::select('select * from dcji01 inner join dccmks on dcji01.personnel_id = dccmks.lower_id where dcji01.client_id = ?
                        and dcji01.personnel_id = ?',[$client,$projection_data[0]->projection_source_id]);

                        array_push($personnel_data,$data[0]);
                    }
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
         * @var App\Libraries\php\Service\ZeroPadding $padding
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
                operation_end_date,
                remarks)
                VALUE (?,?,?,?,?,?,?,?,?)',
                [$client_id,
                $department_id,
                $copy_department[0]->responsible_person_id,
                $copy_department[0]->name,
                $copy_department[0]->status,
                $copy_department[0]->management_personnel_id,
                $copy_department[0]->operation_start_date,
                $copy_department[0]->operation_end_date,
                $copy_department[0]->remarks]);
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

        public static function copy($client_id, $copy_id, $high_id){
            try{
                DB::beginTransaction();
                $code_number = substr($copy_id, 0, 2);
                
                if($code_number == "ji"){
                    //複製前の最新の人員番号を取得
                    try{
                        $personnel_db = new PersonnelDataBase();
                        $personnel_number = $personnel_db->getId($client_id)[0]->personnel_id;
                    }catch(\Exception $e){
                        OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                        DatabaseException::common($e);
                        return redirect()->route('index');
                    }
                    DB::statement('CREATE TEMPORARY TABLE tmp_dcji01 LIKE dcji01;');
                    DB::statement('INSERT INTO tmp_dcji01 SELECT * FROM dcji01 
                    WHERE client_id = ? and personnel_id = ?',[$client_id, $copy_id]);
                    //登録する番号を作成
                    $padding = new ZeroPadding();
                    $personnel_number = $padding->padding($personnel_number);
                    $personnel_id = $personnel_number;

                    DB::statement('UPDATE tmp_dcji01 
                    set personnel_id = ?   
                    where personnel_id = ?',
                    [$personnel_id, $copy_id]);

                    DB::insert('insert into dccmks
                    (client_id,lower_id,high_id)
                    VALUE (?,?,?)',
                    [$client_id, $personnel_id, $high_id]);
                    DB::statement('INSERT INTO dcji01 SELECT * FROM tmp_dcji01;');
                }else if($code_number == "bs"){
                    //複製前の最新の人員番号を取得
                    try{
                        $personnel_db = new PersonnelDataBase();
                        $personnel_number = $personnel_db->getId($client_id)[0]->personnel_id;
                    }catch(\Exception $e){
                        OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                        DatabaseException::common($e);
                        return redirect()->route('index');
                    }
                    //複製前の最新の部署番号を取得
                    try{
                        $department_db = new DepartmentDataBase();
                        $department_number = $department_db->getId($client_id)[0]->department_id;
                    }catch(\Exception $e){
                        OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                        DatabaseException::common($e);
                        return redirect()->route('index');
                    }
                    static::subordinateBsCopy($client_id, $copy_id, $high_id, $department_number, $personnel_number);
                }else if($code_number == "sb"){
                    $db_name = "dbsb01";
                    $id_name = "space_id";
                    //複製前の最新の作業場所番号を取得
                    try{
                        $space_db = new WorkSpaceDataBase();
                        $space_id = $space_db->getId($client_id)[0]->space_id;
                    } catch (\Exception $e) {
                        OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
                        DatabaseException::common($e);
                        return redirect()->route('pssb01.index');
                    }
                    static::subCopy($client_id, $copy_id, $high_id, $space_id, $db_name, $id_name);
                }else if($code_number == "kb"){
                    $db_name = "dbkb01";
                    $id_name = "board_id";
                    //複製前の最新の掲示板番号を取得
                    try{
                        $board_db = new BoardDataBase();
                        $board_id = $board_db->getId($client_id)[0]->board_id;
                    }catch(\Exception $e){
                        OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                        DatabaseException::common($e);
                        return redirect()->route('pskb01.index');
                    }
                    static::subCopy($client_id, $copy_id, $high_id, $board_id, $db_name, $id_name);
                }

                DB::commit();
            }catch(\Exception $e){
                //ロールバック
                DB::rollBack();
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return static::redirectIndex(substr($copy_id, 0, 2));
            }
        }

        public static function subCopy($client_id, $copy_id, $high_id, $number, $db_name, $id_name){
            $id_num = substr($number,3);
            $id_number = str_pad($id_num, 8, '0', STR_PAD_LEFT);
            $lists = [];
            $lists[] = static::hierarchy_instance($high_id, $copy_id);

            DB::statement('CREATE TEMPORARY TABLE tmp_'.$db_name.' LIKE '.$db_name.';');
            
            DB::statement('INSERT INTO tmp_'.$db_name.' SELECT * FROM '.$db_name.' 
            WHERE client_id = ? and '.$id_name.' = ?',[$client_id, $copy_id]);
            //登録する番号を作成
            $number = ZeroPadding::padding($number);
            $increment_id = $number;

            DB::statement('UPDATE tmp_'.$db_name.' 
            set '.$id_name.' = ?   
            where '.$id_name.' = ?',
            [$increment_id, $copy_id]);

            DB::insert('insert into dccmks
            (client_id,lower_id,high_id)
            VALUE (?,?,?)',
            [$client_id, $increment_id, $lists[0]->high_id]);
            $lists[0]->high_id = $increment_id;
            while($lists){
                $list = array_shift($lists);
                $copy_id = $list->lower_id;
                //複製開始前の状態で、複製するデータに直下配下があるかどうかの確認
                try{
                    $dccmks_lists = static::hierarchy_select($client_id, $copy_id, $id_number, substr($copy_id, 0, 2));
                    $dccmks_copy = [];
                    foreach($dccmks_lists as $bs_list){
                        $dccmks_copy[] = clone $bs_list;
                    }
                    $dccmks_count = count($dccmks_lists);
                }catch(\Exception $e){
                    OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                    DatabaseException::common($e);
                    return static::redirectIndex(substr($copy_id, 0, 2));
                }

                if($dccmks_count > 0){
                    //複製するデータの取得
                    static::temporary_insert($client_id, $dccmks_lists, $dccmks_count, $db_name, $id_name);
                    $insert_dccmks = [];
                    for($i = 0; $i < $dccmks_count; $i++){
                        //登録する番号を作成
                        $padding = new ZeroPadding();
                        $number = $padding->padding($number);
                        $increment_id = $number;
                        $dccmks_lists[$i]->high_id = $copy_id;
                        $dccmks_lists[$i]->lower_id = $increment_id;
                        $dccmks_copy[$i]->high_id = $increment_id;
                        $insert_dccmks[] = $client_id;
                        $insert_dccmks[] = $increment_id;
                        $insert_dccmks[] = $list->high_id;
                    }
                    //データベースに部署情報を登録
                    static::temporary_update($dccmks_lists, $dccmks_copy, $dccmks_count, $db_name, $id_name);
                        
                    //階層情報を挿入する
                    static::hierarchy_insert($insert_dccmks, $dccmks_count);

                    $lists = array_merge($lists, $dccmks_copy);
                }
            }
            DB::statement('INSERT INTO '.$db_name.' SELECT * FROM tmp_'.$db_name.';');
            return 0;
        }

        public static function subordinateBsCopy($client_id, $copy_id, $high, $number, $number2){
            $id_num = substr($number, 3);
            $id2_num = substr($number2, 3);
            $bs_number = str_pad($id_num, 8, '0', STR_PAD_LEFT);
            $ji_number = str_pad($id2_num, 8, '0', STR_PAD_LEFT);
            $lists = [];
            $lists[] = static::hierarchy_instance($high, $copy_id);
            DB::statement('CREATE TEMPORARY TABLE tmp_dcbs01 LIKE dcbs01;');
            DB::statement('CREATE TEMPORARY TABLE tmp_dcji01 LIKE dcji01;');
            //
            DB::statement('INSERT INTO tmp_dcbs01 SELECT * FROM dcbs01 
            WHERE client_id = ? and department_id = ?',[$client_id, $copy_id]);
            //登録する番号を作成
            $number = ZeroPadding::padding($number);
            $department_id = $number;

            DB::statement('UPDATE tmp_dcbs01 
            set department_id = ?   
            where department_id = ?',
            [$department_id, $copy_id]);

            DB::insert('insert into dccmks
            (client_id,lower_id,high_id)
            VALUE (?,?,?)',
            [$client_id, $department_id, $lists[0]->high_id]);
            $lists[0]->high_id = $department_id;
            
            while($lists){
                $list = array_shift($lists);
                $copy_id = $list->lower_id;
                //複製開始前の状態で、複製するデータに直下配下があるかどうかの確認
                try{
                    $bs_dccmks_lists = static::hierarchy_select($client_id, $copy_id, $bs_number, 'bs');
                    $bs_dccmks_copy = [];
                    foreach($bs_dccmks_lists as $bs_list){
                        $bs_dccmks_copy[] = clone $bs_list;
                    }
                    $bs_dccmks_count = count($bs_dccmks_lists);
                    
                    $ji_dccmks_lists = static::hierarchy_select($client_id, $copy_id, $ji_number, 'ji');
                    $ji_dccmks_copy = [];
                    foreach($ji_dccmks_lists as $ji_list){
                        $ji_dccmks_copy[] = clone $ji_list;
                    }
                    $ji_dccmks_count = count($ji_dccmks_lists);
                }catch(\Exception $e){
                    OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                    DatabaseException::common($e);
                    return redirect()->route('index');
                }

                if($bs_dccmks_count > 0){
                    //複製するデータの取得
                    static::temporary_insert($client_id, $bs_dccmks_lists, $bs_dccmks_count, 'dcbs01', 'department_id');
                    //データベースに部署情報を登録
                    try{
                        $insert_dccmks = [];
                        for($i = 0; $i < $bs_dccmks_count; $i++){
                            //登録する番号を作成
                            $number = ZeroPadding::padding($number);
                            $department_id = $number;
                            $bs_dccmks_lists[$i]->high_id = $copy_id;
                            $bs_dccmks_lists[$i]->lower_id = $department_id;
                            $bs_dccmks_copy[$i]->high_id = $department_id;
                            $insert_dccmks[] = $client_id;
                            $insert_dccmks[] = $department_id;
                            $insert_dccmks[] = $list->high_id;
                        }
                    
                        static::temporary_update($bs_dccmks_lists, $bs_dccmks_copy, $bs_dccmks_count, 'dcbs01', 'department_id');

                        //階層情報を挿入する
                        static::hierarchy_insert($insert_dccmks, $bs_dccmks_count);
                    }catch(\Exception $e){
                        OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                        DatabaseException::common($e);
                        return redirect()->route('index');
                    }

                    $lists = array_merge($lists, $bs_dccmks_copy);
                }
                
                if($ji_dccmks_count > 0){

                    $date = new Date();

                    static::temporary_insert($client_id, $ji_dccmks_lists, $ji_dccmks_count, 'dcji01', 'personnel_id');
            
                    //データベースに登録
                    try{
                        $insert_dccmks = [];
                        for($i = 0; $i < $ji_dccmks_count; $i++){
                            //登録する番号を作成
                            $number2 = ZeroPadding::padding($number2);
                            $personnel_id = $number2;
                            $ji_dccmks_lists[$i]->high_id = $copy_id;
                            $ji_dccmks_lists[$i]->lower_id = $personnel_id;
                            $insert_dccmks[] = $client_id;
                            $insert_dccmks[] = $personnel_id;
                            $insert_dccmks[] = $list->high_id;
                        }
                        
                        DB::statement('UPDATE tmp_dcji01 
                        set personnel_id = ELT(
                            FIELD(personnel_id,?'.str_repeat(',?', $ji_dccmks_count - 1).')
                            ,?'.str_repeat(',?', $ji_dccmks_count - 1).'
                        ),
                        password_update_day = ?, 
                        management_personnel_id = personnel_id 
                        where personnel_id in (?'.str_repeat(',?', $ji_dccmks_count - 1).')',
                            array_merge(
                                Arr::pluck($ji_dccmks_copy, 'lower_id'),
                                array_merge(
                                    Arr::pluck($ji_dccmks_lists, 'lower_id'),
                                    array_merge(
                                        [$date->today()],
                                        Arr::pluck($ji_dccmks_copy, 'lower_id')
                                    )
                                )
                            )
                        );
                        static::hierarchy_insert($insert_dccmks, $ji_dccmks_count);
                    }catch(\Exception $e){
                        OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                        DatabaseException::common($e);
                        return redirect()->route('index');
                    }
                }
            }
            DB::statement('INSERT INTO dcbs01 SELECT * FROM tmp_dcbs01;');
            DB::statement('INSERT INTO dcji01 SELECT * FROM tmp_dcji01;');
            return 0;
        }

        public static function temporary_insert($client_id, $dccmks_lists, $dccmks_count, $db_name, $id_name){
            //複製するデータの取得
            try{
                $start = microtime(true);
                DB::statement('INSERT INTO tmp_'.$db_name.' SELECT * FROM '.$db_name.' 
                WHERE client_id = ? and '.$id_name.' in (?'.str_repeat(',?', $dccmks_count - 1).')',
                array_merge([$client_id], Arr::pluck($dccmks_lists, 'lower_id')));
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return static::redirectIndex(substr($dccmks_lists[0]->lower_id, 0, 2));
            }
        }

        public static function temporary_update($dccmks_lists, $dccmks_copy, $dccmks_count, $db_name, $id_name){
            try {
                DB::statement('UPDATE tmp_'.$db_name.' 
                            set '.$id_name.' = ELT(
                                FIELD('.$id_name.', ?'.str_repeat(',?', $dccmks_count - 1).')
                                ,?'.str_repeat(',?', $dccmks_count - 1).'
                            )
                            where '.$id_name.' in (?'.str_repeat(',?', $dccmks_count - 1).')',
                            array_merge(
                                Arr::pluck($dccmks_copy, 'lower_id'),
                                array_merge(
                                    Arr::pluck($dccmks_lists, 'lower_id'),
                                    Arr::pluck($dccmks_copy, 'lower_id')
                                )
                            )
                        );
            } catch (\Exception $e) {
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return static::redirectIndex(substr($dccmks_lists[0]->lower_id, 0, 2));
            }
        }

        public static function hierarchy_instance($high_id, $copy_id){
            return ((
                new class {
                    public $high_id;
                    public $lower_id;
                    public function set($high_id, $lower_id){
                        $this->high_id = $high_id;
                        $this->lower_id = $lower_id;
                        return $this;
                    }
                }
            )->set($high_id, $copy_id));
        }
        public static function hierarchy_insert($insert_dccmks, $dccmks_count){
            try{
                //階層情報を挿入する
                DB::insert('insert into dccmks
                (client_id,lower_id,high_id)
                VALUE (?,?,?)'.str_repeat(',(?,?,?)', $dccmks_count - 1),
            $insert_dccmks);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return static::redirectIndex(substr($insert_dccmks[0][1], 0 ,2));
            }
        }

        public static function hierarchy_select($client_id, $copy_id, $number, $code){
            return DB::select('select client_id, lower_id, high_id from dccmks where client_id = ? and substring(lower_id, 1, 2) = "'.$code.'" and substring(lower_id, 3, 10) <= ? and high_id = ?',
            [$client_id, $number, $copy_id]);
        }

        public static function redirectIndex($code_number){
            if($code_number == "bs" || $code_number == "ji"){
                return redirect()->route('index');
            }else if($code_number == "sb"){
                return redirect()->route('pssb01.index');
            }else if($code_number == "kb"){
                return redirect()->route('pskb01.index');
            }
        }
    }