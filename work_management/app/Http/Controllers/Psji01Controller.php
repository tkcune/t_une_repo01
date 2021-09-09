<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Date;
use App\Librarys\php\StatusCheck;

class Psji01Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$tree = new PtcmtrController();
        //$tree_data = $tree->set_view_treedata();
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
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
        $id = DB::select('select personnel_id from dcji01 where client_id = ? 
        order by personnel_id desc limit 1',[$client_id]);
        if(empty($id))
        {
            $personnel_id = "ji00000001";
        }else{

        $pieces[0] = substr($id[0]->personnel_id,0,2);
        $pieces[1] = substr($id[0]->personnel_id,3);
        $pieces[1] = $pieces[1] + "1";

        //0埋め
        $personnel_number = str_pad($pieces[1], 8, '0', STR_PAD_LEFT);
        $personnel_id = $pieces[0].$personnel_number;
        }

        list($operation_start_date,$operation_end_date) = $check->statusCheck($request->status);

        //データベースに登録
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

        //データベースに階層情報を登録
        DB::insert('insert into dccmks
        (client_id,lower_id,high_id)
        VALUE (?,?,?)',
        [$client_id,$personnel_id,$high]);

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
     * 8.19　ルートが未設定
     * 人員情報の更新
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $client_id　顧客ID
     * @param  string  $personnel_id　人員ID
     * @param  string  $name　名前
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
       $status = $request->status;

       //部署情報の更新
       if($status == "13")
       {
           //状態が稼働中なら稼働開始日を更新
           $check = new StatusCheck();
           list($operation_start_date,$operation_end_date) = $check->statusCheck($request->status);
       
           DB::update('update dcji01 set name = ?,status = ?,operation_start_date = ? where client_id = ? and personnel_id = ?',
           [$name,$status,$operation_start_date,$client_id,$personnel_id]);
       }else if($status == "18"){
           //状態が廃止なら稼働終了日を更新
           $check = new StatusCheck();
           list($operation_start_date,$operation_end_date) = $check->statusCheck($request->status);
       
           DB::update('update dcji01 set name = ?,status = ?,operation_end_date = ? where client_id = ? and personnel_id = ?',
           [$name,$status,$operation_end_date,$client_id,$personnel_id]); 
       }else{
           //上記以外なら状態と名前のみ更新
           DB::update('update  dcji01 set name = ?,status = ? where client_id = ? and personnel_id = ?',
           [$name,$status,$client_id,$personnel_id]);
       }
       
       return redirect()->route('index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,$id2)
    {

        DB::delete('delete from dcji01 where client_id = ? and personnel_id = ?',[$id,$id2]);

        return redirect()->route('index');
    }
}
