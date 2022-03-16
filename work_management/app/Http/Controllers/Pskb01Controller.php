<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Libraries\php\Domain\BoardDataBase;
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
        $client_id = $request->client_id;
        $name = $request->name;
        $status = $request->status;
        $management_personnel_id = $request->management_number;
        $high = $request->high;
        $remarks = $request->remarks;

        // 重複クリック対策
        $request->session()->regenerateToken();

        try{
            DB::beginTransaction();
            //データベースに掲示板情報を登録
            $borad_db = new BoardDataBase();
            $borad_db->insert($client_id,$name,$status,$management_personnel_id,$remarks);

            //データベースに階層情報を登録
            $hierarchical = new Hierarchical();
            $hierarchical->insert2($client_id,$high);

            DB::commit();

        }catch(\Exception $e){
            DB::rollBack();

            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('index');
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
        //dd($board_lists);

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
}
