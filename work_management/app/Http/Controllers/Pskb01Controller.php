<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Facades\OutputLog;
use App\Libraries\php\Domain\PersonnelDataBase;
use App\Libraries\php\Domain\BoardDataBase;
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
     * @var  App\Libraries\php\Domain\BoardDataBase $board
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
        $board = new BoardDataBase();
        $board_lists = $board->getAll($client_id);

        return view('pvkb01.pvkb01',compact('board_lists'));
    }

    /**
     * 新規登録ページに遷移
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
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
     * @param  int  $id
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
        $board = new BoardDataBase();
        $board_details = $board->get($client_id,$select_id);

        //一覧に記載する掲示板データの取得
        $board_lists = $board->getList($client_id,$select_id);

        return view('pskb01.pskb01',compact('board_details','board_lists'));
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
     * DB:アンチパターンになってしまっている為、親子関係を示すテーブルの修正の必要性大
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($delete_id)
    {
        $client_id = session('client_id');

        //選択した部署の配下を取得
        $hierarchical = new Hierarchical();
        $delete_lists = $hierarchical->getAll($client_id,$delete_id);
    }
}
