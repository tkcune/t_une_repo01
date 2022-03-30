<?php

namespace App\Http\Controllers;

use App\Facades\OutputLog;
use App\Models\Date;
use App\Http\Controllers\PtcmtrController;
use App\Libraries\php\Domain\DepartmentDataBase;
use App\Libraries\php\Domain\PersonnelDataBase;
use App\Libraries\php\Domain\ProjectionDataBase;
use App\Libraries\php\Domain\Hierarchical;
use App\Libraries\php\Logic\ResponsiblePerson;
use App\Libraries\php\Service\DatabaseException;
use App\Libraries\php\Service\Message;
use App\Libraries\php\Service\OperationCheck;
use App\Libraries\php\Service\Pagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;

use App\Libraries\php\Service\DepartmentDetailsObject;
use App\Libraries\php\Service\PersonnelDetailsObject;



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
     * @var  App\Libraries\php\Domain\DepartmentDataBase $department_db
     * @var  App\Libraries\php\Domain\PersonnelDataBase $personnel_db
     * @var  App\Libraries\php\Domain\ProjectionDataBase $projection_db
     * @var  array $department_data 部署データ
     * @var  array $personnel_data 人員データ
     * @var  array $system_management_lists システム管理者リスト
     * @var  App\Libraries\php\Service\Pagination $pagination
     * @var  int $department_max 部署データページネーションの最大値
     * @var  array $departments 一覧表記部署データ
     * @var  int $personnel_max 人員データページネーションの最大値
     * @var  array $names 一覧表記人員データ
     * @var  $pagination_object ページネーションオブジェクト
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

        //ログイン機能が完成次第、そちらで取得可能なため、このセッション取得を削除する。
        session(['client_id'=>$client_id]);

        $select_code = substr($select_id,0,2);

        $click_id = $select_id;
        View::share('click_id', $click_id);

        $count_department = Config::get('startcount.count');
        $count_personnel = Config::get('startcount.count');

        $department_db = new DepartmentDataBase();
        $personnel_db = new PersonnelDataBase();
        $projection_db = new ProjectionDataBase();

        //詳細画面のデータ取得
        try{
            $click_department_data = $department_db->get($client_id,$select_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }

        //一覧の部署データの取得
        try{
            $department_data = $department_db->getSelectList($client_id,$select_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }

        //一覧の投影部署データの取得
        try{
            $projection_department = $projection_db->getDepartmentList($client_id,$select_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }

        //投影データを一覧部署に追加
        $department_data = array_merge($department_data,$projection_department);

        //一覧の人員データの取得
        try{
            $personnel_data = $personnel_db->getSelectList($client_id,$select_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }

        //一覧の投影人員データの取得
        try{
            $projection_personnel = $projection_db->getPersonnelList($client_id,$select_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }

        //投影データを一覧人員に追加
        $personnel_data = array_merge($personnel_data,$projection_personnel);

        //システム管理者のリストを取得
        try{
            $system_management_lists = $personnel_db->getSystemManagement($client_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }

        //基本ページネーション設定
        $pagination = new Pagination();
        $department_max = $pagination->pageMax($department_data,count($department_data));
        $departments = $pagination->pagination($department_data,count($department_data),$count_department);
        $personnel_max= $pagination->pageMax($personnel_data,count($personnel_data));
        $names = $pagination->pagination($personnel_data,count($personnel_data),$count_personnel);

        //ページネーションオブジェクト設定
        $pagination_object = new Pagination();
        //ページネーションオブジェクトをセットする
        $pagination_object->set_pagination($department_data, $count_department, $personnel_data, $count_personnel);

        //ツリーデータの取得
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        //クリックコードの保存
        session(['click_code'=>'bs']);

        //運用状況の確認
        $operation_check = new OperationCheck();
        $operation_check->check($click_department_data,$select_code);

        //詳細画面オブジェクトの設定
        $department_details_object = new DepartmentDetailsObject();
        $department_details_object->setDepartmentObject($click_department_data);

        if(session('device') != 'mobile'){
            return view('pacm01.pacm01',compact('department_max','departments','personnel_max','names','system_management_lists',
            'count_department','department_data','count_personnel','personnel_data','click_department_data','select_id'));
        }else{
            $click_id = NULL;
            $click_management_lists = $top_management;
            $click_responsible_lists = $top_responsible;
            $click_data = $top_department;
            $select_id = "bs00000001";
            $department_high = NULL;
            return view('pacm01.pacm02',compact('click_data', 'pagination_object', 'department_details_object',
            'click_management_lists', 'click_responsible_lists', 'click_id', 'select_id',
            'responsible_lists', 'personnel_high', 'department_high', 'personnel_data', 'all_personnel_data'));
        }
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
     * 部署を絞り込みした後のページネーション
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @var  int  $client 顧客ID　9/27現在　ダミーデータ
     * @var　int $count_department 部署ページネーションのページ数
     * @var　int $count_personnel  人員ページネーションのページ数
     * @var  App\Libraries\php\Domain\DepartmentDataBase $department_db
     * @var  App\Libraries\php\Domain\PersonnelDataBase $personnel_db
     * @var  App\Libraries\php\Domain\ProjectionDataBase $projection_db
     * @var  array  $department_data 部署データ
     * @var  array  $personnel_data 人員データ
     * @var  array  $click_department_data 選択した部署データ
     * @var  array  $click_personnel_data 選択した人員データ
     * @var  array  $system_management_lists システム管理者リスト
     * @var  App\Libraries\php\Service\Pagination $pagination
     * @var  int $department_max 部署データページネーションの最大値
     * @var  array $departments ページネーション後の部署データ
     * @var  int $personnel_max 人員データページネーションの最大値
     * @var  array $names ページネーション後の人員データ
     * @var  App\Http\Controllers\PtcmtrController $tree
     * @var  array $tree_data ツリーデータ
     * 
     * @return \Illuminate\Http\Response
     */
    public function countNarrowDown(Request $request)
    {

        //リクエストの取得
        $client_id = $_GET['id'];
        $select_id = $_GET['id2'];
        $count_department = $_GET['department_page'];
        $count_personnel = $_GET['personnel_page'];

        $click_id = $select_id;
        View::share('click_id', $click_id);

        $department_db = new DepartmentDataBase();
        $personnel_db = new PersonnelDataBase();
        $projection_db = new ProjectionDataBase();

        //選択したツリーで場合分け
        if(substr($select_id,0,2) == "bs"){
            
            //詳細画面のデータ取得
            try{
                $click_department_data = $department_db->get($client_id,$select_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('pa0001.errormsg');
            }

            //一覧の部署データの取得
            try{
                $department_data = $department_db->getSelectList($client_id,$select_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('pa0001.errormsg');
            }

            //一覧の投影部署データの取得
            try{
                $projection_department = $projection_db->getDepartmentList($client_id,$select_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('pa0001.errormsg');
            }

            //投影データを一覧部署に追加
            $department_data = array_merge($department_data,$projection_department);

            //一覧の人員データの取得
            try{
                $personnel_data = $personnel_db->getSelectList($client_id,$select_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('pa0001.errormsg');
            }

            //一覧の投影人員データの取得
            try{
                $projection_personnel = $projection_db->getPersonnelList($client_id,$select_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('pa0001.errormsg');
            }

            //投影データを一覧人員に追加
            $personnel_data = array_merge($personnel_data,$projection_personnel);

            //システム管理者のリストを取得
            try{
                $system_management_lists = $personnel_db->getSystemManagement($client_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('pa0001.errormsg');
            }

            //基本ページネーション設定
            $pagination = new Pagination();
            $department_max = $pagination->pageMax($department_data,count($department_data));
            $departments = $pagination->pagination($department_data,count($department_data),$count_department);
            $personnel_max= $pagination->pageMax($personnel_data,count($personnel_data));
            $names = $pagination->pagination($personnel_data,count($personnel_data),$count_personnel);

            //ページネーションオブジェクト設定
            $pagination_object = new Pagination();
            //ページネーションオブジェクトをセットする
            $pagination_object->set_pagination($department_data, $count_department, $personnel_data, $count_personnel);

            $tree = new PtcmtrController();
            $tree_data = $tree->set_view_treedata();

            if(session('device') != 'mobile'){
                return view('pacm01.pacm01',compact('click_department_data','department_max','departments','personnel_max','names',
                'department_data','count_department','count_personnel','client_id','select_id','personnel_data','system_management_lists'));
            }else{
                //部署詳細オブジェクトの設定
                $department_details_object = new DepartmentDetailsObject();
                $department_details_object->setDepartmentObject($click_department_data);
                $click_data = $click_department_data;
                return view('pacm01.pacm02',compact('department_high', 'click_data', 'pagination_object', 'department_details_object',
                'responsible_lists', 'personnel_high', 'click_management_lists', 'select_id', 'personnel_data', 'all_personnel_data'));
            }
        }else{
            //詳細画面人員のデータを取得
            try{
                $click_personnel_data = $personnel_db->get($client_id,$select_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
            }

            //一覧の部署データの取得
            try{
                $department_data = $department_db->getDepartmentList($client_id,$select_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('pa0001.errormsg');
            }

            //一覧の投影部署データの取得
            try{
                $projection_department = $projection_db->getDepartmentList($client_id,$select_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('pa0001.errormsg');
            }

            //投影データを一覧部署に追加
            $department_data = array_merge($department_data,$projection_department);

            //一覧の人員データの取得
            try{
                $personnel_data = $personnel_db->getSelectList($client_id,$select_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('pa0001.errormsg');
            }

            //システム管理者のリストを取得
            try{
                $system_management_lists = $personnel_db->getSystemManagement($client_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('pa0001.errormsg');
            }

            //基本ページネーション設定
            $pagination = new Pagination();
            $department_max = $pagination->pageMax($department_data,count($department_data));
            $departments = $pagination->pagination($department_data,count($department_data),$count_department);
            $personnel_max= $pagination->pageMax($personnel_data,count($personnel_data));
            $names = $pagination->pagination($personnel_data,count($personnel_data),$count_personnel);

            //ページネーションオブジェクト設定
            $pagination_object = new Pagination();
            //ページネーションオブジェクトをセットする
            $pagination_object->set_pagination($department_data, $count_department, $personnel_data, $count_personnel);
        
            //ツリーデータ取得
            $tree = new PtcmtrController();
            $tree_data = $tree->set_view_treedata();

            if(session('device') != 'mobile'){
                return view('pacm01.pacm01',compact('count_department','count_personnel','department_max','departments',
                'department_data','personnel_max','names','click_personnel_data',));
            }else{
                //詳細画面オブジェクトの設定
                $personnel_details_object = new PersonnelDetailsObject();
                $personnel_details_object->setPersonnelObject($click_personnel_data,$responsible_lists,$click_management_lists,$operation_date);
                $click_data = $click_department_data;
                return view('pacm01.pacm02',compact('department_high', 'personnel_details_object', 'click_data', 'pagination_object', 
                'responsible_lists', 'select_id','click_management_lists', 'all_personnel_data'));
            }
        }
    }

    /**
     * 部署トップのページネーションのページ数移動
     * 
     * @param  \Illuminate\Http\Request  $request
     * 
     * @var  int $client_id 顧客ID　9/27現在　ダミーデータ(route indexでセッション保存)
     * @var　int $count_department 部署ページネーションのページ数
     * @var　int $count_personnel  人員ページネーションのページ数
     * @var  App\Libraries\php\DepartmentDataBase $department_db
     * @var  App\Libraries\php\PersonnelDataBase $personnel_db
     * @var  App\Libraries\php\Domain\ProjectionDataBase $projection_db
     * @var  array $department_data 部署データ
     * @var  array $personnel_data 人員データ
     * @var  array $department_details 一覧部署データ
     * @var  array $personnel_details 一覧人員データ
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

        //部署データの取得
        try{
            $department_data = $department_db->getList($client_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
        }
        //人員データの取得
        try{
            $personnel_data = $personnel_db->getList($client_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
            DatabaseException::common($e);
        }

        //基本ページネーション設定
        $pagination = new Pagination();
        $department_max = $pagination->pageMax($department_data,count($department_data));
        $department_details = $pagination->pagination($department_data,count($department_data),$count_department);
        $personnel_max= $pagination->pageMax($personnel_data,count($personnel_data));
        $personnel_details = $pagination->pagination($personnel_data,count($personnel_data),$count_personnel);

        //ツリーデータ取得
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        if(session('device') != 'mobile'){
            return view('pvbs01.pvbs01',compact('department_max','department_details','personnel_max','personnel_details',
            'department_data','count_department','count_personnel','personnel_data'));
        }else{
            return view('pvbs01.pvbs02',compact('top_department','top_responsible','department_max','departments','personnel_max','names',
            'department_data','responsible_lists','department_high','personnel_high','top_management','count_department','count_personnel','personnel_data'));
        }
    }

    /**
     * 部署データ検索した後のページネーション
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @var  string  $client_id 顧客ID
     * @var  string  $select_id  選択ID
     * @var  string  $click_id 選択したID
     * @var  int $count_department 部署ページネーションのページ数
     * @var  int $count_personnel 人員ページネーションのページ数
     * @var  App\Libraries\php\Domain\PersonnelDataBase personnel_db
     * @var  App\Libraries\php\Domain\DepartmentDataBase $department_db
     * @var  App\Libraries\php\Domain\ProjectionDataBase $projection_db
     * @var  array $department_data 部署データ
     * @var  array $personnel_data 人員データ
     * @var  array $system_management_lists システム管理者リスト
     * @var  int $department_max 部署データページネーションの最大値
     * @var  array $departments ページネーション後の部署データ
     * @var  int $personnel_max 人員データページネーションの最大値
     * @var  array $names ページネーション後の人員データ
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

        $department_db = new DepartmentDataBase();
        $personnel_db = new PersonnelDataBase();
        $projection_db = new ProjectionDataBase();

        $click_id = $select_id;
        View::share('click_id', $click_id);

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
            //部署詳細画面のロジック

            //一覧の部署データの検索取得
            try{
                $department_data = $department_db->search($client_id,$select_id,$request->search);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('pa0001.errormsg');
            }

            //一覧の投影部署データの取得
            try{
                $projection_department = $projection_db->getDepartmentSearch($client_id,$select_id,$request->search);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('pa0001.errormsg');
            }

            //投影データを一覧部署に追加
            $department_data = array_merge($department_data,$projection_department);

            //一覧の人員データの取得
            try{
                $personnel_data = $personnel_db->getSelectList($client_id,$select_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('pa0001.errormsg');
            }

            //一覧の投影人員データの取得
            try{
                $projection_personnel = $projection_db->getPersonnelList($client_id,$select_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('pa0001.errormsg');
            }

            //投影データを一覧人員に追加
            $personnel_data = array_merge($personnel_data,$projection_personnel);

            //システム管理者のリストを取得
            try{
                $system_management_lists = $personnel_db->getSystemManagement($client_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('pa0001.errormsg');
            }

            if(empty($department_data)){
                //ログ処理
                OutputLog::message_log(__FUNCTION__, 'mhcmwn0001');
                $message = Message::get_message_handle('mhcmwn0001',[0=>'']);
                session(['message'=>$message[0],'handle_message'=>$message[3]]);
                return redirect()->route('plbs01.show',[$client_id,$select_id]);
            }

            //部署詳細データの取得
            try{
                $click_department_data = $department_db->get($client_id,$select_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('pa0001.errormsg');
            }

            //基本ページネーション設定
            $pagination = new Pagination();
            $department_max = $pagination->pageMax($department_data,count($department_data));
            $departments = $pagination->pagination($department_data,count($department_data),$count_department);
            $personnel_max= $pagination->pageMax($personnel_data,count($personnel_data));
            $names = $pagination->pagination($personnel_data,count($personnel_data),$count_personnel);

            //ページネーションオブジェクト設定
            $pagination_object = new Pagination();
            //ページネーションオブジェクトをセットする
            $pagination_object->set_pagination($department_data, $count_department, $personnel_data, $count_personnel);

            //ツリーデータの取得
            $tree = new PtcmtrController();
            $tree_data = $tree->set_view_treedata();

            //クリックコードの保存
            session(['click_code'=>'bs']);

            //運用状況の確認
            $operation_check = new OperationCheck();
            $operation_check->check($click_department_data,$select_code);

            //詳細画面オブジェクトの設定
            $department_details_object = new DepartmentDetailsObject();
            $department_details_object->setDepartmentObject($click_department_data);

            if(session('device') != 'mobile'){
                return view('pacm01.pacm01',compact('department_max','departments','personnel_max','names','system_management_lists',
                'count_department','department_data','count_personnel','personnel_data','click_department_data','select_id'));
            }
        
        }else{
            //人員詳細画面のロジック
            //一覧の部署データの検索取得
            try{
                $department_data = $department_db->searchDetailPersonnel($client_id,$select_id,$request->search);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('pa0001.errormsg');
            }

            //一覧の投影部署データの取得
            try{
                $projection_department = $projection_db->getDepartmentList($client_id,$select_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('pa0001.errormsg');
            }

            //投影データを一覧部署に追加
            $department_data = array_merge($department_data,$projection_department);

            //一覧の人員データの取得
            try{
                $personnel_data = $personnel_db->getSelectList($client_id,$select_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('pa0001.errormsg');
            }

            //詳細画面人員のデータを取得
            try{
                $personnel_db = new PersonnelDataBase();
                $click_personnel_data = $personnel_db->get($client_id,$select_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
            }

            //システム管理者のリストを取得
            try{
                $system_management_lists = $personnel_db->getSystemManagement($client_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('pa0001.errormsg');
            }

            if(empty($department_data)){
                //ログ処理
                OutputLog::message_log(__FUNCTION__, 'mhcmwn0001');
                $message = Message::get_message_handle('mhcmwn0001',[0=>'']);
                session(['message'=>$message[0],'handle_message'=>$message[3]]);
                return redirect()->route('plbs01.show',[$client_id,$select_id]);
            }
    
            //基本ページネーション設定
            $pagination = new Pagination();
            $department_max = $pagination->pageMax($department_data,count($department_data));
            $departments = $pagination->pagination($department_data,count($department_data),$count_department);
            $personnel_max = $pagination->pageMax($personnel_data,count($personnel_data));
            $names = $pagination->pagination($personnel_data,count($personnel_data),$count_personnel);

            $tree = new PtcmtrController();
            $tree_data = $tree->set_view_treedata();

            //ページネーションオブジェクト設定
            $pagination_object = new Pagination();
            $pagination_object->set_pagination($department_data, $count_department, $personnel_data, $count_personnel);

            //詳細画面オブジェクトの設定
            $personnel_details_object = new PersonnelDetailsObject();
            $personnel_details_object->setPersonnelObject($click_personnel_data);

            if(session('device') != 'mobile'){
                return view('pacm01.pacm01',compact('count_department','department_data','personnel_data','select_id','department_max','departments','personnel_max',
                'names','count_personnel','click_personnel_data','system_management_lists'));
            }else{
                $click_data = $click_department_data;
                return view('pacm01.pacm02',compact('department_high', 'click_data', 'pagination_object', 'department_details_object',
                'responsible_lists', 'personnel_high', 'personnel_high', 'click_management_lists', 'select_id', 'personnel_data', 'all_personnel_data'));
            }
        }
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
     * @var  string $click_id 選択ID
     * @var  App\Libraries\php\Domain\PersonnelDataBase personnel_db
     * @var  App\Libraries\php\Domain\DepartmentDataBase $department_db
     * @var  App\Libraries\php\Domain\ProjectionDataBase $projection_db
     * @var  array $department_data 部署データ
     * @var  array $personnel_data 人員データ
     * @var  App\Libraries\php\Service\Pagination $pagination
     * @var  int $department_max 部署データページネーションの最大値
     * @var  array $departments ページネーション後の部署データ
     * @var  int $personnel_max 人員データページネーションの最大値
     * @var  array $names ページネーション後の人員データ
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

        $department_db = new DepartmentDataBase();
        $personnel_db = new PersonnelDataBase();
        $projection_db = new ProjectionDataBase();

        //一覧の部署データの検索取得
        try{
            $department_data = $department_db->getSelectList($client_id,$select_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }

        //一覧の投影部署データの取得
        try{
            $projection_department = $projection_db->getDepartmentList($client_id,$select_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }

        //投影データを一覧部署に追加
        $department_data = array_merge($department_data,$projection_department);

        //一覧の人員データの取得
        try{
            $personnel_data = $personnel_db->search($client_id,$select_id,$_GET['search']);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }

        //システム管理者のリストを取得
        try{
            $system_management_lists = $personnel_db->getSystemManagement($client_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }

        //検索結果が0件なら戻る
        if(empty($personnel_data)){
            OutputLog::message_log(__FUNCTION__, 'mhcmwn0001');
            $message = Message::get_message_handle('mhcmwn0001',[0=>'']);
            session(['message'=>$message[0],'handle_message'=>$message[3]]);
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
        
        //詳細画面部署のデータを取得
        try{
            $department_db = new DepartmentDataBase();
            $click_department_data = $department_db->get($client_id,$select_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
        }
        
        //基本ページネーション設定
        $pagination = new Pagination();
        $department_max = $pagination->pageMax($department_data,count($department_data));
        $departments = $pagination->pagination($department_data,count($department_data),$count_department);
        $personnel_max = $pagination->pageMax($personnel_data,count($personnel_data));
        $names = $pagination->pagination($personnel_data,count($personnel_data),$count_personnel);

        //ツリーデータの取得
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        //部署詳細オブジェクトの設定
        $department_details_object = new DepartmentDetailsObject();
        $department_details_object->setDepartmentObject($click_department_data);

        //ページネーションオブジェクト設定
        $pagination_object = new Pagination();
        $pagination_object->set_pagination($department_data, $count_department, $personnel_data, $count_personnel);

        if(session('device') != 'mobile'){
            return view('pacm01.pacm01',compact('count_department','count_personnel','click_department_data','department_data','personnel_data','select_id','department_max',
            'departments','personnel_max','names','system_management_lists'));
        }else{
            $click_data = $click_department_data;
            return view('pacm01.pacm02',compact('department_high', 'click_data', 'pagination_object', 'department_details_object',
            'responsible_lists', 'personnel_high', 'click_management_lists', 'select_id', 'personnel_data', 'all_personnel_data'));
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

    //モバイル端末情報をセッションにセットする
    public function setMobile(){
        session(['device' => 'mobile']);

        return redirect()->route('index');
    }

    //モバイル端末情報をセッションにリセットする
    public function resetMobile(){
        session(['device' => 'pc']);

        return redirect()->route('index');
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
     * @var int $count_department 部署のページ番号
     * @var int $count_personnel　人員のページ番号
     * @var App\Libraries\php\Domain\DepartmentDataBase $department_db
     * @var App\Libraries\php\Domain\PersonnelDataBase personnel_db
     * @var array $department_data 部署データ
     * @var array $personnel_data 人員データ
     * @var  App\Libraries\php\Service\Pagination $pagination
     * @var  int $department_max 部署データページネーションの最大値
     * @var  array $department_details 一覧に表示する部署データ
     * @var  int $personnel_max 人員データページネーションの最大値
     * @var  array $personnel_details 一覧に表示する人員データ
     * @var  App\Http\Controllers\PtcmtrController $tree
     * @var  array $tree_data ツリーデータ
     * 
     * @return \Illuminate\Http\Response
     */
    public function top(){

        //$client_idの数値はダミー(ログイン時にセッション保存する予定)
        $client_id = "aa00000001";

        //ログイン機能が完成次第、そちらで取得可能なため、このセッション取得を削除する。
        session(['client_id'=>$client_id]);

        $count_department = Config::get('startcount.count');
        $count_personnel = Config::get('startcount.count');

        //一覧の部署データの取得
        try{
            $department_db = new DepartmentDataBase();
            $department_data = $department_db->getList($client_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }

        //一覧の人員データの取得
        try{
            $personnel_db = new PersonnelDataBase();
            $personnel_data = $personnel_db->getList($client_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }      

        //基本ページネーション設定
        $pagination = new Pagination();
        $department_max = $pagination->pageMax($department_data,count($department_data));
        $department_details = $pagination->pagination($department_data,count($department_data),$count_department);
        $personnel_max= $pagination->pageMax($personnel_data,count($personnel_data));
        $personnel_details = $pagination->pagination($personnel_data,count($personnel_data),$count_personnel);

        //ツリーデータの取得
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        if(session('device') != 'mobile'){
            return view('pvbs01.pvbs01',compact('department_max','department_details','personnel_max','personnel_details',
            'count_department','count_personnel','department_data','personnel_data'));
        }else{
            return view('pvbs01.pvbs02',compact('department_max','department_details','personnel_max','personnel_details',
            'count_department','count_personnel','department_data','personnel_data'));
        }
    }

}
