<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Librarys\php\OutputLog;
use App\Librarys\php\Message;

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $client_id = $request->client_id;
        $high_id = $request->high_id;
        $projection_source_id = $request->projection_source_id;
        //最新の投影番号を生成
        try{
            $id = DB::select('select projection_id from dccmta where client_id = ? 
            order by projection_id desc limit 1',[$client_id]);
        }catch(\Exception $e){

            OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
            DatabaseException::common($e);
            return redirect()->route('index');
        }
        if(empty($id))
        {
            $projection_id = "ta00000001";
        }else{

        $pieces[0] = substr($id[0]->projection_id,0,2);
        $pieces[1] = substr($id[0]->projection_id,3);
        $pieces[1] = $pieces[1] + "1";

        //0埋め
        $projection_number = str_pad($pieces[1], 8, '0', STR_PAD_LEFT);
        $projection_id = $pieces[0].$projection_number;
        }

        //データベースに投影情報を登録
        try{
            DB::insert('insert into dccmta
            (client_id,projection_id,projection_source_id)
            VALUE (?,?,?)',
            [$client_id,$projection_id,$projection_source_id]);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
        }

        //データベースに階層情報を登録
        try{
            DB::insert('insert into dccmks
            (client_id,lower_id,high_id)
            VALUE (?,?,?)',
            [$client_id,$projection_id,$high_id]);
        }catch(\Exception $e){

            OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
            DatabaseException::common($e);
            return redirect()->route('index');
        }

        //ログ処理
        OutputLog::message_log(__FUNCTION__, 'mhcmok0010');
        $message = Message::get_message('mhcmok0010',[0=>'']);
        session(['message'=>$message[0]]);
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
     * @param  string  $id
     * @param  string  $id2
     * @return \Illuminate\Http\Response
     */
    public function delete($id,$id2)
    {
        try{
            DB::delete('delete from dccmta where client_id = ? and projection_id = ?',
            [$id,$id2]);
        }catch(\Exception $e){

            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
        }

        OutputLog::message_log(__FUNCTION__, 'mhcmok0003');
        $message = Message::get_message('mhcmok0003',[0=>'']);
        session(['message'=>$message[0]]);
        return redirect()->route('index');
    }
}
