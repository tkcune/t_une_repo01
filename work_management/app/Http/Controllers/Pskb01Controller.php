<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Facades\OutputLog;
use App\Libraries\php\Domain\PersonnelDataBase;
use App\Libraries\php\Domain\BoardDataBase;
use App\Libraries\php\Domain\ProjectionDataBase;
use App\Libraries\php\Domain\Hierarchical;
use App\Libraries\php\Service\DatabaseException;
use App\Libraries\php\Service\Message;
use App\Libraries\php\Service\ZeroPadding;
use App\Http\Controllers\PtcmtrController;

class Pskb01Controller extends Controller
{
    /**
     * 掲示板トップ画面
     *
     * @var  string $client_id 顧客ID
     * @var  App\Http\Controllers\PtcmtrController $tree
     * @var  array $tree_data ツリーデータ
     * @var  App\Libraries\php\Domain\BoardDataBase $board_db
     * @var  array $board_lists 掲示板一覧データ
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //ログインしている顧客IDの取得
        $client_id = session('client_id');
        
        //ツリーデータの取得
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        //一覧に記載する掲示板データの取得
        $board_db = new BoardDataBase();
        $board_lists = $board_db->getAll($client_id);

        return view('pvkb01.pvkb01',compact('board_lists'));
    }

    /**
     * 新規登録ページに遷移
     *
     * @var $client_id 顧客ID
     * @var  App\Http\Controllers\PtcmtrController $tree
     * @var  array $tree_data ツリーデータ
     * 
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //ログインしている顧客IDの取得
        $client_id = session('client_id');

        //ツリーデータの取得
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        return view('pskb01.pskb02');
    }

    /**
     * 掲示板登録
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @var $client_id　顧客ID
     * @var $name 名称
     * @var $status 状態
     * @var $management_personnel_id 管理者ID
     * @var $high 上位ID
     * @var $remarks 備考
     * @var App\Libraries\php\Domain\BoardDataBase $board_db
     * @var $id 現時点でDBに存在している最新のID
     * @var $board_id 最新の掲示板ID
     * @var App\Libraries\php\Domain\Hierarchical $hierarchical
     * 
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $client_id = session('client_id');
        $name = $request->name;
        $status = $request->status;
        $management_personnel_id = $request->management_number;
        $high = $request->high;
        $remarks = $request->remarks;

        // 重複クリック対策
        $request->session()->regenerateToken();

        //顧客IDに対応した最新の部署IDを取得
        try{
            $board_db = new BoardDataBase();
            $id = $board_db->getId($client_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            
            return redirect()->route('pskb.index');
        }

        if(empty($id)){
            $board_id = "kb00000001";
        }else{
            //登録する番号を作成
            $padding = new ZeroPadding();
            $board_id = $padding->padding($id[0]->board_id);
        }

        try{
            DB::beginTransaction();
            //データベースに掲示板情報を登録
            $board_db = new BoardDataBase();
            $board_db->insert($client_id,$board_id,$name,$status,$management_personnel_id,$remarks);

            //データベースに階層情報を登録
            $hierarchical = new Hierarchical();
            $hierarchical->insert($client_id,$board_id,$high);

            DB::commit();

            return redirect()->route('pskb01.show',[$client_id,$high]);

        }catch(\Exception $e){
            DB::rollBack();

            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('pskb01.index');
        }

    }

    /**
     * 選択掲示板の表示
     *
     * @param  int  $client_id 顧客ID
     * @param  int  $select_id 選択ID
     * 
     * @var App\Libraries\php\Domain\BoardDataBase $board_db
     * @var array $board_details 掲示板詳細データ
     * @var array $board_lists 掲示板一覧データ
     * @var App\Libraries\php\Domain\PersonnelDataBase $personnel_db
     * @var $system_management_lists システム管理者リスト
     * 
     * @return \Illuminate\Http\Response
     */
    public function show($client_id,$select_id)
    {
        //ログインしている顧客IDの取得
        $client_id = session('client_id');

        //ツリーデータの取得
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        //詳細に記載する掲示板データの取得
        $board_db = new BoardDataBase();
        $board_details = $board_db->get($client_id,$select_id);

        //一覧に記載する掲示板データの取得
        $board_lists = $board_db->getList($client_id,$select_id);

        //システム管理者のリストを取得
        try{
            $personnel_db = new PersonnelDataBase();
            $system_management_lists = $personnel_db->getSystemManagement($client_id);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }

        return view('pskb01.pskb01',compact('board_details','board_lists','system_management_lists'));
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
     * @param  int $board_id 掲示板ID
     * 
     * @var $client_id　顧客ID
     * @var $name 名称
     * @var $status 状態
     * @var $management_personnel_id 管理者ID
     * @var $high 上位ID
     * @var $remarks 備考
     * @var App\Libraries\php\Domain\PersonnelDataBase $personnel_db
     * @var $management_personnel_id 管理者人員ID
     * @var App\Libraries\php\Domain\BoardDataBase $board_db
     * 
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$board_id)
    {
        //リクエストの取得
        $client_id = session('client_id');
        $name = $request->name;
        $management_number = $request->management_number;
        $status = $request->status;
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
                return redirect()->route('pskb.index');
        }
        
