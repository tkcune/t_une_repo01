<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Librarys\php\DatabaseException;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use App\Librarys\php\Pagination;
use App\Librarys\php\ResponsiblePerson;
use App\Librarys\php\Hierarchical;
use App\Http\Controllers\PtcmtrController;
use App\Librarys\php\OutputLog;
use App\Models\Date;
use App\Librarys\php\Message;
use App\Librarys\php\ListDisplay;
use Illuminate\Support\Facades\View;



/**
 * 導入画面のコントローラー
 */

class Pa0001Controller extends Controller
{
    /**
     * ディスプレイ表示
     *
     * @var  string  $client_id 顧客ID　9/27現在　ダミーデータ
     * @var　int $count_department 部署ページネーションのページ数
     * @var　int $count_personnel  人員ページネーションのページ数
     * @var  array $top_department 最上位の部署データ
     * @var  App\Models\Date $date
     * @var  array $department_data 全体部署データ
     * @var  array $personnel_data 全体人員データ
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
    public function index()
    {
        //$client_idの数値はダミー(ログイン時にセッション保存する予定)
        $client_id = "aa00000001";
        session(['client_id'=>$client_id]);

        $count_department = Config::get('startcount.count');
        $count_personnel = Config::get('startcount.count');

        //一番上の部署を取得
        try{
            $top_department = DB::select('select * from dcbs01 where client_id = ? limit 1',[$client_id]);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
        }

        //全体の部署データの取得
        try{
            $department_data = DB::select('select 
            dcbs01.client_id, department_id,responsible_person_id,name,status,management_personnel_id,operation_start_date,operation_end_date,lower_id, high_id, dcbs01.created_at, dcbs01.updated_at
            from dcbs01 inner join dccmks on dcbs01.department_id = dccmks.lower_id and dcbs01.client_id = ?',[$client_id]);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
        }

        //全体の人員データの取得
        try{
            $personnel_data = DB::select('select 
            dcji01.client_id ,personnel_id,name,email,password,password_update_day,status,management_personnel_id,login_authority,system_management,operation_start_date,operation_end_date,dcji01.created_at, dcji01.updated_at ,high_id ,lower_id
            from dcji01 inner join dccmks on dcji01.personnel_id = dccmks.lower_id and dcji01.client_id = ?',[$client_id]);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
        }

        //登録日付を6桁に変換
        $date = new Date();
        $date->formatDate($top_department);

        //責任者を名前で取得
        $responsible = new ResponsiblePerson();
        $top_responsible = $responsible->getResponsibleLists($client_id,$top_department);

        //管理者を名前で取得
        $top_management = $responsible->getManagementLists($client_id,$top_department);

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
        $responsible_lists = $responsible->getResponsibleLists($client_id,$departments);

        //上位階層取得
        $hierarchical = new Hierarchical();
        try{
            $department_high = $hierarchical->upperHierarchyName($departments);
            $personnel_high = $hierarchical->upperHierarchyName($names);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
            DatabaseException::dataCatchMiss($e);
            return redirect()->route('errormsg');
        }

        //ツリーデータの取得
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        //クリックコードの保存
        session(['click_code'=>'bs']);

        return view('pacm01.pacm01',compact('top_management','department_max','departments','personnel_max','names',
        'top_department','top_responsible','count_department','responsible_lists','department_high','personnel_high',
        'count_personnel','personnel_data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * ページネーションのページ数移動
     * 
     * @param  \Illuminate\Http\Request  $request
     * 
     * @var  int  $client_id 顧客ID　9/27現在　ダミーデータ(route indexでセッション保存)
     * @var　int $count_department 部署ページネーションのページ数
     * @var　int $count_personnel  人員ページネーションのページ数
     * @var  array $top_department 最上位の部署データ
     * @var  App\Models\Date $date
     * @var  array $department_data 部署データ
     * @var  array $personnel_data 人員データ
     * @var  App\Librarys\php\ResponsiblePerson $responsible
     * @var  array $top_responsible 最上位の責任者データ
     * @var  array $top_management 最上位の管理者データ
     * @var  App\Librarys\php\ListDisplay $list_display
     * @var  App\Http\Controllers\PtcmtrController $tree
     * @var  array $tree_data ツリーデータ
     *
     * @return \Illuminate\Http\Response
     */
    public function count(Request $request)
    {
        $client_id = session('client_id');
        $count_department = $_GET['department_page'];
        $count_personnel = $_GET['personnel_page'];

        //一番上の部署を取得
        try{
            $top_department = DB::select('select * from dcbs01 where client_id = ? limit 1',[$client_id]);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
        }
        
        //部署データの取得
        try{
            $department_data = DB::select('select 
            dcbs01.client_id, department_id,responsible_person_id,name,status,management_personnel_id,operation_start_date,operation_end_date,lower_id, high_id, dcbs01.created_at, dcbs01.updated_at
            from dcbs01 inner join dccmks on dcbs01.department_id = dccmks.lower_id and dcbs01.client_id = ?',[$client_id]);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
        }
        //人員データの取得
        try{
            $personnel_data = DB::select('select 
            dcji01.client_id ,personnel_id,name,email,password,password_update_day,status,management_personnel_id,login_authority,system_management,operation_start_date,operation_end_date,dcji01.created_at, dcji01.updated_at ,high_id ,lower_id
            from dcji01 inner join dccmks on dcji01.personnel_id = dccmks.lower_id and dcji01.client_id = ?',[$client_id]);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
            DatabaseException::common($e);
        }
        //登録日付を6桁に変換
        $date = new Date();
        $date->formatDate($top_department);

        //責任者を名前で取得
        $responsible = new ResponsiblePerson();
        $top_responsible = $responsible->getResponsibleLists($client_id,$top_department);

        //管理者を名前で取得
        $top_management = $responsible->getManagementLists($client_id,$top_department);
        
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
        $responsible = new ResponsiblePerson();
        $responsible_lists = $responsible->getResponsibleLists($client_id,$departments);

        //上位階層取得
        $hierarchical = new Hierarchical();
        try{
            $department_high = $hierarchical->upperHierarchyName($departments);
            $personnel_high = $hierarchical->upperHierarchyName($names);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
            DatabaseException::dataCatchMiss($e);
            return redirect()->route('errormsg');
        }

        //ツリーデータ取得
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        return view('pacm01.pacm01',compact('top_department','top_responsible','department_max','departments','personnel_max','names',
        'responsible_lists','department_high','personnel_high','top_management','count_department','count_personnel','personnel_data'));
    }

    /**
     * 部署を絞り込みした後のページネーション
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @var  array $lists 選択したIDを格納するリスト
     * @var  array $department_data 部署データ
     * @var  array $personnel_data 人員データ
     * @var  int  $client 顧客ID　9/27現在　ダミーデータ
     * @var  int  $select_id 選択したID
     * @var　int $count_department 部署ページネーションのページ数
     * @var　int $count_personnel  人員ページネーションのページ数
     * @var  array $click_department_data 選択した部署データ
     * @var  array $select_lists 選択した部署の配下データ
     * @var  string $code 機能コード
     * @var  array  $data 取得した配下データ
     * @var  array  $department_data 部署データ
     * @var  array  $personnel_data 人員データ
     * @var  array  $click_personnel_data 選択した人員データ
     * @var  array  $affiliation_data　選択した人員の所属部署データ
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
    public function count2(Request $request)
    {
        $lists = [];
        $department_data = [];
        $personnel_data = [];
    
        //リクエストの取得
        $client = $_GET['id'];
        $select_id = $_GET['id2'];
        $count_department = $_GET['department_page'];
        $count_personnel = $_GET['personnel_page'];

        $click_id = $select_id;
        View::share('click_id', $click_id);

        //選択したツリーで場合分け
        if(substr($select_id,0,2) == "bs"){
            //選択した部署のデータを取得
            try{
                $click_department_data = DB::select('select 
                dcbs01.client_id,department_id,responsible_person_id,name,status,management_personnel_id,operation_start_date,operation_end_date,lower_id, high_id, dcbs01.created_at, dcbs01.updated_at
                from dcbs01 inner join dccmks on dcbs01.department_id = dccmks.lower_id where dcbs01.client_id = ? and department_id = ?',[$client,$select_id]);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
            }

            //選択した部署のIDをarray型に格納
            array_push($lists,$select_id);
        
            //選択した部署の配下を取得
            $hierarchical = new Hierarchical();
            $select_lists = $hierarchical->subordinateSearch($lists,$client);
           
            //選択したデータ及び配下データを取得
            try{
                $lists = $hierarchical->subordinateGet($select_lists,$client);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::dataCatchMiss($e);
                return redirect()->route('errormsg');
            }
            $department_data = $lists[0];
            $personnel_data = $lists[1];

            //登録日付を6桁に変換
            $date = new Date();
            $date->formatDate($click_department_data);

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
       
            //上位階層取得
            $hierarchical = new Hierarchical();
            try{
                $click_department_high = $hierarchical->upperHierarchyName($click_department_data);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::dataCatchMiss($e);
                return redirect()->route('errormsg');
            }
           
            //部署・人員の一覧表示領域のデータ表示
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
            $hierarchical = new Hierarchical();
            try{
                $department_high = $hierarchical->upperHierarchyName($departments);
                $personnel_high = $hierarchical->upperHierarchyName($names);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::dataCatchMiss($e);
                return redirect()->route('errormsg');
            }

            $tree = new PtcmtrController();
            $tree_data = $tree->set_view_treedata();

            return view('pacm01.pacm01',compact('click_department_data','click_management_lists','department_max','departments','personnel_max','names','responsible_lists',
            'department_high','personnel_high','count_department','count_personnel','click_department_high','client','select_id','personnel_data'));
        }else{
            //選択した人員のデータを取得
            try{
                $click_personnel_data = DB::select('select 
                dcji01.client_id ,personnel_id,name,email,password,password_update_day,status,management_personnel_id,login_authority,system_management,operation_start_date,operation_end_date,dcji01.created_at, dcji01.updated_at,high_id
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
                $data = DB::select('select * from dcbs01 inner join dccmks on dcbs01.department_id = dccmks.lower_id where dcbs01.client_id = ?
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

                //部署・人員の一覧表示領域のデータ表示
                $list_display = new ListDisplay();
                $list_display->listDisplay($department_data,$personnel_data,$count_department,$count_personnel,$client);
        
                //ツリーデータの取得
                $tree = new PtcmtrController();
                $tree_data = $tree->set_view_treedata();

                return view('pacm01.pacm01',compact('top_management','click_management_lists',
                'top_department','top_responsible','count_department','count_personnel','client',
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

            //登録日付を6桁に変換
            $date = new Date();
            $date->formatDate($click_personnel_data);

            //責任者を名前で取得
            $responsible = new ResponsiblePerson();
            if(isset($click_personnel_data)){
                $click_management_lists = $responsible->getManagementLists($client,$click_personnel_data);
            }

            //部署・人員の一覧表示領域のデータ表示
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
            $hierarchical = new Hierarchical();
            try{
                $department_high = $hierarchical->upperHierarchyName($departments);
                $personnel_high = $hierarchical->upperHierarchyName($names);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::dataCatchMiss($e);
                return redirect()->route('errormsg');
            }
        
            //ツリーデータ取得
            $tree = new PtcmtrController();
            $tree_data = $tree->set_view_treedata();

            return view('pacm01.pacm01',compact('count_department','count_personnel','click_management_lists','data','department_max','departments',
            'personnel_max','names','responsible_lists','department_high','personnel_high','client','select_id','click_personnel_data','personnel_data'));
        }
    }

    /**
     * 選択した部署のIDを複写するメソッド
     * 
     * @param $id 選択したID
     * 
     * @return \Illuminate\Http\Response
     */ 
    public function clipboard($id){

        session(['clipboard_id'=>$id]);

        //ログ処理
        OutputLog::message_log(__FUNCTION__, 'mhcmok0004');
        $message = Message::get_message('mhcmok0004',[0=>$id]);
        session(['message'=>$message[0]]);

        return back();
    }

    /**
     * 複写したIDを削除するメソッド
     * 
     * @return \Illuminate\Http\Response
     */
    public function deleteClipboard(){

        session()->forget('clipboard_id');

        //ログ処理
        OutputLog::message_log(__FUNCTION__, 'mhcmok0005');
        $message = Message::get_message('mhcmok0005',[0=>'']);
        session(['message'=>$message[0]]);

        return back();
    }

    public function log_redirct(){

        
        return redirect()->route('test');
    }

    /**
     * エラーメッセージページ遷移
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function errorMsg()
    {
        //ツリーデータ取得
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        return view('pcms01.pcms01');
    }

}
