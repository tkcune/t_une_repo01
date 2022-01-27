<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Facades\OutputLog;
use App\Http\Controllers\PtcmtrController;
use App\Libraries\php\Service\DatabaseException;
use App\Libraries\php\Service\Message;
use App\Libraries\php\Domain\Hierarchical;
use App\Libraries\php\Domain\ProjectionDataBase;

/**
 * 投影データを操作するコントローラー
 */
class Ptcm01Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * 投影データの登録
     * @param  \Illuminate\Http\Request  $request
     * 
     * @var  string $client_id　顧客ID
     * @var  string $high_id 上位ID
     * @var  string $projection_source_id　投影元ID
     * @var string $projection_id　作成する投影ID 
     * @var App\Libraries\php\ProjectionDataBase $projection_db
     * @var App\Libraries\php\Hierarchical $hierarchical
     * @var string $message ログメッセージ
     * 
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $client_id = $request->client_id;
        $high_id = $request->high_id;
        $projection_source_id = $request->projection_source_id;

        // 重複クリック対策
        $request->session()->regenerateToken();

        //複写番号が空白の場合はエラーメッセージを表示
        if($request->projection_source_id == null){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0009','01');
            $message = Message::get_message_handle('mhcmer0009',[0=>'']);
            session(['message'=>$message[0],'handle_message'=>$message[3]]);
            return redirect()->route('index');
        }

        //送信元が投影だった場合は投影元のIDに変換
        if(substr($projection_source_id,0,2) == "ta"){
            try{
                $projection_db = new ProjectionDataBase();
                $code = $projection_db->getId($projection_source_id);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
            }
            $projection_source_id = $code[0]->projection_source_id;
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

        try{
            //トランザクション
            DB::beginTransaction();

            //データベースに投影情報を登録
            $projection_db = new ProjectionDataBase();
            $projection_db->insert($client_id,$projection_id,$projection_source_id);

            //データベースに階層情報を登録
            $hierarchical = new Hierarchical();
            $hierarchical->insert($client_id,$projection_id,$high_id);

            DB::commit();
        }catch(\Exception $e){

            //ロールバック
            DB::rollBack();

            OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
            DatabaseException::common($e);
            return redirect()->route('index');
        }

        //ログ処理
        OutputLog::message_log(__FUNCTION__, 'mhcmok0010');
        $message = Message::get_message('mhcmok0010',[0=>'']);
        session(['message'=>$message[0]]);
        PtcmtrController::open_node($projection_id);
        return redirect()->route('plbs01.show',[$client_id,$high_id]);
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
     * 
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 投影データの削除
     *
     * @param  string  $id 顧客ID
     * @param  string  $id2　投影ID
     * 
     * @var App\Libraries\php\ProjectionDataBase $projection_db
     * @var string $high_id 上位ID
     * @var App\Libraries\php\Hierarchical $hierarchical
     * @var string  $message ログメッセージ
     * 
     * @return \Illuminate\Http\Response
     */
    public function delete($id,$id2)
    {
        try{
            //選択した投影の上位IDを取得
            $projection_db = new ProjectionDataBase();
            $high_id = $projection_db->getHighID($id,$id2);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
            return redirect()->route('index');
        }

        try{
            //トランザクション
            DB::beginTransaction();

            //投影の削除
            $projection_db = new ProjectionDataBase();
            $projection_db->delete($id,$id2);

            //階層の削除
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
}
