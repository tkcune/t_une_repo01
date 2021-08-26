<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Librarys\php\StatusCheck;

class Psbs01Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //リクエストの取得
        $client_id = $request->client_id;
        $responsible_person_id = $request->responsible_person_id;
        $name = $request->name;
        $status = $request->status;
        $management_personnel_id = $request->management_personnel_id;
        $high = $request->high;

        $check = new StatusCheck();

        //顧客IDに対応した最新の部署IDを取得
        $id = DB::select('select department_id from dcbs01 where client_id = ? 
        order by department_id desc limit 1',[$client_id]);
        $pieces[0] = substr($id[0]->department_id,0,2);
        $pieces[1] = substr($id[0]->department_id,3);
        $pieces[1] = $pieces[1] + "1";

        //0埋め
        $department_number = str_pad($pieces[1], 8, '0', STR_PAD_LEFT);
        $department_id = $pieces[0].$department_number;

        list($operation_start_date,$operation_end_date) = $check->statusCheck($request->status);

        //データベースに部署情報を登録
        DB::insert('insert into dcbs01
        (client_id,
        department_id,
        responsible_person_id,
        name,
        status,
        management_personnel_id,
        operation_start_date,
        operation_end_date)
        VALUE (?,?,?,?,?,?,?,?)',
        [$client_id,
        $department_id,
        $responsible_person_id,
        $name,
        $status,
        $management_personnel_id,
        $operation_start_date,
        $operation_end_date]);

        //データベースに階層情報を登録
        DB::insert('insert into dccmks
        (client_id,lower_id,high_id)
        VALUE (?,?,?)',
        [$client_id,$department_id,$high]);


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
    public function update(Request $request)
    {
        //dd($request);
        $client_id = $request->client_id;
        $department_id = $request->department_id;
        $name = $request->name;
        $status = $request->status;

        //状態によって時刻を挿入するのかどうかの判定　この部分共通関数にしたい
       

       //ここまで

        DB::update('update dcbs01 set name = ?,status = ? where client_id = ? and department_id = ?',
        [$name,$status,$client_id,$department_id]);

        return redirect()->route('index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id,$id2)
    {
        DB::delete('delete from dcbs01 where client_id = ? and department_id = ?',
        [$id,$id2]);

        return redirect()->route('index');
    
    }
}
