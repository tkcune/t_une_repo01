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
use Illuminate\Support\Facades\View;

/**
 * 人員データを操作するコントローラー
 */
class Psji01Controller extends Controller
{
    /**
     * 人員新規登録画面表示
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
     * 
     * @var  string $client_id 顧客ID
     * @var  string $name 名前
     * @var　string $email メールアドレス
     * @var　string $password パスワード
     * @var　int $status 状態
     * @var  int $login_authority ログイン権限
     * @var  int $system_management システム管理者権限
     * @var  int $high 上位部署
     * @var  App\Models\Date $date
     * @var  App\Librarys\php\StatusCheck $check
     * @var  array $id　顧客IDに対応した最新の部署IDを格納する因数
     * @var  string $personnel_id 最新の人員ID
     * @var  App\Librarys\php\ZeroPadding $padding
     * @var  App\Librarys\php\StatusCheck $check
     * @var  string $operation_start_date　稼働開始日
     * @var  string $operation_end_date　稼働終了日
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
     * 
     * @var  string  $client_id　顧客ID
     * @var  string  $personnel_id　人員ID
     * @var  string  $name　名前
     * @var  string  $mail　メールアドレス
     * @var  string  $mail　メールアドレス
     * @var  string  $management_number 管理者ID
     * @var  string  $management_personnel_id 管理者番号
     * @var  string  $status　状態
     * @var  App\Librarys\php\StatusCheck $check
     * @var  string  $operation_start_date 稼働開始日
     * @var  string  $operation_end_date 稼働終了日
     * 
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $client_id = $request->client_id;
        $personnel_id = $request->personnel_id;
        $name = $request->name;
        $mail = $request->mail;
        $password = Hash::make($request->password);
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
               DB::update('update dcji01 set name = ?,status = ?,email = ?,password = ?,management_personnel_id = ?,operation_start_date = ? where client_id = ? and personnel_id = ?',
               [$name,$status,$mail,$password,$management_number,$operation_start_date,$client_id,$personnel_id]);
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
                DB::update('update dcji01 set name = ?,status = ?,email = ?,password = ?,management_personnel_id = ?,operation_end_date = ? where client_id = ? and personnel_id = ?',
                [$name,$status,$mail,$password,$management_number,$operation_end_date,$client_id,$personnel_id]);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('index');
            } 
        }else{
           //上記以外なら状態と名前のみ更新
            try{
                DB::update('update  dcji01 set name = ?,status = ?,email = ?,password = ?,management_personnel_id = ? where client_id = ? and personnel_id = ?',
                [$name,$status,$mail,$password,$management_number,$client_id,$personnel_id]);
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
     * 
     * @var array $high_id　上位ID
     * @var string  $message ログメッセージ
     * 
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,$id2)
    {
        //削除する人員が部署の責任者及び管理者となっていないかどうかの確認
        //if(isset()){

        //}
        try{
            $high_id = DB::select('select 
            high_id from dcji01 inner join dccmks on dcji01.personnel_id = dccmks.lower_id where dcji01.client_id = ?
            and dcji01.personnel_id = ?',[$id,$id2]);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('index');
        }
        try{
            DB::delete('delete from dcji01 where client_id = ? and personnel_id = ?',[$id,$id2]);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('index');
        }

        //データの階層構造を削除
        try{
            DB::delete('delete from dccmks where client_id = ? 
            and lower_id = ?',[$id,$id2]);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('index');
        }
        //ログ処理
        OutputLog::message_log(__FUNCTION__, 'mhcmok0003');
        $message = Message::get_message('mhcmok0003',[0=>'']);
        session(['message'=>$message[0]]);
        PtcmtrController::delete_node($high_id[0]->high_id);
        return redirect()->route('plbs01.show',[$id,$high_id[0]->high_id]);
    }

    /**
     * データ検索
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id　顧客ID
     * @param  string  $id2　選択ID
     * 
     * @var  string  $client_id　顧客ID
     * @var  string  $select_id  選択ID
     * @var  int $count_department 部署ページネーションのページ数
     * @var  int $count_personnel 人員ページネーションのページ数
     * @var  array $department_data 部署データ
     * @var  array $personnel_data 人員データ
     * @var  App\Models\Date; $date
     * @var  App\Librarys\php\ResponsiblePerson $responsible
     * @var  array $top_responsible 最上位の責任者データ
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
            where dcji01.name like ?',[$client_id,'%'.$request->search2.'%']);
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
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
            DatabaseException::dataCatchMiss($e);
            return redirect()->route('errormsg');
        }

        //ツリーデータの取得
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        return view('pacm01.pacm01',compact('count_department','personnel_data','select_id','count_personnel','department_max',
        'departments','personnel_max','names','responsible_lists','department_high','personnel_high'));
    }

    /**
     * 
     * 複製したデータを挿入するメソッド
     * @param  \Illuminate\Http\Request  $request
     * 
     * @var string $client_id 顧客ID
     * @var string $copy_id 複製するID
     * @var string $high 複製IDが所属する上位階層ID
     * @var array  $copy_personnel 複製するデータ
     * @var array  $id 存在している最新のID
     * @var string $department_id 登録する部署ID
     * @var string $personnel_id 登録する人員ID
     * @var App\Librarys\php\ZeroPadding $padding
     * @var  App\Models\Date; $date
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
        return back();
    }
}
