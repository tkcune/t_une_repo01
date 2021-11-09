<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Librarys\php\DatabaseException;
use App\Librarys\php\Pagination;
use App\Librarys\php\Hierarchical;
use App\Librarys\php\StatusCheck;
use App\Librarys\php\OutputLog;
use App\Librarys\php\ZeroPadding;
use Illuminate\Support\Facades\Config;
use App\Librarys\php\Message;
use App\Librarys\php\ResponsiblePerson;
use App\Http\Controllers\PtcmtrController;
use App\Models\Date;
use Illuminate\Support\Facades\View;

/**
 * 部署データを操作するコントローラー
 */
class Psbs01Controller extends Controller
{
    /**
     * 部署新規登録画面表示
     * 
     * @var  App\Http\Controllers\PtcmtrController $tree
     * @var  array $tree_data ツリーデータ
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();
        return view('psbs01.psbs01');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * 部署の新規登録
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @var  string $client_id  顧客ID
     * @var  string $responsible_person_id  責任者ID
     * @var  string $name  部署名
     * @var  string $status  状態
     * @var  string $management_personnel_id　管理者ID
     * @var  string $high　上位部署のID番号
     * @var  string $id　顧客IDに対応した最新の部署IDを格納する因数
     * @var  App\Librarys\php\ZeroPadding $padding
     * @var  App\Librarys\php\StatusCheck $check
     * @var  string $operation_start_date　稼働開始日
     * @var  string $operation_end_date　稼働終了日
     * @var  int $department_id 部署ID
     * 
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //リクエストの取得
        $client_id = $request->client_id;
        $responsible_person_id = $request->responsible_person_id;
        $name = $request->name;
        $status = $request->status;
        $management_personnel_id = $request->management_personnel_id;
        $high = $request->high;

        //顧客IDに対応した最新の部署IDを取得
        try{
            $id = DB::select('select department_id from dcbs01 where client_id = ? 
            order by department_id desc limit 1',[$client_id]);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            
            return redirect()->route('index');
        }
        if(empty($id)){
            $department_id = "bs00000001";
        }else{
            //登録する番号を作成
            $padding = new ZeroPadding();
            $department_id = $padding->padding($id[0]->department_id);
        }

        //部署状態を判別して稼働開始日・稼働終了日を決定させる
        $check = new StatusCheck();
        list($operation_start_date,$operation_end_date) = $check->statusCheck($request->status);

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
            $responsible_person_id,
            $name,
            $status,
            $management_personnel_id,
            $operation_start_date,
            $operation_end_date]);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
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
        OutputLog::message_log(__FUNCTION__, 'mhcmok0001');
        //メッセージの表示
        $request->session()->put('message',Config::get('message.mhcmok0001'));
        PtcmtrController::open_node($department_id);
        return redirect()->route('index');
        
    }

    /**
     * 配下部署の表示
     *
     * @param  string $client 顧客ID
     * @param  string $select_id 選択した部署ID
     * 
     * @var  array $lists 選択した部署
     * @var　int $count_department 部署ページネーションのページ数
     * @var　int $count_personnel  人員ページネーションのページ数
     * @var  array $department_data 部署データ
     * @var  array $personnel_data 人員データ
     * @var  string $select_code 選択したIDのコード
     * @var  array $projection_code 投影元のデータコード
     * @var  array $select_lists 選択した部署の配下データ
     * @var  string $code 機能コード
     * @var  array  $data 取得したデータ
     * @var  App\Librarys\php\ResponsiblePerson $responsible
     * @var  array $top_responsible 最上位の責任者データ
     * @var  array $top_management 最上位の管理者データ
     * @var  App\Librarys\php\Pagination $pagination
     * @var  int $department_max 部署データページネーションの最大値
     * @var  array $departments ページネーション後の部署データ
     * @var  int $personnel_max 人員データページネーションの最大値
     * @var  array $names ページネーション後の人員データ
     * @var  array $responsible_lists 責任者リスト
     * @var  App\Librarys\php\Hierarchical $hierarchical
     * @var  array $department_high 部署データの上位階層
     * @var  array $personnel_high 人員データの上位階層
     * @var  App\Http\Controllers\PtcmtrController $tree
     * @var  array $tree_data ツリーデータ
     * 
     * @return \Illuminate\Http\Response
     */
    public function show($client,$select_id)
    {
        if($select_id == "bs00000001" or $select_id == "bs"){
            return redirect()->route('index');
        }
        $count_department = Config::get('startcount.count');
        $count_personnel = Config::get('startcount.count');

        //選択した部署のIDをarray型に格納
        $lists = [];
        $department_data = [];
        $personnel_data = [];
        $select_code = substr($select_id,0,2);
        $click_id = $select_id;
        View::share('click_id', $click_id);

        if($select_code == "ta"){
            //選択部署がtaだった場合は対応するIDを取得
            $projection_code = DB::select('select projection_source_id from dccmta where projection_id = ?', [$select_id]);
            $select_id = $projection_code[0]->projection_source_id;
            $select_code = substr($projection_code[0]->projection_source_id,0,2);
        }
        
        if($select_code == "bs"){
            //選択した部署のデータを取得
            try{
                $click_department_data = DB::select('select 
                dcbs01.client_id,department_id,responsible_person_id,name,status,management_personnel_id,operation_start_date,operation_end_date,lower_id, high_id, dcbs01.created_at, dcbs01.updated_at
                from dcbs01 inner join dccmks on dcbs01.department_id = dccmks.lower_id where dcbs01.client_id = ? and department_id = ?',[$client,$select_id]);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
            }

            array_push($lists,$select_id);

            //選択した部署の配下を取得
            $hierarchical = new Hierarchical();
            $select_lists = $hierarchical->subordinateSearch($lists,$client);
          
            //選択したデータ及び配下データを取得
            $lists = $hierarchical->subordinateGet($select_lists,$client);
            $department_data = $lists[0];
            $personnel_data = $lists[1];

            //責任者を名前で取得
            $responsible = new ResponsiblePerson();
            if($click_department_data){
                $click_responsible_lists = $responsible->getResponsibleLists($client,$click_department_data);
                View::share('click_responsible_lists', $click_responsible_lists);
            }
            //管理者を名前で取得
            if(isset($click_department_data)){
                $click_management_lists = $responsible->getManagementLists($client,$click_department_data);
            }

            //日付を6桁に変換
            $date = new Date();
            $date->formatDate($click_department_data);
       
            //上位階層取得
            $hierarchical = new Hierarchical();
            $click_department_high = $hierarchical->upperHierarchyName($click_department_data);
           
            //一覧表示データの取得

            //日付フォーマットを6桁にする
            $date->formatDate($department_data);
            $date->formatDate($personnel_data);

            //基本ページネーション設定
            $pagination = new Pagination();
            $department_max = $pagination->pageMax($department_data,count($department_data));
            $departments = $pagination->pagination($department_data,count($department_data),$count_department);
            $personnel_max= $pagination->pageMax($personnel_data,count($personnel_data));
            $names = $pagination->pagination($personnel_data,count($personnel_data),$count_personnel);

            //責任者を名前で取得
            $responsible_lists = $responsible->getResponsibleLists($client,$departments);

            //上位階層取得
            $department_high = $hierarchical->upperHierarchyName($departments);
            $personnel_high = $hierarchical->upperHierarchyName($names);

            $tree = new PtcmtrController();
            $tree_data = $tree->set_view_treedata();

            session(['click_code'=>$select_code]);

            return view('pacm01.pacm01',compact('click_department_data','count_department','count_personnel',
            'department_max','departments','personnel_max','names','responsible_lists','department_high','personnel_high',
            'click_department_high','click_management_lists','client','select_id','personnel_data'));
            
        }else{
            //選択した人員のデータを取得
            try{
                $click_personnel_data = DB::select('select 
                dcji01.client_id ,personnel_id,name,email,password,password_update_day,status,management_personnel_id,login_authority,system_management,operation_start_date,operation_end_date,dcji01.created_at, dcji01.updated_at ,high_id ,lower_id
                from dcji01 inner join dccmks on dcji01.personnel_id = dccmks.lower_id where dcji01.client_id = ?
                and dcji01.personnel_id = ?',[$client,$select_id]);
            }catch(\Exception $e){

                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
            }

            //選択した人員の所属部署を取得
            try{
                $affiliation_data = DB::select('select high_id from dccmks where client_id = ?
                and lower_id = ?',[$client,$select_id]);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
            }
            
            //取得した部署IDを元に部署データを取得
            try{
                $data = DB::select('select 
                dcbs01.client_id, department_id,responsible_person_id,name,status,management_personnel_id,operation_start_date,operation_end_date,lower_id, high_id, dcbs01.created_at, dcbs01.updated_at
                from dcbs01 inner join dccmks on dcbs01.department_id = dccmks.lower_id where dcbs01.client_id = ?
                and dcbs01.department_id = ?',[$client,$affiliation_data[0]->high_id]);
            }catch(\Exception $e){

                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
            }

            //部署データが存在しない場合、選択部署が最上位部署か判別
            if(empty($data)){
                $top_department = DB::select('select * from dcbs01 where client_id = ? and department_id = ?',[$client,$affiliation_data[0]->high_id]);

                //全体の部署データの取得
                try{
                    $department_data = DB::select('select 
                    dcbs01.client_id, department_id,responsible_person_id,name,status,management_personnel_id,operation_start_date,operation_end_date,lower_id, high_id, dcbs01.created_at, dcbs01.updated_at
                    from dcbs01 inner join dccmks on dcbs01.department_id = dccmks.lower_id and dcbs01.client_id = ?',[$client]);
                }catch(\Exception $e){
                    OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                    DatabaseException::common($e);
                }

                //全体の人員データの取得
                try{
                    $personnel_data = DB::select('select 
                    dcji01.client_id ,personnel_id,name,email,password,password_update_day,status,management_personnel_id,login_authority,system_management,operation_start_date,operation_end_date,dcji01.created_at, dcji01.updated_at ,high_id ,lower_id
                    from dcji01 inner join dccmks on dcji01.personnel_id = dccmks.lower_id and dcji01.client_id = ?',[$client]);
                }catch(\Exception $e){
                    OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                    DatabaseException::common($e);
                }
                //日付を6桁にする
                $date = new Date();
                $date->formatDate($click_personnel_data);

                //責任者を名前で取得
                $responsible = new ResponsiblePerson();
                $top_responsible = $responsible->getResponsibleLists($client,$top_department);

                //管理者を名前で取得
                $top_management = $responsible->getManagementLists($client,$top_department);
                $click_management_lists = $responsible->getManagementLists($client,$click_personnel_data);
        
                //一覧表示のデータ取得
                $date->formatDate($department_data);
                $date->formatDate($personnel_data);

                //基本ページネーション設定
                $pagination = new Pagination();
                $department_max = $pagination->pageMax($department_data,count($department_data));
                $departments = $pagination->pagination($department_data,count($department_data),$count_department);
                $personnel_max= $pagination->pageMax($personnel_data,count($personnel_data));
                $names = $pagination->pagination($personnel_data,count($personnel_data),$count_personnel);

                //責任者を名前で取得
                $responsible = new ResponsiblePerson();
                $responsible_lists = $responsible->getResponsibleLists($client,$departments);

                //上位階層取得
                $hierarchical = new Hierarchical();
                $department_high = $hierarchical->upperHierarchyName($departments);
                $personnel_high = $hierarchical->upperHierarchyName($names);

                //ツリーデータの取得
                $tree = new PtcmtrController();
                $tree_data = $tree->set_view_treedata();

                //クリックコードの保存
                session(['click_code'=>$select_code]);

                return view('pacm01.pacm01',compact('top_management','click_management_lists','department_max','departments','personnel_max','names',
                'top_department','top_responsible','count_department','count_personnel','client','responsible_lists','department_high','personnel_high',
                'select_id','personnel_data','click_personnel_data'));
            }

            array_push($department_data,$data[0]);

            array_push($lists,$affiliation_data[0]->high_id);

            //取得した部署IDを元に配下を取得
            $hierarchical = new Hierarchical();
            $select_lists = $hierarchical->subordinateSearch($lists,$client);

            //選択したデータ及び配下データを取得
            $lists = $hierarchical->subordinateGet($select_lists,$client);
            $department_data = $lists[0];
            $personnel_data = $lists[1];

            //日付を6桁表記にする
            $date = new Date();
            $date->formatDate($click_personnel_data);
            
            //責任者を名前で取得
            $responsible = new ResponsiblePerson();
            if(isset($click_personnel_data)){
                $click_management_lists = $responsible->getManagementLists($client,$click_personnel_data);
            }

            //日付フォーマットを6桁にする
            $date = new Date();
            $date->formatDate($department_data);
            $date->formatDate($personnel_data);

            //基本ページネーション設定
            $pagination = new Pagination();
            $department_max = $pagination->pageMax($department_data,count($department_data));
            $departments = $pagination->pagination($department_data,count($department_data),$count_department);
            $personnel_max= $pagination->pageMax($personnel_data,count($personnel_data));
            $names = $pagination->pagination($personnel_data,count($personnel_data),$count_personnel);

            //責任者を名前で取得
            $responsible = new ResponsiblePerson();
            $responsible_lists = $responsible->getResponsibleLists($client,$departments);

            //上位階層取得
            $hierarchical = new Hierarchical();
            $department_high = $hierarchical->upperHierarchyName($departments);
            $personnel_high = $hierarchical->upperHierarchyName($names);
           
            $tree = new PtcmtrController();
            $tree_data = $tree->set_view_treedata();

            session(['click_code'=>$select_code]);

            return view('pacm01.pacm01',compact('data','count_department','count_personnel','department_max','departments','personnel_max','names',
            'department_high','personnel_high','responsible_lists','client','select_id','click_personnel_data','click_management_lists','personnel_data'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 部署情報を更新
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @var  string  $client_id　顧客ID
     * @var  string  $department_id　部署ID
     * @var  string  $name　名前
     * @var  string  $management_number 管理者人員番号
     * @var  string  $responsible_person_id 責任者番号
     * @var  array   $management_personnel_id　管理者ID
     * @var  string  $status　状態
     * @var  \App\Librarys\php\StatusCheck $check
     * @var  string  $operation_start_date 稼働開始日
     * @var  string  $operation_end_date 稼働終了日
     * @var  string  $message メッセージ
     * 
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //リクエストの取得
        $client_id = $request->client_id;
        $department_id = $request->department_id;
        $name = $request->name;
        $management_number = $request->management_number;
        $responsible_person_id = $request->responsible_person_id;
        $status = $request->status;
        
        //入力された番号の人員が存在するかの確認
        try{
            $management_personnel_id = DB::select('select * from dcji01 where client_id = ? and personnel_id = ?',[$client_id,$management_number]);
        }catch(\Exception $e){
                //エラー処理
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
        }
        if($management_personnel_id == null){

            return redirect()->route('index');
        }

        //部署情報の更新
        if($status == "13")
        {
            //状態が稼働中なら稼働開始日を更新
            $check = new StatusCheck();
            list($operation_start_date,$operation_end_date) = $check->statusCheck($request->status);
        
            try{
                DB::update('update dcbs01 set responsible_person_id = ?,name = ?,status = ?,management_personnel_id = ?,operation_start_date = ? where client_id = ? and department_id = ?',
                [$responsible_person_id,$name,$status,$management_number,$operation_start_date,$client_id,$department_id]);
            }catch(\Exception $e){
                //エラー処理
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
            }
        }else if($status == "18"){
            //状態が廃止なら稼働終了日を更新
            $check = new StatusCheck();
            list($operation_start_date,$operation_end_date) = $check->statusCheck($request->status);
        
            try{
                DB::update('update dcbs01 set responsible_person_id = ?,name = ?,status = ?,management_personnel_id = ?,operation_end_date = ? where client_id = ? and department_id = ?',
                [$responsible_person_id,$name,$status,$management_number,$operation_end_date,$client_id,$department_id]); 
            }catch(\Exception $e){
                //エラー処理
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('index');
            }
        }else{
            //上記以外なら状態と名前のみ更新
            try{
                DB::update('update dcbs01 set responsible_person_id = ?,name = ?,status = ?,management_personnel_id = ? where client_id = ? and department_id = ?',
                [$responsible_person_id,$name,$status,$management_number,$client_id,$department_id]);
            }catch(\Exception $e){
                //エラー及びログ処理
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('index');
            }
        }
            //ログ処理
            OutputLog::message_log(__FUNCTION__, 'mhcmok0002');
            $message = Message::get_message('mhcmok0002',[0=>'']);
            session(['message'=>$message[0]]);

        return back();
    }

    /** 階層構造を更新
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param string $id 送信されたID
     * 
     * @var string $client_id 顧客ID
     * @var string $high_id 上位ID
     * @var string $lower_id 下位ID
     * @var string $message メッセージ
     * 
     * @return \Illuminate\Http\Response
     */
    public function hierarchyUpdate(Request $request,$id)
    {
        //リクエストの取得
        $client_id = $id;
        $high_id = $request->high_id;
        $lower_id = $request->lower_id;

        //データベース更新
        try{
            DB::update('update dccmks set high_id = ? where client_id = ? and lower_id = ?',
            [$high_id,$client_id,$lower_id]);
        }catch(\Exception $e){
        //エラー及びログ処理
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('index');
        }
        //ログ処理
        OutputLog::message_log(__FUNCTION__, 'mhcmok0002');
        $message = Message::get_message('mhcmok0002',[0=>'']);
        session(['message'=>$message[0]]);
        return redirect()->route('index');
    }

    /**
     * 人員及び部署データの削除
     *
     * @param  string  $client 顧客ID
     * @param  string  $delete 削除予定のID
     * 
     * @var  array   $lists 削除予定のIDを格納した配列
     * @var  \App\Librarys\php\Hierarchical $hierarchical
     * @var  array   $delete_lists 削除予定のIDを格納した配列
     * @var  int     $code 機能コードの頭2文字
     * @var  array $delete_projections 削除元
     * @var  string $message メッセージ
     * 
     * @return \Illuminate\Http\Response
     */
    public function delete($client,$delete)
    {
        //選択した部署のIDをarray型に格納
        $lists = [];
        array_push($lists,$delete);

        //選択した部署の部署情報を取得
        try{
            $delete_data = DB::select('select 
            dcbs01.client_id, department_id,responsible_person_id,name,status,management_personnel_id,operation_start_date,operation_end_date,lower_id, high_id, dcbs01.created_at, dcbs01.updated_at
            from dcbs01 inner join dccmks on dcbs01.department_id = dccmks.lower_id where dcbs01.client_id = ?
            and dcbs01.department_id = ?',[$client,$delete]);
        }catch(\Exception $e){

            OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
            DatabaseException::common($e);
            return redirect()->route('index');
        }

        ////選択した部署が最上位部署かの確認
        if(empty($delete_data)){
            $delete_data = DB::select('select * from dcbs01 where client_id = ? and department_id = ?',[$client,$delete]);
            $delete_data[0]->high_id = "bs";
        }

        //選択した部署の配下を取得
        $hierarchical = new Hierarchical();
        $delete_lists = $hierarchical->subordinateSearch($lists,$client);
         
        //選択したデータ及び配下データを削除
        if(!empty($delete_lists)){
            foreach($delete_lists as $delete_list){
                //機能コードの判定
                $code = substr($delete_list,0,2);

                //対応したデータの削除
                if ($code == "bs"){
                    DB::delete('delete from dcbs01 where client_id = ? and department_id = ?',
                    [$client,$delete_list]);

                    //削除予定の配下部署が元になった投影を削除
                    $delete_projections = DB::select('select projection_id from dccmta where client_id = ? and projection_source_id = ?',
                    [$client,$delete_list]);
                    foreach($delete_projections as $delete_projection){
                        try{
                            DB::delete('delete from dccmks where client_id = ? and lower_id = ?',
                            [$client,$delete_projection->projection_id]);
                        }catch(\Exception $e){
                            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                            DatabaseException::common($e);
                            return redirect()->route('index');
                        }
                    }
                    try{
                        DB::delete('delete from dccmta where client_id = ? and projection_source_id = ?',
                        [$client,$delete_list]);
                    }catch(\Exception $e){
                        OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                        DatabaseException::common($e);
                        return redirect()->route('index');
                    }

                }elseif($code == "ji"){
                    try{
                        DB::delete('delete  from dcji01 where client_id = ? and personnel_id = ?',
                        [$client,$delete_list]);
                    }catch(\Exception $e){
                        OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                        DatabaseException::common($e);
                        return redirect()->route('index');
                    }

                }elseif($code == "ta"){
                    try{
                        DB::delete('delete  from dccmta where client_id = ? and projection_id = ?',
                        [$client,$delete_list]);
                    }catch(\Exception $e){
                        OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                        DatabaseException::common($e);
                        return redirect()->route('index');
                    }
                }else{

                }
                //データの階層構造を削除
                try{
                    DB::delete('delete from dccmks where client_id = ? 
                    and lower_id = ?',[$client,$delete_list]);
                }catch(\Exception $e){
                    OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                    DatabaseException::common($e);
                    return redirect()->route('index');
                }

            }
            
        }
        //ログ処理
        OutputLog::message_log(__FUNCTION__, 'mhcmok0003');
        $message = Message::get_message('mhcmok0003',[0=>'']);
        session(['message'=>$message[0]]);

        PtcmtrController::delete_node($delete_data[0]->high_id);
        
        if($delete_data[0]->high_id == "bs"){
            redirect()->route('index');
        }
        return redirect()->route('plbs01.show',[$client,$delete_data[0]->high_id]);
    
    }

    /**
     * 部署データ検索
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $id 顧客ID
     * @param  string $id2 選択したID 
     * 
     * @var  string  $client_id 顧客ID
     * @var  string  $select_id  選択ID
     * @var  string  $click 選択したID
     * @var  int $count_department 部署ページネーションのページ数
     * @var  int $count_personnel 人員ページネーションのページ数
     * @var  array $department_data 部署データ
     * @var  array $personnel_data 人員データ
     * @var  App\Models\Date $date 
     * @var  App\Librarys\php\ResponsiblePerson $responsible
     * @var  App\Librarys\php\Hierarchical $hierarchical
     * @var  App\Librarys\php\Pagination $pagination
     * @var  int $department_max 部署データページネーションの最大値
     * @var  array $departments ページネーション後の部署データ
     * @var  int $personnel_max 人員データページネーションの最大値
     * @var  array $names ページネーション後の人員データ
     * @var  array $responsible_lists 責任者リスト
     * @var  array $department_high 部署データの上位階層
     * @var  array $personnel_high 人員データの上位階層
     * @var  App\Http\Controllers\PtcmtrController $tree
     * @var  array $tree_data ツリーデータ
     * 
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request,$id,$id2)
    {
        $client_id = $id;
        $select_id = $id2;

        $count_department = Config::get('startcount.count');
        $count_personnel = Config::get('startcount.count');

        $click_id = $select_id;
        View::share('click_id', $click_id);

        //データベースの検索
        try{
            $department_data = DB::select('select 
            dcbs01.client_id, department_id,responsible_person_id,name,status,management_personnel_id,operation_start_date,operation_end_date,lower_id, high_id, dcbs01.created_at, dcbs01.updated_at
            from dcbs01 inner join dccmks on dcbs01.department_id = dccmks.lower_id and dcbs01.client_id = ?
            where dcbs01.name like ?',[$client_id,'%'.$request->search.'%']);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('index');
        }
        try{
            $personnel_data = DB::select('select 
            dcji01.client_id ,personnel_id,name,email,password,password_update_day,status,management_personnel_id,login_authority,system_management,operation_start_date,operation_end_date,dcji01.created_at, dcji01.updated_at ,high_id ,lower_id
            from dcji01 inner join dccmks on dcji01.personnel_id = dccmks.lower_id and dcji01.client_id = ?',[$client_id]);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('index');
        }

        if(empty($department_data)){
            return redirect()->route('plbs01.show',[$client_id,$select_id]);
        }

        //詳細画面のデータ表示
        $select_code = substr($select_id,0,2);

        if($select_code == "ta"){
            //選択部署がtaだった場合は対応するIDを取得
            $projection_code = DB::select('select projection_source_id from dccmta where projection_id = ?', [$select_id]);
            $select_id = $projection_code[0]->projection_source_id;
            $select_code = substr($projection_code[0]->projection_source_id,0,2);
        }
        
        if($select_code == "bs"){
            //選択した部署のデータを取得
            try{
                $click_department_data = DB::select('select 
                dcbs01.client_id,department_id,responsible_person_id,name,status,management_personnel_id,operation_start_date,operation_end_date,lower_id, high_id, dcbs01.created_at, dcbs01.updated_at
                from dcbs01 inner join dccmks on dcbs01.department_id = dccmks.lower_id where dcbs01.client_id = ? and department_id = ?',[$client_id,$select_id]);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
            }
            View::share('click_department_data', $click_department_data);
            //部署データが存在しない場合、選択部署が最上位部署か判別
            if(empty($click_department_data)){
                $top_department = DB::select('select * from dcbs01 where client_id = ? and department_id = ?',[$client_id,$select_id]);

                View::share('top_department', $top_department);
            }
        
        }else{
            //選択した人員のデータを取得
            try{
                $click_personnel_data = DB::select('select 
                dcji01.client_id ,personnel_id,name,email,password,password_update_day,status,management_personnel_id,login_authority,system_management,operation_start_date,operation_end_date,dcji01.created_at, dcji01.updated_at ,high_id ,lower_id
                from dcji01 inner join dccmks on dcji01.personnel_id = dccmks.lower_id where dcji01.client_id = ?
                and dcji01.personnel_id = ?',[$client_id,$select_id]);
            }catch(\Exception $e){

                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
            }
            View::share('click_personnel_data', $click_personnel_data);

            //選択した人員の所属部署を取得
            try{
                $affiliation_data = DB::select('select high_id from dccmks where client_id = ?
                and lower_id = ?',[$client_id,$select_id]);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
            }
            
            //取得した部署IDを元に部署データを取得
            try{
                $data = DB::select('select 
                dcbs01.client_id, department_id,responsible_person_id,name,status,management_personnel_id,operation_start_date,operation_end_date,lower_id, high_id, dcbs01.created_at, dcbs01.updated_at
                from dcbs01 inner join dccmks on dcbs01.department_id = dccmks.lower_id where dcbs01.client_id = ?
                and dcbs01.department_id = ?',[$client_id,$affiliation_data[0]->high_id]);
            }catch(\Exception $e){

                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
            }
            View::share('data', $data);
        }

        //日付を6桁にする
        $date = new Date();
        if(isset($top_department)){
            $date->formatDate($top_department);
        }
        if(isset($click_department_data)){
            $date->formatDate($click_department_data);
        }
        if(isset($click_personnel_data)){
            $date->formatDate($click_personnel_data);
        }

        //責任者を名前で取得
        $responsible = new ResponsiblePerson();
        if(isset($top_department)){
            $top_responsible = $responsible->getResponsibleLists($client_id,$top_department);
            View::share('top_responsible', $top_responsible);
        }
        if(isset($click_department_data)){
            $click_responsible_lists = $responsible->getResponsibleLists($client_id,$click_department_data);
            View::share('click_responsible_lists', $click_responsible_lists);
        }


        //管理者を名前で取得
        if(isset($top_department)){
            $top_management = $responsible->getManagementLists($client_id,$top_department);
            View::share('top_management', $top_management);
        }
        if(isset($click_department_data)){
            $click_management_lists = $responsible->getManagementLists($client_id,$click_department_data);
            View::share('click_management_lists', $click_management_lists);
        }
        if(isset($click_personnel_data)){
            $click_management_lists = $responsible->getManagementLists($client_id,$click_personnel_data);
            View::share('click_management_lists', $click_management_lists);
        }

        //上位階層取得
        $hierarchical = new Hierarchical();
        if(isset($click_department_data)){
            $click_department_high = $hierarchical->upperHierarchyName($click_department_data);
            View::share('click_department_high', $click_department_high);
        }

        //部署・人員の一覧表示領域のデータ表示
        //日付フォーマットを6桁にする
        $date = new Date();
        $date->formatDate($department_data);
        $date->formatDate($personnel_data);

        //基本ページネーション設定
        $pagination = new Pagination();
        $department_max = $pagination->pageMax($department_data,count($department_data));
        $departments = $pagination->pagination($department_data,count($department_data),$count_department);
        $personnel_max = $pagination->pageMax($personnel_data,count($personnel_data));
        $names = $pagination->pagination($personnel_data,count($personnel_data),$count_personnel);

        //責任者を名前で取得
        $responsible_lists = $responsible->getResponsibleLists($client_id,$departments);

        //上位階層取得
        $department_high = $hierarchical->upperHierarchyName($departments);
        $personnel_high = $hierarchical->upperHierarchyName($names);

        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        return view('pacm01.pacm01',compact('count_department','personnel_data','select_id','department_max','departments','personnel_max',
        'names','responsible_lists','department_high','personnel_high','count_personnel',));
    }

    /**
     * 
     * 複製したデータを挿入するメソッド
     * @param  \Illuminate\Http\Request  $request
     * 
     * @var string $client_id 顧客ID
     * @var string $copy_id 複製するID
     * @var string $high 複製IDが所属する上位階層ID
     * @var string $id 複製前の最新の部署ID
     * @var string $id2 複製前の最新の人員ID
     * @var string $id_num 複製前の最新の部署IDの数字部分
     * @var string $id2_num 複製前の最新の部署IDの数字部分
     * @var string $number 8桁に0埋めした複製前の最新の部署IDの数字部分
     * @var string $number2　8桁に0埋めした複製前の最新の人員IDの数字部分
     * @var App\Librarys\php\Hierarchical $hierarchical
     * 
     * @return \Illuminate\Http\Response
     */
    public function copy(Request $request){

        $client_id = $request->client_id;
        $copy_id = $request->copy_id;
        $high = $request->high_id;

        if($request->copy_id == null){
            return redirect()->route('index');
        }

        //複製動作
        //複製前の最新の部署番号を取得
        try{
            $id = DB::select('select department_id from dcbs01 where client_id = ? 
            order by department_id desc limit 1',[$client_id]);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('index');
        }
        //複製前の最新の人員番号を取得
        try{
            $id2 = DB::select('select personnel_id from dcji01 where client_id = ? 
            order by personnel_id desc limit 1',[$client_id]);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('index');
        }
        $id_num = substr($id[0]->department_id,3);
        $id2_num = substr($id2[0]->personnel_id,3);
        $number = str_pad($id_num, 8, '0', STR_PAD_LEFT);
        $number2 = str_pad($id2_num, 8, '0', STR_PAD_LEFT);
        $hierarchical = new Hierarchical();
        $hierarchical->subordinateCopy($copy_id,$client_id,$high,$number,$number2);

        //ログ処理
        OutputLog::message_log(__FUNCTION__, 'mhcmok0009');
        $message = Message::get_message('mhcmok0009',[0=>'']);
        session(['message'=>$message[0]]);
        return back();
    }
}
