<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Librarys\php\DatabaseException;
use App\Models\Date;
use App\Librarys\php\StatusCheck;
use App\Librarys\php\Pagination;
use App\Librarys\php\Hierarchical;
use App\Librarys\php\ResponsiblePerson;
use Illuminate\Support\Facades\Config;
use App\Librarys\php\OutputLog;
use App\Librarys\php\Message;
use App\Librarys\php\ZeroPadding;
use App\Http\Controllers\PtcmtrController;

/**
 * 人員データを操作するコントローラー
 */
class Psji01Controller extends Controller
{
    /**
     * 人員新規登録画面表示
     *
     * @param  App\Http\Controllers\PtcmtrController $tree
     * @param  array $tree_data ツリーデータ
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();
        return view('psji01.psji01');
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
     * 新規人員データ登録
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  string $client_id 顧客ID
     * @param  string $name 名前
     * @param　string $email メールアドレス
     * @param　string $password パスワード
     * @param　int $status 状態
     * @param  int $login_authority ログイン権限
     * @param  int $login_authority 管理者権限
     * @param  int $high 上位部署
     * @param  App\Models\Date $date
     * @param  App\Librarys\php\StatusCheck $check
     * @param  string $id　顧客IDに対応した最新の部署IDを格納する因数
     * @param  string $personnel_id 最新の人員ID
     * @param  App\Librarys\php\ZeroPadding $padding
     * @param  App\Librarys\php\StatusCheck $check
     * @param  string $operation_start_date　稼働開始日
     * @param  string $operation_end_date　稼働終了日
     * 
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //リクエストの取得
        $client_id = $request->client_id;
        $name = $request->name;
        $email = $request->email;
        $password = Hash::make($request->password);
        $status = $request->status;
        $login_authority = $request->login_authority;
        $system_management = $request->system_management;
        $high = $request->high;
        $date = new Date();
        $check = new StatusCheck();

        //顧客IDに対応した最新の人員IDを取得
        try{
            $id = DB::select('select personnel_id from dcji01 where client_id = ? 
            order by personnel_id desc limit 1',[$client_id]);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('index');
        }

        if(empty($id)){
            $personnel_id = "ji00000001";
        }else{

        //登録する番号を作成
        $padding = new ZeroPadding();
        $personnel_id = $padding->padding($id[0]->personnel_id);
        }

        list($operation_start_date,$operation_end_date) = $check->statusCheck($request->status);

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
            [
            $client_id,
            $personnel_id,
            $name,
            $email,
            $password,
            $date->today(),
            $status,
            $personnel_id,
            $login_authority,
            $system_management,
            $operation_start_date,
            $operation_end_date]);
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
            [$client_id,$personnel_id,$high]);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('index');
        }

        PtcmtrController::open_node($personnel_id);
        return redirect()->route('index');
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
     * 人員情報の更新
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $client_id　顧客ID
     * @param  string  $personnel_id　人員ID
     * @param  string  $name　名前
     * @param  string  $management_number 管理者ID
     * @param  string  $management_personnel_id 管理者番号
     * @param  string  $status　状態
     * @param  App\Librarys\php\StatusCheck $check
     * @param  string  $operation_start_date 稼働開始日
     * @param  string  $operation_end_date 稼働終了日
     * 
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $client_id = $request->client_id;
        $personnel_id = $request->personnel_id;
        $name = $request->name;
        $management_number = $request->management_number;
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
               DB::update('update dcji01 set name = ?,status = ?,management_personnel_id = ?,operation_start_date = ? where client_id = ? and personnel_id = ?',
               [$name,$status,$management_number,$operation_start_date,$client_id,$personnel_id]);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('index');
            }

        }else if($status == "18"){
            //状態が廃止なら稼働終了日を更新
            $check = new StatusCheck();
            list($operation_start_date,$operation_end_date) = $check->statusCheck($request->status);
       
            try{
                DB::update('update dcji01 set name = ?,status = ?,management_personnel_id = ?,operation_end_date = ? where client_id = ? and personnel_id = ?',
                [$name,$status,$management_number,$operation_end_date,$client_id,$personnel_id]);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('index');
            } 
        }else{
           //上記以外なら状態と名前のみ更新
            try{
                DB::update('update  dcji01 set name = ?,status = ?,management_personnel_id = ? where client_id = ? and personnel_id = ?',
                [$name,$status,$management_number,$client_id,$personnel_id]);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('index');
            }
        }

        //ログ処理
        OutputLog::message_log(__FUNCTION__, 'mhcmok0002');
        $message = Message::get_message('mhcmok0002',[0=>'']);
        session(['message'=>$message[0]]);

        //ツリー開閉
        PtcmtrController::open_node($personnel_id);
        return back();
    }

    /**
     * 人員データの削除
     *
     * @param  string  $id　顧客ID　
     * @param  string  $id2 人員ID
     * @param  string  $message ログメッセージ
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,$id2)
    {
        try{
            DB::delete('delete from dcji01 where client_id = ? and personnel_id = ?',[$id,$id2]);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('index');
        }
        //ログ処理
        OutputLog::message_log(__FUNCTION__, 'mhcmok0003');
        $message = Message::get_message('mhcmok0003',[0=>'']);
        session(['message'=>$message[0]]);
        PtcmtrController::delete_node($personnel_id);
        return redirect()->route('index');
    }

    /**
     * データ検索
     *
     * @param  @param  \Illuminate\Http\Request  $request
     * @param  string  $client_id　顧客ID
     * @param  int $count_department 部署ページネーションのページ数
     * @param  int $count_personnel 人員ページネーションのページ数
     * @param  array $department_data 部署データ
     * @param  array $personnel_data 人員データ
     * @param  App\Librarys\php\Pagination $pagination 
     * @param  int $department_max 部署データページネーションの最大値
     * @param  array $departments ページネーション後の部署データ
     * @param  int $personnel_max 人員データページネーションの最大値
     * @param  array $names ページネーション後の人員データ
     * @param  App\Librarys\php\ResponsiblePerson $responsible
     * @param  array $top_responsible 最上位の責任者データ
     * @param  array $responsible_lists 責任者リスト
     * @param  array $top_management 最上位の管理者データ
     * @param  array $management_lists 管理者データ
     * @param  App\Librarys\php\Hierarchical $hierarchical
     * @param  array $department_high 部署データの上位階層
     * @param  array $personnel_high 人員データの上位階層
     * @param  App\Http\Controllers\PtcmtrController $tree
     * @param  array $tree_data ツリーデータ
     * 
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request,$id)
    {
        $client_id = $id;
        $count_department = Config::get('startcount.count');
        $count_personnel = Config::get('startcount.count');

        //データベースの検索
        try{
            $department_data = DB::select('select * from dcbs01 inner join dccmks on dcbs01.department_id = dccmks.lower_id and dcbs01.client_id = ?',[$client_id]);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('index');
        }
        try{
            $personnel_data = DB::select('select 
            dcji01.client_id ,personnel_id,name,email,password,password_update_day,status,management_personnel_id,login_authority,system_management,operation_start_date,operation_end_date,dcji01.created_at, dcji01.updated_at ,high_id ,lower_id
            from dcji01 inner join dccmks on dcji01.personnel_id = dccmks.lower_id and dcji01.client_id = ?
            where dcji01.name like ?',[$client_id,'%'.$request->search.'%']);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('index');
        }
        //日付を６桁にする
        $date = new Date();
        $date->formatDate($department_data);
        $date->formatDate($personnel_data);

        //ページネーション
        $pagination = new Pagination();
        $department_max = $pagination->pageMax($department_data,count($department_data));
        $departments = $pagination->pagination($department_data,count($department_data),$count_department);
        $personnel_max= $pagination->pageMax($personnel_data,count($personnel_data));
        $names = $pagination->pagination($personnel_data,count($personnel_data),$count_personnel);

        //責任者を名前で取得
        $responsible = new ResponsiblePerson();
        $responsible_lists = $responsible->getResponsibleLists($client_id,$departments);

        //管理者を名前で取得
        $management_lists = $responsible->getManagementLists($client_id,$departments);

        //上位階層取得
        $hierarchical = new Hierarchical();
        $department_high = $hierarchical->upperHierarchyName($departments);
        $personnel_high = $hierarchical->upperHierarchyName($names);

        //ツリーデータの取得
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        return view('pacm01.pacm01',compact('management_lists','departments','names','count_department',
        'count_personnel','department_max','personnel_max','department_high','personnel_high','responsible_lists'));
    }

    /**
     * 
     * 複製したデータを挿入するメソッド
     * @param string $client_id 顧客ID
     * @param string $copy_id 複製するID
     * @param string $high 複製IDが所属する上位階層ID
     * @param array  $copy_personnel 複製するデータ
     * @param string $department_id 登録する部署ID
     * @param App\Librarys\php\ZeroPadding $padding
     * 
     * @return \Illuminate\Http\Response
     */
    public function copy(Request $request){

        $client_id = $request->client_id;
        $copy_id = $request->copy_id;
        $high = $request->high_id;

        $date = new Date();

        if($request->copy_id == null){

            return redirect()->route('index');

        }

        try{
            $copy_personnel = DB::select('select * from dcji01 where client_id = ? 
            and personnel_id = ?',[$client_id,$copy_id]);
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
        [
            $client_id,
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
            [$client_id,$personnel_id,$high]);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('index');
        }
        //ツリー開閉
        PtcmtrController::open_node($personnel_id);
       return redirect()->route('index');
    }
}
