<?php

namespace App\Http\Controllers;

use App\Models\Date;
use App\Facades\OutputLog;
use App\Http\Controllers\PtcmtrController;
use App\Http\Requests\DepartmentRequest;
use App\Libraries\php\Domain\DepartmentDataBase;
use App\Libraries\php\Domain\PersonnelDataBase;
use App\Libraries\php\Domain\ProjectionDataBase;
use App\Libraries\php\Domain\Hierarchical;
use App\Libraries\php\Logic\JudgmentHierarchy;
use App\Libraries\php\Logic\ResponsiblePerson;
use App\Libraries\php\Service\DatabaseException;
use App\Libraries\php\Service\Message;
use App\Libraries\php\Service\OperationCheck;
use App\Libraries\php\Service\Pagination;
use App\Libraries\php\Service\ZeroPadding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

use App\Libraries\php\Service\DepartmentDetailsObject;
use App\Libraries\php\Service\PersonnelDetailsObject;

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
        if(session('device') != 'mobile'){
            return view('psbs01.psbs01');
        }else{
            return view('psbs01.psbs02');
        }
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
     * @var  string $client_id 顧客ID
     * @var  string $responsible_person_id 責任者ID
     * @var  string $name 部署名
     * @var  string $status 状態
     * @var  string $management_personnel_id 管理者ID
     * @var  string $high 上位部署のID番号
     * @var  string $id 顧客IDに対応した最新の部署IDを格納する因数
     * @var  App\Libraries\php\Service\ZeroPadding $padding
     * @var  string $operation_start_date 稼働開始日
     * @var  int $department_id 部署ID
     * 
     * @return \Illuminate\Http\Response
     */
    public function store(DepartmentRequest $request)
    {
        //リクエストの取得
        $client_id = $request->client_id;
        $responsible_person_id = $request->responsible_person_id;
        $name = $request->name;
        $status = $request->status;
        $management_personnel_id = $request->management_number;
        $high = $request->high;
        $date = new Date();
        $operation_start_date = $date->today();
        $remarks = $request->remarks;

        // 重複クリック対策
        $request->session()->regenerateToken();

        //顧客IDに対応した最新の部署IDを取得
        try{
            $department_db = new DepartmentDataBase();
            $id = $department_db->getId($client_id);
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

        //データベースに部署情報を登録
        try{
            $department_db = new DepartmentDataBase();
            $department_db->insert($client_id,$department_id,$responsible_person_id,
            $name,$status,$management_personnel_id,$operation_start_date,$remarks);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
            DatabaseException::common($e);
            return redirect()->route('index');
        }

        //データベースに階層情報を登録
        try{
            $hierarchical = new Hierarchical();
            $hierarchical->insert($client_id,$department_id,$high);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('index');
        }
        OutputLog::message_log(__FUNCTION__, 'mhcmok0001');
        //メッセージの表示
        $request->session()->put('message',Config::get('message.mhcmok0001'));
        PtcmtrController::open_node($department_id);
        return redirect()->route('plbs01.show',[$client_id,$high]);
        
    }

    /**
     * 配下部署の表示
     *
     * @param  string $client 顧客ID
     * @param  string $select_id 選択した部署ID
     * 
     * @var　int $count_department 部署ページネーションのページ数
     * @var　int $count_personnel  人員ページネーションのページ数
     * @var  App\Libraries\php\Domain\DepartmentDataBase $department_db
     * @var  App\Libraries\php\Domain\PersonnelDataBase $personnel_db
     * @var  App\Libraries\php\Domain\ProjectionDataBase $projection_db
     * @var  string $select_code 選択したIDのコード
     * @var  array $projection_code 投影元のデータコード
     * @var  array $department_data 部署データ
     * @var  array $projection_department 投影された部署データ
     * @var  array $personnel_data 人員データ
     * @var  array $projection_personnel 投影された人員データ
     * @var  array $system_management_lists システム管理者リスト
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
    public function show($client_id,$select_id)
    {
        session(['page_id'=>$select_id]);

        //ログイン機能が完成次第、そちらで取得可能なため、このセッション取得を削除する。
        session(['client_id'=>$client_id]);

        if($select_id == "bs"){
            return redirect()->route('pa0001.top');
        }
        
        $count_department = Config::get('startcount.count');
        $count_personnel = Config::get('startcount.count');

        $department_db = new DepartmentDataBase();
        $personnel_db = new PersonnelDataBase();
        $projection_db = new ProjectionDataBase();

        $select_code = substr($select_id,0,2);
        $click_id = $select_id;
        View::share('click_id', $click_id);

        if($select_code == "ta"){
            //選択部署が投影だった場合は対応するIDを取得 
            $projection_code = $projection_db->getId($select_id);
            $select_id = $projection_code[0]->projection_source_id;
            $select_code = substr($projection_code[0]->projection_source_id,0,2);
        }
        
        //部署詳細画面のロジック
        if($select_code == "bs"){
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

            //ツリーデータの取得
            $tree = new PtcmtrController();
            $tree_data = $tree->set_view_treedata();

            session(['click_code'=>$select_code]);

            //運用状況の確認
            $operation_check = new OperationCheck();
            $operation_check->check($click_department_data,$select_code);

            //部署詳細オブジェクトの設定
            $department_details_object = new DepartmentDetailsObject();
            $department_details_object->setDepartmentObject($click_department_data);

            //ページネーションオブジェクト設定
            $pagination_object = new Pagination();
            $pagination_object->set_pagination($department_data, $count_department, $personnel_data, $count_personnel);

            if(session('device') != 'mobile'){
                return view('pacm01.pacm01',compact('click_department_data','count_department','count_personnel','department_max','system_management_lists',
                'departments','personnel_max','names','department_data','client_id','select_id','personnel_data'));
            }else{
                $click_data = $click_department_data;
                return view('pacm01.pacm02',compact('click_data', 'pagination_object', 'department_details_object',
                'responsible_lists', 'personnel_high', 'department_high', 'click_management_lists', 'select_id', 'personnel_data', 'all_personnel_data'));
            }
        }else{
            //人員詳細画面のロジック

            //詳細画面人員のデータを取得
            try{
                $personnel_db = new PersonnelDataBase();
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

            $tree = new PtcmtrController();
            $tree_data = $tree->set_view_treedata();

            session(['click_code'=>$select_code]);

            //運用状況の確認
            $operation_check = new OperationCheck();
            $operation_check->check($click_personnel_data,$select_code);

            //詳細画面オブジェクトの設定
            $personnel_details_object = new PersonnelDetailsObject();
            $personnel_details_object->setPersonnelObject($click_personnel_data);

            //ページネーションオブジェクト設定
            $pagination_object = new Pagination();
            $pagination_object->set_pagination($department_data, $count_department, $personnel_data, $count_personnel);

            if(session('device') != 'mobile'){
                return view('pacm01.pacm01',compact('count_department','count_personnel','department_max','departments','personnel_max','names',
                'client_id','select_id','click_personnel_data','department_data','system_management_lists'));
            }else{
                $click_data = $click_department_data;
                return view('pacm01.pacm02',compact('personnel_details_object', 'click_data', 'pagination_object', 
                'responsible_lists', 'select_id','click_management_lists', 'all_personnel_data', 'department_high'));
            }
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
     * @param  \Illuminate\Http\DepartmentRequest  $request
     * 
     * @var  string  $client_id　顧客ID
     * @var  string  $department_id　部署ID
     * @var  string  $name　名前
     * @var  string  $management_number 管理者人員番号
     * @var  string  $responsible_person_id 責任者番号
     * @var  array   $management_personnel_id　管理者ID
     * @var  string  $status　状態
     * @var  string  $start_day 稼働開始日
     * @var  string  $finish_day 稼働終了日
     * @var  string  $remarks 備考
     * @var  App\Libraries\php\Domain\PersonnelDataBase $personnel_db
     * @var  string  $message メッセージ
     * 
     * @return \Illuminate\Http\Response
     */
    public function update(DepartmentRequest $request)
    {
        //リクエストの取得
        $client_id = $request->client_id;
        $department_id = $request->department_id;
        $name = $request->name;
        $management_number = $request->management_number;
        $responsible_person_id = $request->responsible_person_id;
        $status = $request->status;
        $start_day = $request->start_day;
        $finish_day = $request->finish_day;
        $remarks = $request->remarks;

        // 重複クリック対策
        $request->session()->regenerateToken();
        
        //入力された番号の人員が存在するかの確認
        try{
            $personnel_db = new PersonnelDataBase();
            $management_personnel_id = $personnel_db->getData($client_id,$management_number);
        }catch(\Exception $e){
                //エラー処理
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
        }
        
        if($management_personnel_id == null){

            return redirect()->route('index');
        }

        //日付チェック
        if($start_day>$finish_day){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0008','01');
            $message = Message::get_message_handle('mhcmer0008',[0=>'']);
            session(['message'=>$message[0],'handle_message'=>$message[3]]);
            return back();
        }

        //部署情報の更新
        try{
            $department_db = new DepartmentDataBase();
            $department_db->update($responsible_person_id,$name,$status,$management_number,$start_day,$finish_day,$remarks,$client_id,$department_id);
        }catch(\Exception $e){
            //エラー処理
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
            DatabaseException::common($e);
            return redirect()->route('index');
        }
            //ログ処理
            OutputLog::message_log(__FUNCTION__, 'mhcmok0002');
            $message = Message::get_message('mhcmok0002',[0=>'']);
            session(['message'=>$message[0]]);

        return back();
    }

    /** 部署・人員の移動
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param string $id 送信されたID
     * 
     * @var string $client_id 顧客ID
     * @var string $high_id 上位ID
     * @var string $lower_id 下位ID
     * @var string $message メッセージ
     * @var \App\Libraries\php\Logic\JudgmentHierarchy $judgment_hierarchy
     * 
     * @return \Illuminate\Http\Response
     */
    public function hierarchyUpdate(Request $request,$id)
    {
        //リクエストの取得
        $client_id = $id;
        $high_id = $request->high_id;
        $lower_id = $request->lower_id;

        // 重複クリック対策
        $request->session()->regenerateToken();

        //複写番号が空白の場合エラーメッセージを表示
        if($request->lower_id == null){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0009','01');
            $message = Message::get_message_handle('mhcmer0009',[0=>'']);
            session(['message'=>$message[0],'handle_message'=>$message[3]]);
            return redirect()->route('index');
        }

        //無限ループの回避の判断
        try{
            $judgment_hierarchy = new JudgmentHierarchy();
            $move_flag = $judgment_hierarchy->judgmentHierarchy($client_id,$high_id,$lower_id);
        }catch(\Exception $e){
            //エラー及びログ処理
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('index');
        }
        if($move_flag == false){
            //ログ処理
            OutputLog::message_log(__FUNCTION__, 'mhcmer0011','01');
            $message = Message::get_message_handle('mhcmer0011',[0=>'']);
            session(['message'=>$message[0],'handle_message'=>$message[3]]);
            return back();
        }
        
        //データベース更新
        try{
            $hierarchical = new Hierarchical();
            $hierarchical->update($high_id,$client_id,$lower_id);
        }catch(\Exception $e){
        //エラー及びログ処理
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('index');
        }
        //移動処理終了後に、クリップボードの削除
        session()->forget('clipboard_id');
        //ログ処理
        OutputLog::message_log(__FUNCTION__, 'mhcmok0008');
        $message = Message::get_message('mhcmok0008',[0=>'']);
        session(['message'=>$message[0]]);
        return back();
    }

    /**
     * 人員及び部署データの削除
     *
     * @param  string  $client 顧客ID
     * @param  string  $delete 削除予定のID
     * 
     * @var  array $lists 削除予定のIDを格納した配列
     * @var  array $delete_id 選択部署の配下の削除予定のID
     * @var  App\Libraries\php\Domain\DepartmentDataBase $department_db
     * @var  \App\Libraries\php\Domain\Hierarchical $hierarchical
     * @var  array $delete_lists 削除予定のIDを格納した配列
     * @var  int   $code 機能コードの頭2文字
     * @var  array $delete_projections 削除元
     * @var  App\Libraries\php\Domain\PersonnelDataBase $personnel_db
     * @var  App\Libraries\php\Domain\ProjectionDataBase $projection_db
     * @var  string $message メッセージ
     * 
     * @return \Illuminate\Http\Response
     */
    public function delete($client,$delete)
    {
        //選択した部署のIDをarray型に格納
        $lists = [];
        $delete_id = [];
        array_push($lists,$delete);

        //選択した部署の部署情報を取得
        try{
            $department_db = new DepartmentDataBase();
            $delete_data = $department_db->get($client,$delete);
        }catch(\Exception $e){

            OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
            DatabaseException::common($e);
            return redirect()->route('index');
        }

        ////選択した部署が最上位部署かの確認
        if(empty($delete_data)){
            $department_db = new DepartmentDataBase();
            $delete_data = $department_db->getClickTop($client,$delete);
            $delete_data[0]->high_id = "bs";
        }

        //選択した部署の配下を取得
        $hierarchical = new Hierarchical();
        $delete_lists = $hierarchical->subordinateSearchRoop($lists,$client,$delete_id);

        //削除リストの作成
        array_unshift($delete_lists,$delete);

        //選択したデータ及び配下データを削除
        if(!empty($delete_lists)){
            try{
                //トランザクション
                DB::beginTransaction();

                foreach($delete_lists as $delete_list){
                    //機能コードの判定
                    $code = substr($delete_list,0,2);

                    //対応したデータの削除
                    if ($code == "bs"){
                        $department_db = new DepartmentDataBase();
                        $department_db->delete($client,$delete_list);

                        //削除予定の配下部署が元になった投影を削除
                        $projection_db = new ProjectionDataBase();
                        $delete_projections = $projection_db->getProjectionId($client,$delete_list);
                        foreach($delete_projections as $delete_projection){
                            $hierarchical = new Hierarchical();
                            $hierarchical->delete($client,$delete_projection->projection_id);
                            $projection_db = new ProjectionDataBase();
                            $projection_db->delete($client,$delete_projection->projection_id);
                        }
                        $projection_db = new ProjectionDataBase();
                        $projection_db->delete($client,$delete_list);

                    }elseif($code == "ji"){
                        //人員データの削除
                        $personnel_db = new PersonnelDataBase();
                        $personnel_db->delete($client,$delete_list);
                    }elseif($code == "ta"){
                        //投影の削除
                        $projection_db = new ProjectionDataBase();
                        $projection_db->delete($client,$delete_list);
                    }else{

                    }
                    //データの階層構造を削除
                    $hierarchical = new Hierarchical();
                    $hierarchical->delete($client,$delete_list);
                }

                DB::commit();

            }catch(\Exception $e){
                //ロールバック
                DB::rollBack();

                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('index');
            }
        }
        //ログ処理
        OutputLog::message_log(__FUNCTION__, 'mhcmok0003');
        $message = Message::get_message('mhcmok0003',[0=>'']);
        session(['message'=>$message[0]]);

        PtcmtrController::delete_node($delete_data[0]->high_id);
        
        if(!isset($delete_data[0]->high_id)){
            return redirect()->route('pa0001.top');
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
     * @var  App\Libraries\php\Domain\DepartmentDataBase $department_db
     * @var  App\Libraries\php\Domain\PersonnelDataBase $personnel_db
     * @var  int $count_department 部署ページネーションのページ数
     * @var  int $count_personnel 人員ページネーションのページ数
     * @var  array $department_data 部署データ
     * @var  array $personnel_data 人員データ
     * @var  array $system_management_lists システム管理者リスト
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
    public function search(Request $request,$id,$id2)
    {
        $client_id = $id;
        $select_id = $id2;

        $count_department = Config::get('startcount.count');
        $count_personnel = Config::get('startcount.count');

        $department_db = new DepartmentDataBase();
        $personnel_db = new PersonnelDataBase();
        $projection_db = new ProjectionDataBase();

        $click_id = $select_id;
        View::share('click_id', $click_id);

        //詳細画面のデータ表示
        $select_code = substr($select_id,0,2);

        if($select_code == "ta"){
            //選択部署がtaだった場合は対応するIDを取得
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
     * 
     * 複製したデータを挿入するメソッド
     * @param  \Illuminate\Http\Request  $request
     * 
     * @var string $client_id 顧客ID
     * @var string $copy_id 複製するID
     * @var string $high 複製IDが所属する上位階層ID
     * @var App\Libraries\php\Domain\ProjectionDataBase $projection_db
     * @var string $id 複製前の最新の部署ID
     * @var string $id2 複製前の最新の人員ID
     * @var string $id_num 複製前の最新の部署IDの数字部分
     * @var string $id2_num 複製前の最新の部署IDの数字部分
     * @var string $number 8桁に0埋めした複製前の最新の部署IDの数字部分
     * @var string $number2　8桁に0埋めした複製前の最新の人員IDの数字部分
     * @var App\Libraries\php\Domain\Hierarchical $hierarchical
     * @var App\Libraries\php\Domain\DepartmentDataBase $department_db
     * @var App\Libraries\php\Domain\PersonnelDataBase $personnel_db
     * 
     * @return \Illuminate\Http\Response
     */
    public function copy(Request $request){

        $client_id = $request->client_id;
        $copy_id = $request->copy_id;
        $high = $request->high_id;

        // 重複クリック対策
        $request->session()->regenerateToken();

        if($request->copy_id == null){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0009','01');
            $message = Message::get_message_handle('mhcmer0009',[0=>'']);
            session(['message'=>$message[0],'handle_message'=>$message[3]]);
            return redirect()->route('index');
        }

        //保存先の部署及び人員の上位階層IDを取得し、そのIDが複製する部署IDと一致した場合は処理を中止

        //投影を複製する場合
        if(substr($copy_id,0,2) == "ta"){
            try{
                $projection_db = new ProjectionDataBase();
                $code = $projection_db->getId($copy_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
            }
            $projection_source_id = $code[0]->projection_source_id;
            //最新の投影番号を生成
            try{
                $projection_db = new ProjectionDataBase();
                $projection_id = $projection_db->getNewId($client_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
            }

            try{
                //トランザクション
                DB::beginTransaction();

                //データベースに投影情報を登録
                $projection_db = new ProjectionDataBase();
                $projection_db->insert($client_id,$projection_id,$projection_source_id);

                //データベースに階層情報を登録
                $hierarchical = new Hierarchical();
                $hierarchical->insert($client_id,$projection_id,$high);

                DB::commit();
            }catch(\Exception $e){
                //ロールバック
                DB::rollBack();
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
            }
            //ログ処理
            OutputLog::message_log(__FUNCTION__, 'mhcmok0009');
            $message = Message::get_message('mhcmok0009',[0=>'']);
            session(['message'=>$message[0]]);
            return back();
        }else{
            //複製動作
            //複製前の最新の部署番号を取得
            try{
                $department_db = new DepartmentDataBase();
                $id = $department_db->getId($client_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('index');
            }
            //複製前の最新の人員番号を取得
            try{
                $personnel_db = new PersonnelDataBase();
                $id2 = $personnel_db->getId($client_id);
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
}
