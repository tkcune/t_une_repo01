<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Libraries\php\DatabaseException;
use App\Libraries\php\Pagination;
use App\Libraries\php\Hierarchical;
use App\Libraries\php\StatusCheck;
use App\Libraries\php\OutputLog;
use App\Libraries\php\ZeroPadding;
use Illuminate\Support\Facades\Config;
use App\Libraries\php\Message;
use App\Libraries\php\ResponsiblePerson;
use App\Libraries\php\OperationCheck;
use App\Http\Controllers\PtcmtrController;
use App\Libraries\php\DepartmentDataBase;
use App\Libraries\php\PersonnelDataBase;
use App\Libraries\php\ProjectionDataBase;
use App\Models\Date;
use Illuminate\Support\Facades\View;
use App\Http\Requests\DepartmentRequest;

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
     * @var  string $client_id 顧客ID
     * @var  string $responsible_person_id 責任者ID
     * @var  string $name 部署名
     * @var  string $status 状態
     * @var  string $management_personnel_id 管理者ID
     * @var  string $high 上位部署のID番号
     * @var  string $id 顧客IDに対応した最新の部署IDを格納する因数
     * @var  App\Libraries\php\ZeroPadding $padding
     * @var  App\Libraries\php\StatusCheck $check
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
            $name,$status,$management_personnel_id,$operation_start_date);
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
    public function show($client,$select_id)
    {
        if($select_id == "bs"){
            return redirect()->route('pa0001.top');
        }
        if($select_id == "bs00000001"){
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
            $projection_db = new ProjectionDataBase();
            $projection_code = $projection_db->getId($select_id);
            $select_id = $projection_code[0]->projection_source_id;
            $select_code = substr($projection_code[0]->projection_source_id,0,2);
        }
        
        if($select_code == "bs"){
            //選択した部署のデータを取得
            try{
                $department_db = new DepartmentDataBase();
                $click_department_data = $department_db->get($client,$select_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
            }

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

            //全体の人員データの取得
            try{
                $personnel_db = new PersonnelDataBase();
                $all_personnel_data = $personnel_db->getAll($client);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
            }

            //日付を変換
            $date = new Date();
            $operation_date = $date->formatOperationDate($click_department_data);
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
                $click_department_high = $hierarchical->upperHierarchyName($click_department_data);
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

            session(['click_code'=>$select_code]);

            //運用状況の確認
            $operation_check = new OperationCheck();
            $operation_check->check($click_department_data);

            return view('pacm01.pacm01',compact('click_department_data','count_department','count_personnel',
            'department_max','departments','personnel_max','names','responsible_lists','department_high','personnel_high',
            'click_department_high','click_management_lists','client','select_id','personnel_data','operation_date','all_personnel_data'));
            
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
                $data = $department_db->getClickDepartmentData($client,$affiliation_data[0]->high_id);
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
                    $department_db = new DepartmentDataBase();
                    $department_data = $department_db->getAll($client);
                }catch(\Exception $e){
                    OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                    DatabaseException::common($e);
                }

                //全体の人員データの取得
                try{
                    $personnel_db = new PersonnelDataBase();
                    $all_personnel_data = getAll($client);
                }catch(\Exception $e){
                    OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                    DatabaseException::common($e);
                }

                $personnel_data = $all_personnel_data;

                //責任者を名前で取得
                $responsible = new ResponsiblePerson();
                $top_responsible = $responsible->getResponsibleLists($client,$top_department);

                //管理者を名前で取得
                $top_management = $responsible->getManagementLists($client,$top_department);
                $click_management_lists = $responsible->getManagementLists($client,$click_personnel_data);
        
                //日付フォーマットの変更
                $date = new Date();
                $operation_date = $date->formatOperationDate($click_personnel_data);
                $date->formatDate($top_department);
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

                //運用状況の確認
                $operation_check = new OperationCheck();
                $operation_check->check($top_department);

                //クリックコードの保存
                session(['click_code'=>$select_code]);

                return view('pacm01.pacm01',compact('top_management','click_management_lists','department_max','departments','personnel_max','names',
                'top_department','top_responsible','count_department','count_personnel','client','responsible_lists','department_high','personnel_high',
                'select_id','personnel_data','click_personnel_data','operation_date','all_personnel_data'));
            }

            array_push($department_data,$data[0]);

            array_push($lists,$affiliation_data[0]->high_id);

            //全体の人員データの取得
            try{
                $personnel_db = new PersonnelDataBase();
                $all_personnel_data = $personnel_db->getAll($client);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
            }

            //取得した部署IDを元に配下を取得
            $hierarchical = new Hierarchical();
            $select_lists = $hierarchical->subordinateSearch($lists,$client);

            //選択したデータ及び配下データを取得
            $lists = $hierarchical->subordinateGet($select_lists,$client);
            $department_data = $lists[0];
            $personnel_data = $lists[1];

            //日付フォーマットの変更
            $date = new Date();
            $operation_date = $date->formatOperationDate($click_personnel_data);
            
            //責任者を名前で取得
            $responsible = new ResponsiblePerson();
            if(isset($click_personnel_data)){
                $click_management_lists = $responsible->getManagementLists($client,$click_personnel_data);
            }

            //日付フォーマットを変更する
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

            session(['click_code'=>$select_code]);

            //運用状況の確認
            $operation_check = new OperationCheck();
            $operation_check->check($click_personnel_data);

            return view('pacm01.pacm01',compact('data','count_department','count_personnel','department_max','departments','personnel_max','names',
            'department_high','personnel_high','responsible_lists','client','select_id','click_personnel_data','click_management_lists',
            'personnel_data','operation_date','all_personnel_data'));
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
     * @var  \App\Libraries\php\StatusCheck $check
     * @var  string  $start_day 稼働開始日
     * @var  string  $finish_day 稼働終了日
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

        //複写番号が空白の場合エラーメッセージを表示
        if($request->lower_id == null){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0009','01');
            $message = Message::get_message('mhcmer0009',[0=>'']);
            session(['message'=>$message[0]]);
            return redirect()->route('index');
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
     * @var  array   $lists 削除予定のIDを格納した配列
     * @var  \App\Libraries\php\Hierarchical $hierarchical
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
        $delete_lists = $hierarchical->subordinateSearch($lists,$client);
         
        //選択したデータ及び配下データを削除
        if(!empty($delete_lists)){
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
                        DB::delete('delete from dcji01 where client_id = ? and personnel_id = ?',
                        [$client,$delete_list]);
                    }catch(\Exception $e){
                        OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                        DatabaseException::common($e);
                        return redirect()->route('index');
                    }

                }elseif($code == "ta"){
                    try{
                        DB::delete('delete from dccmta where client_id = ? and projection_id = ?',
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
    public function search(Request $request,$id,$id2)
    {
        $client_id = $id;
        $select_id = $id2;

        $count_department = Config::get('startcount.count');
        $count_personnel = Config::get('startcount.count');

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
            $department_data = $department_db->search($client_id,$request->search);
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
        'names','responsible_lists','department_high','personnel_high','count_personnel','operation_date','all_personnel_data'));
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
     * @var App\Libraries\php\Hierarchical $hierarchical
     * 
     * @return \Illuminate\Http\Response
     */
    public function copy(Request $request){

        $client_id = $request->client_id;
        $copy_id = $request->copy_id;
        $high = $request->high_id;

        if($request->copy_id == null){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0009','01');
            $message = Message::get_message('mhcmer0009',[0=>'']);
            session(['message'=>$message[0]]);
            return redirect()->route('index');
        }
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
                $id = $projection_db->getNewId($client_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
            }
            if(empty($id)){
                $projection_id = "ta00000001";
            }else{
                //登録する番号を作成
                $padding = new ZeroPadding();
                $projection_id = $padding->padding($id[0]->projection_id);
            }

            //データベースに投影情報を登録
            try{
                $projection_db = new ProjectionDataBase();
                $projection_db->insert($client_id,$projection_id,$projection_source_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
            }

            //データベースに階層情報を登録
            try{
                $hierarchical = new Hierarchical();
                $hierarchical->insert($client_id,$projection_id,$high);
            }catch(\Exception $e){
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
