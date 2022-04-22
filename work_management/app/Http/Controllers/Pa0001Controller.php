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
use App\Libraries\php\Service\NumberCheck;
use App\Libraries\php\Service\OperationCheck;
use App\Libraries\php\Service\Pagination;
use App\Libraries\php\Service\PaginationObject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;

use App\Libraries\php\Service\DepartmentDetailsObject;
use App\Libraries\php\Service\PersonnelDetailsObject;

use App\Libraries\php\Service\Display\List\DepartmentDisplayList;
use App\Libraries\php\Service\Display\List\PersonnelDisplayList;



/**
 * 導入画面のコントローラー
 */

class Pa0001Controller extends Controller
{
    /**
     * ディスプレイ表示
     *
     * @var  string  $client_id 顧客ID　9/27現在　ダミーデータ
     * @var  string  $select_id 選択ID　
     * @var　int $count_department 部署ページネーションのページ数
     * @var　int $count_personnel  人員ページネーションのページ数
     * @var  App\Libraries\php\Domain\DepartmentDataBase $department_db
     * @var  App\Libraries\php\Domain\PersonnelDataBase $personnel_db
     * @var  array $click_department_data 詳細部署データ
     * @var  array $personnel_data 人員データ
     * @var  array $system_management_lists システム管理者リスト
     * @var  App\Libraries\php\Service\Display\List\DepartmentDisplayList $department_display_list
     * @var  $departments 一覧部署データ
     * @var  App\Libraries\php\Service\Display\List\PersonnelDisplayList $personnel_display_list
     * @var  $names 一覧人員データ
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

        if(isset($_GET['department_page'])){
            $count_department = $_GET['department_page'];
            $count_personnel = $_GET['personnel_page'];
        }else{
            $count_department = Config::get('startcount.count');
            $count_personnel = Config::get('startcount.count');
        }

        //ログイン機能が完成次第、そちらで取得可能な為、このセッション取得を削除する。
        session(['client_id'=>$client_id]);

        $select_code = substr($select_id,0,2);

        $click_id = $select_id;
        View::share('click_id', $click_id);

        $department_db = new DepartmentDataBase();
        $personnel_db = new PersonnelDataBase();

        //詳細画面のデータ取得
        try{
            $click_department_data = $department_db->get($client_id,$select_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }

        //所属人員データの取得
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

        //一覧画面のデータ取得
        try{
            $department_display_list = new DepartmentDisplayList();
            $departments = $department_display_list->display($client_id,$select_id,$count_department);
            $personnel_display_list = new PersonnelDisplayList();
            $names = $personnel_display_list->display($client_id,$select_id,$count_personnel);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }

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

        //ページネーションオブジェクト設定
        $pagination_object = new PaginationObject();
        $pagination_object->set_pagination($departments, $count_department, $names, $count_personnel);

        if(session('device') != 'mobile'){
            return view('pacm01.pacm01',compact('personnel_data','system_management_lists','click_department_data','select_id',
            'count_department','count_personnel','departments','names'));
        }else{
            //@var string クリックしたid
            $click_id = NULL;
            //@var DepartmentDetailsObject 部署の詳細表示オブジェクト
            $click_data = $department_details_object;
            //@var string 選択した表示する詳細画面のid
            $select_id = "bs00000001";
            return view('pacm01.pacm02',compact('click_data','pagination_object', 'department_details_object',
            'system_management_lists', 'click_id', 'select_id', 'personnel_data'));
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
    
    /*
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
     * @var App\Libraries\php\Service\Display\List\DepartmentDisplayList $department_display_list
     * @var App\Libraries\php\Service\Display\List\PersonnelDisplayList $personnel_display_list
     * @var array $department_details 一覧に表示する部署データ
     * @var array $personnel_details 一覧に表示する人員データ
     * @var App\Http\Controllers\PtcmtrController $tree
     * @var array $tree_data ツリーデータ
     * 
     * @return \Illuminate\Http\Response
     */
    public function top(){

        //$client_idの数値はダミー(ログイン時にセッション保存する予定)
        $client_id = "aa00000001";
        $select_id = "bs00000000";

        //ログイン機能が完成次第、そちらで取得可能なため、このセッション取得を削除する。
        session(['client_id'=>$client_id]);

        if(isset($_GET['department_page'])){
            $count_department = $_GET['department_page'];
            $count_personnel = $_GET['personnel_page'];
        }else{
            $count_department = Config::get('startcount.count');
            $count_personnel = Config::get('startcount.count');
        }
        
        //一覧画面のデータ取得
        try{
            $department_display_list = new DepartmentDisplayList();
            $department_details = $department_display_list->display($client_id,$select_id,$count_department);
            $personnel_display_list = new PersonnelDisplayList();
            $personnel_details = $personnel_display_list->display($client_id,$select_id,$count_personnel);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }

        //ツリーデータの取得
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();
        
        if(session('device') != 'mobile'){
            return view('pvbs01.pvbs01',compact('department_details','personnel_details',
            'count_department','count_personnel'));
        }else{
            return view('pvbs01.pvbs02',compact('department_max','department_details','personnel_max','personnel_details',
            'count_department','count_personnel','department_data','personnel_data'));
        }
    }

}
