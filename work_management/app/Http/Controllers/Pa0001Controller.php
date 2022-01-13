<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Libraries\php\DatabaseException;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use App\Libraries\php\Pagination;
use App\Libraries\php\ResponsiblePerson;
use App\Libraries\php\Hierarchical;
use App\Http\Controllers\PtcmtrController;
use App\Facades\OutputLog;
use App\Models\Date;
use App\Libraries\php\Message;
use App\Libraries\php\ListDisplay;
use App\Libraries\php\OperationCheck;
use Illuminate\Support\Facades\View;
use App\Libraries\php\Domain\DepartmentDataBase;
use App\Libraries\php\Domain\PersonnelDataBase;
use App\Libraries\php\Domain\ProjectionDataBase;



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
     * @var  App\Libraries\php\DepartmentDataBase $department_db
     * @var  array $top_department 最上位の部署データ
     * @var  App\Models\Date $date
     * @var  array $operation_date 運用日を格納した配列
     * @var  array $department_data 全体部署データ
     * @var  array $personnel_data 全体人員データ
     * @var  App\Libraries\php\ResponsiblePerson $responsible
     * @var  array $top_responsible 最上位の責任者データ
     * @var  array $top_management 最上位の管理者データ
     * @var  App\Libraries\php\Pagination $pagination
     * @var  int $department_max 部署データページネーションの最大値
     * @var  array $departments ページネーション後の部署データ
     * @var  int $personnel_max 人員データページネーションの最大値
     * @var  array $names ページネーション後の人員データ
     * @var  array $responsible_lists 責任者リスト
     * @var  App\Libraries\php\Hierarchical $hierarchical
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
        $select_id = "bs00000001";
        $lists = [];
        session(['client_id'=>$client_id]);

        $count_department = Config::get('startcount.count');
        $count_personnel = Config::get('startcount.count');

        $department_db = new DepartmentDataBase();
        $personnel_db = new PersonnelDataBase();

        //一番上の部署を取得
        try{
            $top_department = $department_db->getTop($client_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }

        //全体の部署データの取得
        try{
            $all_department_data = $department_db->getAll($client_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }

        //全体の人員データの取得
        try{
            $all_personnel_data = $personnel_db->getAll($client_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }

        array_push($lists,$select_id);

        //選択した部署の配下を取得
        $hierarchical = new Hierarchical();
        $select_lists = $hierarchical->subordinateSearch($lists,$client_id);

        //選択したデータ及び配下データを取得
        try{
            $lists = $hierarchical->subordinateGet($select_lists,$client_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','02');
            DatabaseException::dataCatchMiss($e);
            return redirect()->route('pa0001.errormsg');
        }
        $department_data = $lists[0];
        $personnel_data = $lists[1];

        //責任者を名前で取得
        $responsible = new ResponsiblePerson();
        $top_responsible = $responsible->getResponsibleLists($client_id,$top_department);

        //管理者を名前で取得
        $top_management = $responsible->getManagementLists($client_id,$top_department);

        //日付を変換
        $date = new Date();
        $operation_date = $date->formatOperationDate($top_department);
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
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','02');
            DatabaseException::dataCatchMiss($e);
            return redirect()->route('pa0001.errormsg');
        }

        //ツリーデータの取得
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        //クリックコードの保存
        session(['click_code'=>'bs']);

        //運用状況の確認
        $operation_check = new OperationCheck();
        $operation_check->check($top_department);

        return view('pacm01.pacm01',compact('top_management','department_max','departments','personnel_max','names',
        'top_department','top_responsible','count_department','responsible_lists','department_high','personnel_high',
        'department_data','count_personnel','personnel_data','operation_date','all_personnel_data'));
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
     * @var  App\Libraries\php\DepartmentDataBase $department_db
     * @var  array $top_department 最上位の部署データ
     * @var  App\Models\Date $date
     * @var  array $department_data 部署データ
     * @var  array $personnel_data 人員データ
     * @var  App\Libraries\php\ResponsiblePerson $responsible
     * @var  array $top_responsible 最上位の責任者データ
     * @var  array $top_management 最上位の管理者データ
     * @var  App\Libraries\php\ListDisplay $list_display
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

        $department_db = new DepartmentDataBase();
        $personnel_db = new PersonnelDataBase();

        //一番上の部署を取得
        try{
            $top_department = $department_db->getTop($client_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
        }

        //全ての人員データの取得
        try{
            $all_personnel_data = $personnel_db->getAll($client_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
            DatabaseException::common($e);
        }
        array_push($lists,$select_id);

        //選択した部署の配下を取得
        $hierarchical = new Hierarchical();
        $select_lists = $hierarchical->subordinateSearch($lists,$client_id);

        //選択したデータ及び配下データを取得
        try{
            $lists = $hierarchical->subordinateGet($select_lists,$client_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','02');
            DatabaseException::dataCatchMiss($e);
            return redirect()->route('pa0001.errormsg');
        }
        $department_data = $lists[0];
        $personnel_data = $lists[1];

        //登録日付フォーマットを変更
        $date = new Date();
        $operation_date = $date->formatOperationDate($top_department);

        //責任者を名前で取得
        $responsible = new ResponsiblePerson();
        $top_responsible = $responsible->getResponsibleLists($client_id,$top_department);

        //管理者を名前で取得
        $top_management = $responsible->getManagementLists($client_id,$top_department);
        
        //日付フォーマットを変更する
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
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','02');
            DatabaseException::dataCatchMiss($e);
            return redirect()->route('pa0001.errormsg');
        }

        //ツリーデータ取得
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        //運用状況の確認
        $operation_check = new OperationCheck();
        $operation_check->check($top_department);

        return view('pacm01.pacm01',compact('top_department','top_responsible','department_max','departments','personnel_max','names',
        'department_data','responsible_lists','department_high','personnel_high','top_management','count_department','count_personnel','personnel_data','operation_date','all_personnel_data'));
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
     * @var  App\Libraries\php\ResponsiblePerson $responsible
     * @var  array $top_responsible 最上位の責任者データ
     * @var  array $top_management 最上位の管理者データ
     * @var  App\Libraries\php\Pagination $pagination
     * @var  int $department_max 部署データページネーションの最大値
     * @var  array $departments ページネーション後の部署データ
     * @var  int $personnel_max 人員データページネーションの最大値
     * @var  array $names ページネーション後の人員データ
     * @var  array $responsible_lists 責任者リスト
     * @var  App\Libraries\php\Hierarchical $hierarchical
     * @var  array $department_high 部署データの上位階層
     * @var  array $personnel_high 人員データの上位階層
     * @var  App\Http\Controllers\PtcmtrController $tree
     * @var  array $tree_data ツリーデータ
     * 
     * @return \Illuminate\Http\Response
     */
    public function countNarrowDown(Request $request)
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

        //人員データの取得
        try{
            $personnel_db = new PersonnelDataBase();
            $all_personnel_data = $personnel_db->getAll($client);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
            DatabaseException::common($e);
        }

        //選択したツリーで場合分け
        if(substr($select_id,0,2) == "bs"){
            //選択した部署のデータを取得
            try{
                $department_db = new DepartmentDataBase();
                $click_department_data = $department_db->get($client,$select_id);
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
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','02');
                DatabaseException::dataCatchMiss($e);
                return redirect()->route('pa0001.errormsg');
            }
            $department_data = $lists[0];
            $personnel_data = $lists[1];

            //登録日付を変換
            $date = new Date();
            $operation_date = $date->formatOperationDate($click_department_data);

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
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','02');
                DatabaseException::dataCatchMiss($e);
                return redirect()->route('pa0001.errormsg');
            }
           
            //日付フォーマットを変更する
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
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','02');
                DatabaseException::dataCatchMiss($e);
                return redirect()->route('pa0001.errormsg');
            }

            $tree = new PtcmtrController();
            $tree_data = $tree->set_view_treedata();

            return view('pacm01.pacm01',compact('click_department_data','click_management_lists','department_max','departments','personnel_max','names','responsible_lists',
            'department_data','department_high','personnel_high','count_department','count_personnel','click_department_high','client','select_id','personnel_data','operation_date','all_personnel_data'));
        }else{
            //選択した人員のデータを取得
            try{
                $personnel_db = new PersonnelDataBase();
                $click_personnel_data = $personnel_db->getClick($client,$select_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
            }

            //選択した人員の所属部署を取得
            try{
                $personnel_db = new PersonnelDataBase();
                $affiliation_data = $personnel_db->getClickDepartment($client,$select_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
            }
            //取得した部署IDを元に部署データを取得
            try{
                $department_db = new DepartmentDataBase();
                $data = $department_db->getClickDepartment($client,$affiliation_data[0]->high_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
            }

            //部署データが存在しない場合、選択部署が最上位部署か判別
            if(empty($data)){
                $department_db = new DepartmentDataBase();
                $top_department = $department_db->getClickTop($client,$affiliation_data[0]->high_id);

                //全体の部署データの取得
                try{
                    $department_data = $department_db->getAll($client);
                }catch(\Exception $e){
                    OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                    DatabaseException::common($e);
                }

                //全体の人員データの取得
                try{
                    $personnel_db = new PersonnelDataBase();
                    $personnel_data = $personnel_db->getAll($client);
                }catch(\Exception $e){
                    OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                    DatabaseException::common($e);
                }
                //日付フォーマットを変更する
                $date = new Date();
                $operation_date = $date->formatOperationDate($click_personnel_data);
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
                $top_responsible = $responsible->getResponsibleLists($client,$top_department);
                $responsible_lists = $responsible->getResponsibleLists($client,$departments);

                //上位階層取得
                $hierarchical = new Hierarchical();
                try{
                    $department_high = $hierarchical->upperHierarchyName($departments);
                    $personnel_high = $hierarchical->upperHierarchyName($names);
                }catch(\Exception $e){
                    OutputLog::message_log(__FUNCTION__, 'mhcmer0001','02');
                    DatabaseException::dataCatchMiss($e);
                    return redirect()->route('pa0001.errormsg');
                }

                //管理者を名前で取得
                $top_management = $responsible->getManagementLists($client,$top_department);
                $click_management_lists = $responsible->getManagementLists($client,$click_personnel_data);

                //ツリーデータの取得
                $tree = new PtcmtrController();
                $tree_data = $tree->set_view_treedata();
                
                return view('pacm01.pacm01',compact('top_management','click_management_lists','department_max','departments',
                'personnel_max','names','department_high','personnel_high','responsible_lists','top_department','top_responsible','count_department','count_personnel','client',
                'department_data','select_id','personnel_data','click_personnel_data','operation_date','all_personnel_data'));
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

            //登録日付を変換
            $date = new Date();
            $operation_date = $date->formatOperationDate($click_personnel_data);

            //責任者を名前で取得
            $responsible = new ResponsiblePerson();
            if(isset($click_personnel_data)){
                $click_management_lists = $responsible->getManagementLists($client,$click_personnel_data);
            }

            //日付フォーマットを変更する
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
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','02');
                DatabaseException::dataCatchMiss($e);
                return redirect()->route('pa0001.errormsg');
            }
        
            //ツリーデータ取得
            $tree = new PtcmtrController();
            $tree_data = $tree->set_view_treedata();

            return view('pacm01.pacm01',compact('count_department','count_personnel','click_management_lists','data','department_max','departments',
            'department_data','personnel_max','names','responsible_lists','department_high','personnel_high','client','select_id','click_personnel_data','personnel_data','operation_date','all_personnel_data'));
        }
    }

    /**
     * 部署トップのページネーションのページ数移動
     * 
     * @param  \Illuminate\Http\Request  $request
     * 
     * @var  int  $client_id 顧客ID　9/27現在　ダミーデータ(route indexでセッション保存)
     * @var　int $count_department 部署ページネーションのページ数
     * @var　int $count_personnel  人員ページネーションのページ数
     * @var  App\Libraries\php\DepartmentDataBase $department_db
     * @var  App\Libraries\php\PersonnelDataBase $personnel_db
     * @var  array $top_department 最上位の部署データ
     * @var  App\Models\Date $date
     * @var  array $department_data 部署データ
     * @var  array $personnel_data 人員データ
     * @var  App\Libraries\php\ResponsiblePerson $responsible
     * @var  array $top_responsible 最上位の責任者データ
     * @var  array $top_management 最上位の管理者データ
     * @var  App\Libraries\php\ListDisplay $list_display
     * @var  App\Http\Controllers\PtcmtrController $tree
     * @var  array $tree_data ツリーデータ
     *
     * @return \Illuminate\Http\Response
     */
    public function countTop(Request $request)
    {
        $client_id = session('client_id');
        $count_department = $_GET['department_page'];
        $count_personnel = $_GET['personnel_page'];
        $department_db = new DepartmentDataBase();
        $personnel_db = new PersonnelDataBase();

        //一番上の部署を取得
        try{
            $top_department = $department_db->getTop($client_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }
        
        //部署データの取得
        try{
            $department_data = $department_db->getAll($client_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
        }
        //人員データの取得
        try{
            $personnel_data = $personnel_db->getAll($client_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
            DatabaseException::common($e);
        }

        //責任者を名前で取得
        $responsible = new ResponsiblePerson();
        $top_responsible = $responsible->getResponsibleLists($client_id,$top_department);

        //管理者を名前で取得
        $top_management = $responsible->getManagementLists($client_id,$top_department);
        
        //日付フォーマットを変更
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
        $responsible_lists = $responsible->getResponsibleLists($client_id,$departments);

        //上位階層取得
        $hierarchical = new Hierarchical();
        try{
            $department_high = $hierarchical->upperHierarchyName($departments);
            $personnel_high = $hierarchical->upperHierarchyName($names);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','02');
            DatabaseException::dataCatchMiss($e);
            return redirect()->route('pa0001.errormsg');
        }

        //ツリーデータ取得
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        return view('pvbs01.pvbs01',compact('top_department','top_responsible','department_max','departments','personnel_max','names',
        'department_data','responsible_lists','department_high','personnel_high','top_management','count_department','count_personnel','personnel_data'));
    }

    /**
     * 部署データ検索した後のページネーション
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @var  string  $client_id 顧客ID
     * @var  string  $select_id  選択ID
     * @var  string  $click 選択したID
     * @var  int $count_department 部署ページネーションのページ数
     * @var  int $count_personnel 人員ページネーションのページ数
     * @var  array $department_data 部署データ
     * @var  array $personnel_data 人員データ
     * @var  App\Models\Date $date 
     * @var  App\Libraries\php\ResponsiblePerson $responsible
     * @var  App\Libraries\php\Hierarchical $hierarchical
     * @var  App\Libraries\php\Pagination $pagination
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
    public function countSearchDepartment(Request $request)
    {
        $client_id = $_GET['id'];
        $select_id = $_GET['id2'];

        $_POST['search']=$_GET['search'];

        $count_department = $_GET['department_page'];
        $count_personnel = $_GET['personnel_page'];

        $click_id = $select_id;
        View::share('click_id', $click_id);

        //全体の人員データの取得
        try{
            $personnel_db = new PersonnelDataBase();
            $all_personnel_data = $personnel_db->getAll($client_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
        }

        //データベースの検索
        try{
            $department_db = new DepartmentDataBase();
            $department_data = $department_db->search($client_id,$_GET['search']);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('index');
        }
        try{
            $personnel_db = new PersonnelDataBase();
            $personnel_data = $personnel_db->getAll($client_id);
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
            $projection_db = new ProjectionDataBase();
            $projection_code = $projection_db->getId($select_id);
            $select_id = $projection_code[0]->projection_source_id;
            $select_code = substr($projection_code[0]->projection_source_id,0,2);
        }
        
        if($select_code == "bs"){
            //選択した部署のデータを取得
            try{
                $department_db = new DepartmentDataBase();
                $click_department_data = $department_db->get($client_id,$select_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
            }
            View::share('click_department_data', $click_department_data);
            //部署データが存在しない場合、選択部署が最上位部署か判別
            if(empty($click_department_data)){
                $department_db = new DepartmentDataBase();
                $top_department = $department_db->getClickTop($client_id,$select_id);

                View::share('top_department', $top_department);
            }
        
        }else{
            //選択した人員のデータを取得
            try{
                $personnel_db = new PersonnelDataBase();
                $click_personnel_data = $personnel_db->get($client_id,$select_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
            }
            View::share('click_personnel_data', $click_personnel_data);

            //選択した人員の所属部署を取得
            try{
                $personnel_db = new PersonnelDataBase();
                $affiliation_data = $personnel_db->getClickDepartment($client_id,$select_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
            }
            
            //取得した部署IDを元に部署データを取得
            try{
                $department_db = new DepartmentDataBase();
                $data = $department_db->getClickDepartmentData($client_id,$affiliation_data[0]->high_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
            }
            View::share('data', $data);
        }

        //運用開始日、運用終了日のフォーマット変更
        $date = new Date();
        if(isset($top_department)){
            $operation_date = $date->formatOperationDate($top_department);
        }
        if(!empty($click_department_data)){
            $operation_date = $date->formatOperationDate($click_department_data);
        }
        if(isset($click_personnel_data)){
            $operation_date = $date->formatOperationDate($click_personnel_data);
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

        //部署・人員の一覧表示領域のデータ表示
        //日付フォーマットを変更する
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
        $hierarchical = new Hierarchical();
        try{
            if(isset($click_department_data)){
                $click_department_high = $hierarchical->upperHierarchyName($click_department_data);
                View::share('click_department_high', $click_department_high);
            }
            $department_high = $hierarchical->upperHierarchyName($departments);
            $personnel_high = $hierarchical->upperHierarchyName($names);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','02');
            DatabaseException::dataCatchMiss($e);
            return redirect()->route('pa0001.errormsg');
        }

        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        return view('pacm01.pacm01',compact('count_department','personnel_data','select_id','department_max','departments','personnel_max',
        'department_data','names','responsible_lists','department_high','personnel_high','count_personnel','operation_date','all_personnel_data'));
    }

    /**
     * 人員データ検索した後のページネーション
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @var  string  $client_id　顧客ID
     * @var  string  $select_id  選択ID
     * @var  int $count_department 部署ページネーションのページ数
     * @var  int $count_personnel 人員ページネーションのページ数
     * @var  array $department_data 部署データ
     * @var  array $personnel_data 人員データ
     * @var  App\Models\Date; $date
     * @var  App\Libraries\php\ResponsiblePerson $responsible
     * @var  array $top_responsible 最上位の責任者データ
     * @var  App\Libraries\php\Hierarchical $hierarchical
     * @var  App\Libraries\php\Pagination $pagination
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
    public function countSearchPersonnel(Request $request)
    {
        $client_id = $_GET['id'];
        $select_id = $_GET['id2'];

        $_POST['search2']=$_GET['search'];

        $count_department = $_GET['department_page'];
        $count_personnel = $_GET['personnel_page'];

        $click_id = $select_id;
        View::share('click_id', $click_id);

        //全体の人員データの取得
        try{
            $personnel_db = new PersonnelDataBase();
            $all_personnel_data = $personnel_db->getAll($client_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
        }

        //データベースの検索
        try{
            $department_db = new DepartmentDataBase();
            $department_data = $department_db->getData($client_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('index');
        }
        try{
            $personnel_db = new PersonnelDataBase();
            $personnel_data = $personnel_db->search($client_id,$_GET['search']);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('index');
        }

        //検索結果が0件なら戻る
        if(empty($personnel_data)){
            return redirect()->route('plbs01.show',[$client_id,$select_id]);
        }

        //画面表示データの取得
        $select_code = substr($select_id,0,2);

        if($select_code == "ta"){
            //選択部署がtaだった場合は対応するIDを取得
            $projection_db = new ProjectionDataBase();
            $projection_code = $projection_db->getId($select_id);
            $select_id = $projection_code[0]->projection_source_id;
            $select_code = substr($projection_code[0]->projection_source_id,0,2);
        }
        
        if($select_code == "bs"){
            //選択した部署のデータを取得
            try{
                $department_db = new DepartmentDataBase();
                $click_department_data = $department_db->get($client_id,$select_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
            }
            View::share('click_department_data', $click_department_data);
            //部署データが存在しない場合、選択部署が最上位部署か判別
            if(empty($click_department_data)){
                $department_db = new DepartmentDataBase();
                $top_department = $department_db->getClickTop($client_id,$select_id);
                View::share('top_department', $top_department);
            }
        
        }else{
            //選択した人員のデータを取得
            try{
                $personnel_db = new PersonnelDataBase();
                $click_personnel_data = $personnel_db->get($client,$select_id);
            }catch(\Exception $e){

                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
            }
            View::share('click_personnel_data', $click_personnel_data);

            //選択した人員の所属部署を取得
            try{
                $personnel_db = new PersonnelDataBase();
                $affiliation_data = $personnel_db->getClickDepartment($client_id,$select_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
            }
            
            //取得した部署IDを元に部署データを取得
            try{
                $department_db = new DepartmentDataBase();
                $data = $department_db->getClickDepartmentData($client_id,$affiliation_data[0]->high_id);
            }catch(\Exception $e){

                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
            }
            View::share('data', $data);
        }

        //日付フォーマットを変更する
        $date = new Date();
        if(isset($top_department)){
            $operation_date = $date->formatOperationDate($top_department);
        }
        if(isset($click_department_data)){
            $operation_date = $date->formatOperationDate($click_department_data);
        }
        if(isset($click_personnel_data)){
            $operation_date = $date->formatOperationDate($click_personnel_data);
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

        //日付フォーマットを変更する
        $date = new Date();
        $date->formatDate($department_data);
        $date->formatDate($personnel_data);

        //運用開始日、運用終了日のフォーマット変更
        if(isset($top_department)){
            $operation_date = $date->formatOperationDate($top_department);
        }
        if(!empty($click_department_data)){
            $operation_date = $date->formatOperationDate($click_department_data);
        }
        if(isset($click_personnel_data)){
            $operation_date = $date->formatOperationDate($click_personnel_data);
        }

        //基本ページネーション設定
        $pagination = new Pagination();
        $department_max = $pagination->pageMax($department_data,count($department_data));
        $departments = $pagination->pagination($department_data,count($department_data),$count_department);
        $personnel_max = $pagination->pageMax($personnel_data,count($personnel_data));
        $names = $pagination->pagination($personnel_data,count($personnel_data),$count_personnel);

        //責任者を名前で取得
        $responsible_lists = $responsible->getResponsibleLists($client_id,$departments);

        //上位階層取得
        $hierarchical = new Hierarchical();
        try{
            if(isset($click_department_data)){
                $click_department_high = $hierarchical->upperHierarchyName($click_department_data);
                View::share('click_department_high', $click_department_high);
            }
            $department_high = $hierarchical->upperHierarchyName($departments);
            $personnel_high = $hierarchical->upperHierarchyName($names);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','02');
            DatabaseException::dataCatchMiss($e);
            return redirect()->route('pa0001.errormsg');
        }

        //ツリーデータの取得
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        return view('pacm01.pacm01',compact('count_department','personnel_data','select_id','count_personnel','department_max',
        'department_data','departments','personnel_max','names','responsible_lists','department_high','personnel_high','operation_date','all_personnel_data'));
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
        OutputLog::message_log(__FUNCTION__, 'mhcmok0004',$id);
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

        
        return redirect()->route('pslg.index');
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

    /**
     * 再表示するメソッド
     * 
     * @return \Illuminate\Http\Response
     */
    public function redirect(){

        //クリックボードが保存されている場合は削除
        if(!(null == session()->get('clipboard_id'))){

            session()->forget('clipboard_id');

            //ログ処理
            OutputLog::message_log(__FUNCTION__, 'mhcmok0005');
            $message = Message::get_message('mhcmok0005',[0=>'']);
            session(['message'=>$message[0]]);
        }

        return back();
    }

    /**
     * 部署トップを表示するメソッド
     * 
     * 
     * @return \Illuminate\Http\Response
     */
    public function top(){

        //$client_idの数値はダミー(ログイン時にセッション保存する予定)
        $client_id = "aa00000001";
        session(['client_id'=>$client_id]);

        $count_department = Config::get('startcount.count');
        $count_personnel = Config::get('startcount.count');

        $department_db = new DepartmentDataBase();

        //一番上の部署を取得
        try{
            $top_department = $department_db->getTop($client_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }

        //全体の部署データの取得
        try{
            $department_data = $department_db->getAll($client_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }

        //全体の人員データの取得
        try{
            $personnel_db = new PersonnelDataBase();
            $personnel_data = $personnel_db->getAll($client_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }      

        //責任者を名前で取得
        $responsible = new ResponsiblePerson();
        $top_responsible = $responsible->getResponsibleLists($client_id,$top_department);

        //日付フォーマット変更
        $date = new Date();
        $operation_date = $date->formatOperationDate($top_department);
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
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','02');
            DatabaseException::dataCatchMiss($e);
            return redirect()->route('pa0001.errormsg');
        }

        //ツリーデータの取得
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        return view('pvbs01.pvbs01',compact('department_max','departments','personnel_max','names',
        'top_department','top_responsible','count_department','responsible_lists','department_high','personnel_high',
        'department_data','count_personnel','personnel_data','operation_date'));
    }

}
