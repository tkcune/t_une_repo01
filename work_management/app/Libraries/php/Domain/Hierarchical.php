<?php

    namespace App\Libraries\php\Domain;

    use Illuminate\Support\Facades\DB;
    use App\Facades\OutputLog;
    use App\Models\Date;
    use App\Libraries\php\Service\ZeroPadding;
    use App\Libraries\php\Service\DatabaseException;
    use App\Libraries\php\Domain\ProjectionDataBase;
use Exception;
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

        //複写機能,subordinateCopyより機能改善
        //@param string $client_id クライアントid
        //@param string $copy_id コピーする対象のid
        //@param string $high_id コピー先の親のid
        public static function copy($client_id, $copy_id, $high_id){
            try{
                //トランザクション処理を入れる
                DB::beginTransaction();
                //@var string 機能コード,bs,ji,sbなど
                $code_number = substr($copy_id, 0, 2);
                
                //人事の複写
                if($code_number == "ji"){
                    //複製前の最新の人員番号を取得
                    try{
                        //@var PersonnelDataBase 人事データベースクラス
                        $personnel_db = new PersonnelDataBase();
                        //@var string 最新の人事番号
                        $personnel_number = $personnel_db->getId($client_id)[0]->personnel_id;
                    }catch(\Exception $e){
                        OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                        DatabaseException::common($e);
                        return redirect()->route('index');
                    }
                    //テンポラリーテーブルの作成
                    DB::statement('CREATE TEMPORARY TABLE tmp_dcji01 LIKE dcji01;');
                    //テンポラリーテーブルに，複写対象のデータを挿入する
                    DB::statement('INSERT INTO tmp_dcji01 SELECT * FROM dcji01 
                    WHERE client_id = ? and personnel_id = ?',[$client_id, $copy_id]);
                    
                    //@var ZeroPadding ゼロパディングクラス,idのコードを整える
                    $padding = new ZeroPadding();
                    //@var string 新しい複写するid
                    $personnel_number = $padding->padding($personnel_number);
                    //@var string 保存ようのid
                    $personnel_id = $personnel_number;

                    //複写する対象のデータを新しいidに更新する
                    DB::statement('UPDATE tmp_dcji01 
                    set personnel_id = ?   
                    where personnel_id = ?',
                    [$personnel_id, $copy_id]);

                    //階層データの挿入，親のidはコピー先の親のid
                    DB::insert('insert into dccmks
                    (client_id,lower_id,high_id)
                    VALUE (?,?,?)',
                    [$client_id, $personnel_id, $high_id]);

                    //idを更新した新しいデータを本体のデータベースに挿入し直す。
                    DB::statement('INSERT INTO dcji01 SELECT * FROM tmp_dcji01;');
                }else if($code_number == "bs"){
                    //部署の場合
                    //複製前の最新の人員番号を取得
                    try{
                        //@var PersonnelDataBase 人事データベースクラス
                        $personnel_db = new PersonnelDataBase();
                        //@var string 最新の人事のidの番号です
                        $personnel_number = $personnel_db->getId($client_id)[0]->personnel_id;
                    }catch(\Exception $e){
                        OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                        DatabaseException::common($e);
                        return redirect()->route('index');
                    }
                    //複製前の最新の部署番号を取得
                    try{
                        //@var DepartmentDataBase 部署データベースクラス
                        $department_db = new DepartmentDataBase();
                        //@var string 最新の部署のidの番号です 
                        $department_number = $department_db->getId($client_id)[0]->department_id;
                    }catch(\Exception $e){
                        OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                        DatabaseException::common($e);
                        return redirect()->route('index');
                    }
                    //部署の複写
                    static::subordinateBsCopy($client_id, $copy_id, $high_id, $department_number, $personnel_number);
                }else if($code_number == "sb"){
                    //@var string 作業場所のテーブル名
                    $db_name = "dcsb01";
                    //@var string 作業場所のidのカラム名
                    $id_name = "space_id";
                    try{
                        //@var WorkSpaceDataBase 作業場所のデータベースクラス
                        $space_db = new WorkSpaceDataBase();
                        //@var string 作業場所の最新のid
                        $space_id = $space_db->getId($client_id)[0]->space_id;
                    } catch (\Exception $e) {
                        OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
                        DatabaseException::common($e);
                        return redirect()->route('pssb01.index');
                    }
                    //作業場所の複写
                    static::subCopy($client_id, $copy_id, $high_id, $space_id, $db_name, $id_name);
                }else if($code_number == "kb"){
                    //掲示板の複写
                    //@var string 掲示板のテーブル名
                    $db_name = "dckb01";
                    //@var string 掲示板のidのカラム名
                    $id_name = "board_id";
                    try{
                        //@var BoardDataBase 掲示板のデータベースクラス
                        $board_db = new BoardDataBase();
                        //@var string 掲示板の最新のid
                        $board_id = $board_db->getId($client_id)[0]->board_id;
                    }catch(\Exception $e){
                        OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                        DatabaseException::common($e);
                        return redirect()->route('pskb01.index');
                    }
                    //掲示板の複写
                    static::subCopy($client_id, $copy_id, $high_id, $board_id, $db_name, $id_name);
                }
                //トランザクション処理をコミットする
                DB::commit();
            }catch(\Exception $e){
                //ロールバック
                DB::rollBack();
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return static::redirectIndex(substr($copy_id, 0, 2));
            }
        }

        //複写の共通コード(人事，部署を除く)
        //@param string $client_id クライアントのid
        //@param string $copy_id コピー対象のid
        //@param string $high_id コピー先の親のid
        //@param string $number コピー対象のテーブルの最新のid
        //@param string $db_name コピー対象のテーブル名
        //@param string $id_name コピー対象のカラム名
        public static function subCopy($client_id, $copy_id, $high_id, $number, $db_name, $id_name){
            //@var string idから機能コードを除いた数字の部分
            $id_num = substr($number,3);
            //@var string idの数字の部分
            $id_number = str_pad($id_num, 8, '0', STR_PAD_LEFT);
            //@var array コピー対象を管理する配列
            $lists = [];
            //@var Object コピー対象のデータクラス
            $lists[] = static::hierarchy_instance($high_id, $copy_id);

            //テンポラリーテーブルの作成
            DB::statement('CREATE TEMPORARY TABLE tmp_'.$db_name.' LIKE '.$db_name.';');
            //コピー対象のデータをテンポラリーテーブルに挿入する
            DB::statement('INSERT INTO tmp_'.$db_name.' SELECT * FROM '.$db_name.' 
            WHERE client_id = ? and '.$id_name.' = ?',[$client_id, $copy_id]);
            //登録する番号を作成
            $number = ZeroPadding::padding($number);
            //@var string インクリメントしたid
            $increment_id = $number;

            //複写されるidを新しいidに更新する
            DB::statement('UPDATE tmp_'.$db_name.' 
            set '.$id_name.' = ?   
            where '.$id_name.' = ?',
            [$increment_id, $copy_id]);

            //階層テーブルにデータを挿入する
            DB::insert('insert into dccmks
            (client_id,lower_id,high_id)
            VALUE (?,?,?)',
            [$client_id, $increment_id, $lists[0]->high_id]);
            //次の親のidは，インクリメントしたid
            $lists[0]->high_id = $increment_id;
            while($lists){
                //@var Object 複写するデータクラス
                $list = array_shift($lists);
                //@var string 複写対象のid
                $copy_id = $list->lower_id;
                try{
                    //@var array 配下のデータを取得する
                    $dccmks_lists = static::hierarchy_select($client_id, $copy_id, $id_number, substr($copy_id, 0, 2));
                    //@var array 配下の保存用のデータ
                    $dccmks_copy = [];
                    foreach($dccmks_lists as $bs_list){
                        //オブジェクトの代入は，参照代入なので，cloneで値代入する
                        $dccmks_copy[] = clone $bs_list;
                    }
                    //@var int 配下のデータを数える
                    $dccmks_count = count($dccmks_lists);
                }catch(\Exception $e){
                    OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                    DatabaseException::common($e);
                    return static::redirectIndex(substr($copy_id, 0, 2));
                }

                //配下のデータがあれば
                if($dccmks_count > 0){
                    //複製するデータをテンポラリーテーブルに挿入する
                    static::temporary_insert($client_id, $dccmks_lists, $dccmks_count, $db_name, $id_name);
                    //@var array 階層テーブルに挿入するデータの配列
                    $insert_dccmks = [];
                    for($i = 0; $i < $dccmks_count; $i++){
                        //登録する番号を作成
                        $padding = new ZeroPadding();
                        $number = $padding->padding($number);
                        //@var string インクリメントしたid
                        $increment_id = $number;
                        //$dccmks_listsに新しいidを代入する
                        $dccmks_lists[$i]->high_id = $copy_id;
                        $dccmks_lists[$i]->lower_id = $increment_id;
                        $dccmks_copy[$i]->high_id = $increment_id;
                        //階層テーブルに代入するデータの作成
                        $insert_dccmks[] = $client_id;
                        $insert_dccmks[] = $increment_id;
                        $insert_dccmks[] = $list->high_id;
                    }
                    //データベースに部署情報を登録
                    static::temporary_update($dccmks_lists, $dccmks_copy, $dccmks_count, $db_name, $id_name);
                        
                    //階層情報を挿入する
                    static::hierarchy_insert($insert_dccmks, $dccmks_count);

                    //配下のデータを結合して，配下の配下データを複写していく
                    $lists = array_merge($lists, $dccmks_copy);
                }
            }
            //テンポラリーテーブルのデータを元のテーブルに挿入する
            DB::statement('INSERT INTO '.$db_name.' SELECT * FROM tmp_'.$db_name.';');
            return 0;
        }

        //部署の複写コード
        //@param string $client_id クライアントのid
        //@param string $copy_id コピー対象のid
        //@param string $high コピー先の親のid
        //@param string $number 部署の最新id
        //@param string $number2 人事の最新id
        public static function subordinateBsCopy($client_id, $copy_id, $high, $number, $number2){
            //@var string 部署の機能コードを除いた数字部分
            $id_num = substr($number, 3);
            //@var string 人事の機能コードを除いた数字部分
            $id2_num = substr($number2, 3);
            //@var string 部署のidの数字部分
            $bs_number = str_pad($id_num, 8, '0', STR_PAD_LEFT);
            //@var string 人事のidの数字部分
            $ji_number = str_pad($id2_num, 8, '0', STR_PAD_LEFT);
            //@var array 複写するデータを管理する
            $lists = [];
            //コピー対象のデータをクラスとして代入する
            $lists[] = static::hierarchy_instance($high, $copy_id);
            //部署のテンポラリーテーブルを作成する
            DB::statement('CREATE TEMPORARY TABLE tmp_dcbs01 LIKE dcbs01;');
            //人事のテンポラリーテーブルを作成する
            DB::statement('CREATE TEMPORARY TABLE tmp_dcji01 LIKE dcji01;');
            //コピー対象の部署をテンポラリーテーブルに挿入する
            DB::statement('INSERT INTO tmp_dcbs01 SELECT * FROM dcbs01 
            WHERE client_id = ? and department_id = ?',[$client_id, $copy_id]);
            //登録する番号を作成
            $number = ZeroPadding::padding($number);
            //@var string インクリメントした部署のid
            $department_id = $number;

            //コピー対象の部署のidを新しい部署のidに更新する
            DB::statement('UPDATE tmp_dcbs01 
            set department_id = ?   
            where department_id = ?',
            [$department_id, $copy_id]);

            //複写した部署のidを階層データに登録する
            DB::insert('insert into dccmks
            (client_id,lower_id,high_id)
            VALUE (?,?,?)',
            [$client_id, $department_id, $lists[0]->high_id]);
            $lists[0]->high_id = $department_id;
            //リストがない，子要素を持つ親がなくなるまで，ループする
            while($lists){
                //@var Object $listsの先頭から部署情報を取り出し，$listsから削除。
                $list = array_shift($lists);
                //@var string コピー対象のidを挿入する
                $copy_id = $list->lower_id;
                try{
                    //@var array 部署の配下の部署データを取得する
                    $bs_dccmks_lists = static::hierarchy_select($client_id, $copy_id, $bs_number, 'bs');
                    //@var array 部署の配下部署データの保存用を作成する
                    $bs_dccmks_copy = [];
                    foreach($bs_dccmks_lists as $bs_list){
                        //オブジェクトは，アドレスの代入，参照代入なので，cloneで値代入
                        $bs_dccmks_copy[] = clone $bs_list;
                    }
                    //@var int 部署の配下部署の数を数える
                    $bs_dccmks_count = count($bs_dccmks_lists);
                    
                    //@var array 部署の配下人事のデータ
                    $ji_dccmks_lists = static::hierarchy_select($client_id, $copy_id, $ji_number, 'ji');
                    //@var array 部署の配下人事のデータ保存用
                    $ji_dccmks_copy = [];
                    foreach($ji_dccmks_lists as $ji_list){
                        //オブジェクトは，アドレスの代入，参照代入なので，cloneで値代入
                        $ji_dccmks_copy[] = clone $ji_list;
                    }
                    //@var int 部署の配下人事データを数える
                    $ji_dccmks_count = count($ji_dccmks_lists);
                }catch(\Exception $e){
                    OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                    DatabaseException::common($e);
                    return redirect()->route('index');
                }

                //部署の配下部署があるなら
                if($bs_dccmks_count > 0){
                    //複製する配下部署をテンポラリーテーブルに挿入する
                    static::temporary_insert($client_id, $bs_dccmks_lists, $bs_dccmks_count, 'dcbs01', 'department_id');
                    //データベースに部署情報を登録
                    try{
                        //@var array 階層テーブルに挿入する配列
                        $insert_dccmks = [];
                        for($i = 0; $i < $bs_dccmks_count; $i++){
                            //登録する番号を作成
                            $number = ZeroPadding::padding($number);
                            //@var string インクリメントした部署id
                            $department_id = $number;
                            //$bs_dccmks_listsに，新しい複写したidを代入する
                            $bs_dccmks_lists[$i]->high_id = $copy_id;
                            $bs_dccmks_lists[$i]->lower_id = $department_id;
                            $bs_dccmks_copy[$i]->high_id = $department_id;
                            //階層テーブルに挿入するデータを作成する
                            $insert_dccmks[] = $client_id;
                            $insert_dccmks[] = $department_id;
                            $insert_dccmks[] = $list->high_id;
                        }
                    
                        //複写する対象のidを更新する
                        static::temporary_update($bs_dccmks_lists, $bs_dccmks_copy, $bs_dccmks_count, 'dcbs01', 'department_id');

                        //階層情報を挿入する
                        static::hierarchy_insert($insert_dccmks, $bs_dccmks_count);
                    }catch(\Exception $e){
                        OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                        DatabaseException::common($e);
                        return redirect()->route('index');
                    }

                    //配下の部署データを連結して，また部署の配下を複写していく
                    $lists = array_merge($lists, $bs_dccmks_copy);
                }
                
                //配下の人事データがあれば
                if($ji_dccmks_count > 0){

                    //@var Date 日付クラス
                    $date = new Date();

                    //複写される人事データをテンポラリーテーブルに挿入する
                    static::temporary_insert($client_id, $ji_dccmks_lists, $ji_dccmks_count, 'dcji01', 'personnel_id');
            
                    //データベースに登録
                    try{
                        //@var array 階層テーブルに挿入する配列
                        $insert_dccmks = [];
                        for($i = 0; $i < $ji_dccmks_count; $i++){
                            //登録する番号を作成
                            $number2 = ZeroPadding::padding($number2);
                            //@var string インクリメントした人事のid
                            $personnel_id = $number2;
                            //新しいidを代入する
                            $ji_dccmks_lists[$i]->high_id = $copy_id;
                            $ji_dccmks_lists[$i]->lower_id = $personnel_id;
                            //階層テーブルのデータを作成する
                            $insert_dccmks[] = $client_id;
                            $insert_dccmks[] = $personnel_id;
                            $insert_dccmks[] = $list->high_id;
                        }
                        
                        //複写される人事の情報を更新する
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
                        //階層テーブルに，複製した新しいデータを挿入する
                        static::hierarchy_insert($insert_dccmks, $ji_dccmks_count);
                    }catch(\Exception $e){
                        OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                        DatabaseException::common($e);
                        return redirect()->route('index');
                    }
                }
            }
            //配下の部署，人事のデータがなく，複写が終われば，テンポラリーテーブルのデータを
            //元のテーブルに代入する。
            DB::statement('INSERT INTO dcbs01 SELECT * FROM tmp_dcbs01;');
            DB::statement('INSERT INTO dcji01 SELECT * FROM tmp_dcji01;');
            return 0;
        }

        //テンポラリーテーブルに大量のデータを一括で挿入する
        //@param string $client_id クライアントのid
        //@param array $dccmks_lists 階層データの配列
        //@param int $dccmks_count 階層データの数
        //@param string $db_name データベースの名前
        //@param string $id_name idのカラム名
        public static function temporary_insert($client_id, $dccmks_lists, $dccmks_count, $db_name, $id_name){
            //複製するデータの取得
            try{
                //テンポラリーテーブルに，複写対象のデータを一括で挿入する
                //str_repeat関数により挿入するプレースホルダーを増やす
                //Arr::pluckメソッドによりlower_idだけを一列で取得する
                DB::statement('INSERT INTO tmp_'.$db_name.' SELECT * FROM '.$db_name.' 
                WHERE client_id = ? and '.$id_name.' in (?'.str_repeat(',?', $dccmks_count - 1).')',
                array_merge([$client_id], Arr::pluck($dccmks_lists, 'lower_id')));
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return static::redirectIndex(substr($dccmks_lists[0]->lower_id, 0, 2));
            }
        }

        //テンポラリーテーブルのidを一括で更新する
        //@param array $dccmks_lists 階層データの配列
        //@param array $dccmks_copy 新しいidが入った階層データの配列
        //@param int $dccmks_count 階層データの数
        //@param string $db_name デーブル名
        //@param string $id_name idのカラム名
        public static function temporary_update($dccmks_lists, $dccmks_copy, $dccmks_count, $db_name, $id_name){
            try {
                //テンポラリーテーブルにあるデータのidを新しいidに更新する
                //ELT,FIELD関数により更新するidを選び，一括更新を実現する
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

        //コードのデータ取得を統一するために，無名のクラスを作成する
        //@param string $high_id コピー先の親のid
        //@param string $copy_id コピー対象のid
        public static function hierarchy_instance($high_id, $copy_id){
            //high_id,lower_idというプロパティをもつクラスを作成する
            return ((
                new class {
                    public $high_id;
                    public $lower_id;
                    //プロパティにデータを代入する
                    //@param string $high_id コピー先の親のid
                    //@param string $copy_id コピー対象のid
                    public function set($high_id, $lower_id){
                        $this->high_id = $high_id;
                        $this->lower_id = $lower_id;
                        return $this;
                    }
                }
            )->set($high_id, $copy_id));
        }

        //階層データを一括で挿入する
        //@param array $insert_dccmks 挿入する階層データの配列
        //@param int $dccmks_count 階層データの数
        public static function hierarchy_insert($insert_dccmks, $dccmks_count){
            try{
                throw new Exception();
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

        //階層データの子のデータを取得する
        //@param string $client_id クライアントのid
        //@param string $copy_id コピー対象のid
        //@param string $number 最新のidの番号部分
        //@param string $code 機能コード,bs,jiなど
        //@return array 階層の子のデータ
        public static function hierarchy_select($client_id, $copy_id, $number, $code){
            return DB::select('select client_id, lower_id, high_id from dccmks where client_id = ? and substring(lower_id, 1, 2) = "'.$code.'" and substring(lower_id, 3, 10) <= ? and high_id = ?',
            [$client_id, $number, $copy_id]);
        }

        //エラーの返り場所を選択する
        //@param string $code_number 機能コード,bs,ji
        public static function redirectIndex($code_number){
            //部署Top画面に戻る
            if($code_number == "bs" || $code_number == "ji"){
                return redirect()->route('index');
            }else if($code_number == "sb"){
                //作業場所Top画面に戻る
                return redirect()->route('pssb01.index');
            }else if($code_number == "kb"){
                //掲示板Top画面に戻る
                return redirect()->route('pskb01.index');
            }
        }
    }