        if($management_personnel_id == null){
            return redirect()->route('pskb.index');
        }

        //情報の更新
        try{
            $board_db = new BoardDataBase();
            $board_db->update($client_id,$board_id,$name,$status,$management_number,$remarks);
        }catch(\Exception $e){
            //エラー処理
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
            DatabaseException::common($e);
            return redirect()->route('pskb.index');
        }
            //ログ処理
            OutputLog::message_log(__FUNCTION__, 'mhcmok0002');
            $message = Message::get_message('mhcmok0002',[0=>'']);
            session(['message'=>$message[0]]);

        return back();
    }

    /**
     * 掲示板の削除
     *
     * @param  int  $delete　削除ID
     * 
     * @var  $client_id 顧客ID
     * @var  array $lists 削除予定のIDを格納した配列
     * @var  array $delete_id 選択部署の配下の削除予定のID
     * @var  array $delete_data 削除掲示板の上位ID
     * @var  App\Libraries\php\Domain\BoardDataBase $board_db
     * @var  App\Libraries\php\Domain\Hierarchical $hierarchical
     * @var  array $delete_lists 削除予定のIDを格納した配列
     * @var  int   $code 機能コードの頭2文字
     * @var  array $delete_projections 削除元
     * @var  App\Libraries\php\Domain\ProjectionDataBase $projection_db
     * @var  string $message メッセージ
     * 
     * @return \Illuminate\Http\Response
     */
    public function destroy($delete)
    {
        $client_id = session('client_id');
        $lists = [];
        $delete_id = [];
        array_push($lists,$delete);

        $board_db = new BoardDataBase();

        //削除予定の掲示板の上位IDを取得(削除後のページ遷移に必要)
        $delete_data = $board_db->get($client_id,$delete);

        //削除予定の配下掲示板IDを取得
        $hierarchical = new Hierarchical();
        $delete_lists = $hierarchical->subordinateSearchRoop($lists,$client_id,$delete_id);

        //選択した削除予定掲示板のIDを追加
        array_unshift($delete_lists,$delete);

        try{
            //トランザクション
            DB::beginTransaction();

            foreach($delete_lists as $delete_list){
                //機能コードの判定
                $code = substr($delete_list,0,2);

                //対応したデータの削除
                if ($code == "kb"){
                    $board_db->delete($client_id,$delete_list);

                    //削除予定の配下掲示板が元になった投影を削除
                    $projection_db = new ProjectionDataBase();
                    $delete_projections = $projection_db->getProjectionId($client_id,$delete_list);
                    foreach($delete_projections as $delete_projection){
                        $hierarchical = new Hierarchical();
                        $hierarchical->delete($client_id,$delete_projection->projection_id);
                        $projection_db = new ProjectionDataBase();
                        $projection_db->delete($client_id,$delete_projection->projection_id);
                    }
                    $projection_db = new ProjectionDataBase();
                    $projection_db->delete($client_id,$delete_list);

                }elseif($code == "ta"){
                    //投影の削除
                    $projection_db = new ProjectionDataBase();
                    $projection_db->delete($client_id,$delete_list);
                }else{

                }
                //データの階層構造を削除
                $hierarchical = new Hierarchical();
                $hierarchical->delete($client_id,$delete_list);
            }

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

        PtcmtrController::delete_node($delete_data[0]->high_id);

        if(!isset($delete_data[0]->high_id)){
            return redirect()->route('pskb.index');
        }
        return redirect()->route('plbs01.show',[$client_id,$delete_data[0]->high_id]);
    }

}
