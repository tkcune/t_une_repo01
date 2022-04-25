<?php

namespace App\Http\Controllers;

use App\Facades\OutputLog;
use App\Models\Date;
use App\Http\Controllers\PtcmtrController;
use App\Http\Requests\PersonnelRequest;
use App\Libraries\php\Service\NetworkClient;
use App\Libraries\php\Domain\Hierarchical;
use App\Libraries\php\Domain\DepartmentDataBase;
use App\Libraries\php\Domain\PersonnelDataBase;
use App\Libraries\php\Domain\ProjectionDataBase;
use App\Libraries\php\Logic\ResponsiblePerson;
use App\Libraries\php\Logic\MailAddressCheck;
use App\Libraries\php\Service\DatabaseException;
use App\Libraries\php\Service\Message;
use App\Libraries\php\Service\Pagination;
use App\Libraries\php\Service\PaginationObject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;

use App\Libraries\php\Service\DepartmentDetailsObject;

use App\Libraries\php\Service\Display\List\DepartmentDisplayList;
use App\Libraries\php\Service\Display\List\PersonnelDisplayList;


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
        if(session('device') != 'mobile'){
            return view('psji01.psji01');
        }else{
            return view('psji01.psji02');
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
     * 新規人員データ登録
     * 
     * @param  App\Http\Requests\PersonnelRequest;  $request
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
     * @var  App\Libraries\php\Domain\PersonnelDataBase $personnel_db
     * @var  App\Libraries\php\Domain\Hierarchical $hierarchical
     * @var  string $personnel_id 最新の人員ID
     * 
     * @return \Illuminate\Http\Response
     */
    public function store(PersonnelRequest $request)
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
        $remarks = $request->remarks;
        $date = new Date();

        // 重複クリック対策
        $request->session()->regenerateToken();

        //リクエストに空白が無いかどうかの確認
        if(empty($name) || empty($email) || empty($status)){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0003','01');
            $message = Message::get_message_handle('mhcmer0003',[0=>'']);
            session(['message'=>$message[0],'handle_message'=>$message[3]]);
            return back();
        }

        //顧客IDに対応した最新の人員IDを取得
        try{
            $personnel_db = new PersonnelDataBase();
            $personnel_id = $personnel_db->getNewId($client_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('index');
        }

        $mail_check = new MailAddressCheck();
        $mail_flag = $mail_check->check($client_id,$personnel_id,$email);

        //メールアドレス重複チェック
        if($mail_flag == false){
            OutputLog::message_log(__FUNCTION__, 'mhjier0001','01');
            $message = Message::get_message_handle('mhjier0001',[0=>'']);
            session(['message'=>$message[0],'handle_message'=>$message[3]]);
            return back();
        }

        try{
            //トランザクション
            DB::beginTransaction();

            //データベースに人員情報を登録
            $personnel_db = new PersonnelDataBase();
            $personnel_db->insert($client_id,$personnel_id,$name,$email,$password,$status,$login_authority,$system_management,$remarks);
            //データベースに階層情報を登録
            $hierarchical = new Hierarchical();
            $hierarchical->insert($client_id,$personnel_id,$high);

            DB::commit();
        }catch(\Exception $e){
            //ロールバック
            DB::rollBack();

            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('index');
        }
        OutputLog::message_log(__FUNCTION__, 'mhcmok0001');
        $request->session()->put('message',Config::get('message.mhcmok0001'));
        PtcmtrController::open_node($personnel_id);
        return redirect()->route('plbs01.show',[$client_id,$high]);
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
     * @param  App\Http\Requests\PersonnelRequest $request
     * 
     * @var  string  $client_id　顧客ID
     * @var  string  $personnel_id　人員ID
     * @var  string  $name　名前
     * @var  string  $mail　メールアドレス
     * @var  string  $password　パスワード
     * @var  string  $management_number 管理者ID
     * @var  string  $login_authority ログイン権限
     * @var  string  $system_management システム管理者権限
     * @var  string  $management_personnel_id 管理者番号
     * @var  string  $status　状態
     * @var  string  $start_day 稼働開始日
     * @var  string  $finish_day 稼働終了日
     * @var  string  $remarks 備考
     * @var  App\Libraries\php\Domain\PersonnelDataBase $personnel_db
     * @var  array $check_id 人員が存在するか確認する変数
     * 
     * @return \Illuminate\Http\Response
     */
    public function update(PersonnelRequest $request)
    {
        $client_id = $request->client_id;
        $personnel_id = $request->personnel_id;
        $name = $request->name;
        $mail = $request->email;
        $password = Hash::make($request->password);
        $management_number = $request->management_number;
        if(isset($request->login_authority)){
            $login_authority = $request->login_authority;
        }else{
            $login_authority = "0";
        }
        if(isset($request->system_management)){
            $system_management = $request->system_management;
        }else{
            $system_management = "0";
        }
        $status = $request->status;
        $start_day = $request->start_day;
        $finish_day = $request->finish_day;
        $remarks = $request->remarks;

        // 重複クリック対策
        $request->session()->regenerateToken();

        //入力された番号の人員が存在するかの確認
        try{
            $personnel_db = new PersonnelDataBase();
            $check_id = $personnel_db->getData($client_id,$management_number);
        }catch(\Exception $e){
            //エラー処理
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
            DatabaseException::common($e);
            return redirect()->route('index');
        }
        if($check_id == null){
            return redirect()->route('index');
        }

        $mail_check = new MailAddressCheck();
        $mail_flag = $mail_check->check($client_id,$personnel_id,$mail);

        //メールアドレス重複チェック
        if($mail_flag == false){
            OutputLog::message_log(__FUNCTION__, 'mhjier0001','01');
            $message = Message::get_message_handle('mhjier0001',[0=>'']);
            session(['message'=>$message[0],'handle_message'=>$message[3]]);
            return back();
        }

        //日付チェック
        if($start_day>$finish_day && !$finish_day == null){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0008','01');
            $message = Message::get_message_handle('mhcmer0008',[0=>'']);
            session(['message'=>$message[0],'handle_message'=>$message[3]]);
            return back();
        }

        //人員情報の更新
        try{
            $personnel_db = new PersonnelDataBase();
            $personnel_db->update($request->password,$name,$status,$mail,$password,$management_number,$login_authority,$system_management,$start_day,$finish_day,$remarks,$client_id,$personnel_id);
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
     * @var  App\Libraries\php\Domain\PersonnelDataBase $personnel_db
     * @var array $high_id　上位ID
     * @var App\Libraries\php\Domain\Hierarchical $hierarchical
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
            $personnel_db = new PersonnelDataBase();
            $high_id = $personnel_db->getHighId($id,$id2);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('index');
        }
        try{
            //トランザクション
            DB::beginTransaction();

            //人員データの削除
            $personnel_db = new PersonnelDataBase();
            $personnel_db->delete($id,$id2);

            //データの階層構造を削除
            $hierarchical = new Hierarchical();
            $hierarchical->delete($id,$id2);

            DB::commit();
        }catch(\Exception $e){
            //ロールバック
            DB::rollBack();

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
     * 人員データ検索
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id　顧客ID
     * @param  string  $id2　選択ID
     * 
     * @var  string  $client_id　顧客ID
     * @var  string  $select_id  選択ID
     * @var  int $count_department 部署ページネーションのページ数
     * @var  int $count_personnel 人員ページネーションのページ数
     * @var  App\Libraries\php\Domain\PersonnelDataBase $personnel_db
     * @var  App\Libraries\php\Domain\DepartmentDataBase $department_db
     * @var  App\Libraries\php\Domain\ProjectionDataBase $projection_db
     * @var  array $department_data 部署データ
     * @var  array $personnel_data 人員データ
     * @var  array $system_management_lists システム管理者リスト
     * @var  array $departments ページネーション後の部署データ
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

        if(isset($_GET['search2'])){
            $_POST['search2'] = $_GET['search2'];
        }

        if(isset($_GET['department_page'])){
            $count_department = $_GET['department_page'];
            $count_personnel = $_GET['personnel_page'];
        }else{
            $count_department = Config::get('startcount.count');
            $count_personnel = Config::get('startcount.count');
        }

        $department_db = new DepartmentDataBase();
        $personnel_db = new PersonnelDataBase();
        $projection_db = new ProjectionDataBase();

        $click_id = $select_id;
        View::share('click_id', $click_id);

        //画面表示データの取得
        $select_code = substr($select_id,0,2);

        if($select_code == "ta"){
            //選択部署がtaだった場合は対応するIDを取得
            $projection_db = new ProjectionDataBase();
            $projection_code = $projection_db->getId($select_id);
            $select_id = $projection_code[0]->projection_source_id;
            $select_code = substr($projection_code[0]->projection_source_id,0,2);
        }

        //一覧画面のデータ取得
        $department_display_list = new DepartmentDisplayList();
        $departments = $department_display_list->display($client_id,$select_id,$count_department);

        $personnel_display_list = new PersonnelDisplayList();
        $names = $personnel_display_list->display($client_id,$select_id,$count_personnel,$request->search2);

        //検索結果が0件なら戻る
        if(empty($names['data'])){
            OutputLog::message_log(__FUNCTION__, 'mhcmwn0001');
            $message = Message::get_message_handle('mhcmwn0001',[0=>'']);
            session(['message'=>$message[0],'handle_message'=>$message[3]]);

            if($select_id == "bs00000000"){
                return redirect()->route('pa0001.top');
            }else{
                return redirect()->route('plbs01.show',[$client_id,$select_id]);
            }
        }
        
        //詳細画面部署のデータを取得
        try{
            $department_db = new DepartmentDataBase();
            $click_department_data = $department_db->get($client_id,$select_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
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

        //ツリーデータの取得
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        //部署詳細オブジェクトの設定
        if(!$select_id == "bs00000000"){
            $department_details_object = new DepartmentDetailsObject();
            $department_details_object->setDepartmentObject($click_department_data);
        }

        if(session('device') != 'mobile'){
            //トップ画面かの判別
            if($select_id == "bs00000000"){
                $department_details = $departments;
                $personnel_details = $names;
                return view('pvbs01.pvbs01',compact('department_details','personnel_details',
                'count_department','count_personnel'));
            }else{
                return view('pacm01.pacm01',compact('count_department','count_personnel','click_department_data','personnel_data','select_id',
                'departments','names','system_management_lists'));
            }
        }else{
            //@var DepartmentDetailsObject 変数の名前合わせ
            $click_data = $department_details_object;
            return view('pacm01.pacm02',compact('click_data', 'pagination_object', 'department_details_object',
            'system_management_lists', 'click_id', 'select_id', 'personnel_data'));
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
     * @var App\Models\Date; $date
     * @var App\Libraries\php\Domain\ProjectionDataBase $projection_db
     * @var array  $projection_source_id　複製元ID
     * @var array  $projection_id 投影ID
     * @var array  $copy_personnel 複製するデータ
     * @var array  $id 存在している最新のID
     * @var string $department_id 登録する部署ID
     * @var string $personnel_id 登録する人員ID
     * @var App\Libraries\php\Domain\PersonnelDataBase $personnel_db
     * @var App\Libraries\php\Domain\Hierarchical $hierarchical
     * @var string $message メッセージ
     * 
     * @return \Illuminate\Http\Response
     */
    public function copy(Request $request){

        $client_id = $request->client_id;
        $copy_id = $request->copy_id;
        $high = $request->high_id;

        // 重複クリック対策
        $request->session()->regenerateToken();

        $date = new Date();

        //複写番号が空白の場合エラーメッセージを表示
        if($request->copy_id == null){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0009','01');
            $message = Message::get_message_handle('mhcmer0009',[0=>'']);
            session(['message'=>$message[0],'handle_message'=>$message[3]]);
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

            //人員の配下に部署を複製しないように分岐
            if(substr($projection_source_id,0,2) == "bs"){
                //エラーメッセージ表示
                OutputLog::message_log(__FUNCTION__, 'mhcmer0010');
                $message = Message::get_message_handle('mhcmer0010',[0=>'']);
                session(['message'=>$message[0],'handle_message'=>$message[3]]);
                return redirect()->route('index');
            }

            //最新の投影番号を生成
            try{
                $projection_db = new ProjectionDataBase();
                $projection_id = $projection_db->getNewId($client_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
            }

            //データベースに投影情報を登録
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
            //複製する人員情報の取得
            try{
                $personnel_db = new PersonnelDataBase();
                $copy_personnel = $personnel_db->getData($client_id,$copy_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('index');
            }

            //顧客IDに対応した最新の人員IDを取得
            try{
                $personnel_db = new PersonnelDataBase();
                $personnel_id = $personnel_db->getNewId($client_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('index');
            }

            //データベースに登録
            try{
                //トランザクション
                DB::beginTransaction();

                //データベースに人員情報を登録
                $personnel_db = new PersonnelDataBase();
                $personnel_db->copy($client_id,$personnel_id,$copy_personnel[0]->name,
                $copy_personnel[0]->email,$copy_personnel[0]->password,
                $date->today(),$copy_personnel[0]->status,$personnel_id,
                $copy_personnel[0]->login_authority,$copy_personnel[0]->system_management,
                $copy_personnel[0]->operation_start_date,$copy_personnel[0]->operation_end_date,
                $copy_personnel[0]->remarks);

                //データベースに階層情報を登録
                $hierarchical = new Hierarchical();
                $hierarchical->insert($client_id,$personnel_id,$high);

                DB::commit();

            }catch(\Exception $e){
                //ロールバック
                DB::rollBack();

                OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
                DatabaseException::common($e);
                return redirect()->route('index');
            }
            //ツリー開閉
            PtcmtrController::open_node($personnel_id);

            //ログ処理
            OutputLog::message_log(__FUNCTION__, 'mhcmok0009');
            $message = Message::get_message('mhcmok0009',[0=>'']);
            session(['message'=>$message[0]]);
            return back();
        }
    }

    /**
     * 
     * メールの送信試験メソッド
     * @param  \Illuminate\Http\Request  $request
     * 
     * @var string $client_id 顧客ID 
     * @var string $name 名前
     * @var string $email メールアドレス
     * @var NetworkClient $network
     *
     */
    public function mailSend(Request $request){

        $client_id = $request->client_id;
        $name = $request->name;
        $email = $request->email;

        $network = new NetworkClient();
        $network->send_ji_test_mail($client_id, $name, $email);

        return back();
    }
}
