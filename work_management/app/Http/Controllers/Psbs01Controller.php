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
        //$tree = new PtcmtrController();
        //$tree_data = $tree->set_view_treedata();
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
     * @param  string $client_id  顧客ID
     * @param  string $responsible_person_id  責任者ID
     * @param  string $name  部署名
     * @param  string $status  状態
     * @param  string $management_personnel_id　管理者ID
     * @param  string $high　上位部署のID番号
     * @param  string $id　顧客IDに対応した最新の部署IDを格納する因数
     * @param  array $pieces 部署IDを英字と数字に分けるための配列
     * @param  int $department_number 0埋めをした部署IDの数字部分
     * @param  int $department_id 部署ID
     * 
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

        //顧客IDに対応した最新の部署IDを取得
        $id = DB::select('select department_id from dcbs01 where client_id = ? 
        order by department_id desc limit 1',[$client_id]);
        if(empty($id))
        {
            $department_id = "bs00000001";
        }else{

        $pieces[0] = substr($id[0]->department_id,0,2);
        $pieces[1] = substr($id[0]->department_id,3);
        $pieces[1] = $pieces[1] + "1";

        //0埋め
        $department_number = str_pad($pieces[1], 8, '0', STR_PAD_LEFT);
        $department_id = $pieces[0].$department_number;
        }

        //部署状態を判別して稼働開始日・稼働終了日を決定させる
        $check = new StatusCheck();
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
     * 部署情報を更新
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $client_id　顧客ID
     * @param  string  $department_id　部署ID
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
        //リクエストの取得
        $client_id = $request->client_id;
        $department_id = $request->department_id;
        $name = $request->name;
        $status = $request->status;

        //部署情報の更新
        if($status == "13")
        {
            //状態が稼働中なら稼働開始日を更新
            $check = new StatusCheck();
            list($operation_start_date,$operation_end_date) = $check->statusCheck($request->status);
        
            DB::update('update dcbs01 set name = ?,status = ?,operation_start_date = ? where client_id = ? and department_id = ?',
            [$name,$status,$operation_start_date,$client_id,$department_id]);
        }else if($status == "18"){
            //状態が廃止なら稼働終了日を更新
            $check = new StatusCheck();
            list($operation_start_date,$operation_end_date) = $check->statusCheck($request->status);
        
            DB::update('update dcbs01 set name = ?,status = ?,operation_end_date = ? where client_id = ? and department_id = ?',
            [$name,$status,$operation_end_date,$client_id,$department_id]); 
        }else{
            //上記以外なら状態と名前のみ更新
            DB::update('update dcbs01 set name = ?,status = ? where client_id = ? and department_id = ?',
            [$name,$status,$client_id,$department_id]);
        }

        return redirect()->route('index');
    }

    /** 階層構造を更新
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param string $client_id 顧客ID
     * @param string $id 送信されたID
     * @param string $high_id 上位ID
     * @param string $lower_id 下位ID
     * 
     * @return \Illuminate\Http\Response
     */
    public function hierarchyUpdate(Request $request,$id)
    {
        //リクエストの取得
        $client_id = $id;
        $high_id = $request->high_id;
        $lower_id = $request->lower_id;

        //データベース更新
        DB::update('update dccmks set high_id = ? where client_id = ? and lower_id = ?',
        [$high_id,$client_id,$lower_id]);

        return redirect()->route('index');
    }

    /**
     * 人員及び部署データの削除
     *
     * @param  string  $id
     * @param  string  $id2
     * @return \Illuminate\Http\Response
     */
    public function delete($id,$id2)
    {
        DB::delete('delete from dcbs01 where client_id = ? and department_id = ?',
        [$id,$id2]);

        return redirect()->route('index');
    
    }
}
