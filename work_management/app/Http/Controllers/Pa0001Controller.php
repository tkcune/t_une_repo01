<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Librarys\php\Pagination;

class Pa0001Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$client_idの数値はダミー
        $client_id = "aa00000001";
        $count_department = 1;
        $count_personnel = 1;
        $department_data = DB::select('select * from dcbs01 where client_id = ?',[$client_id]);
        $personnel_data = DB::select('select * from dcji01 where client_id = ?',[$client_id]);
        $pagination = new Pagination();
        $department_max = $pagination->pageMax($department_data,count($department_data));
        $departments = $pagination->pagination($department_data,count($department_data),$count_department);
        $personnel_max= $pagination->pageMax($personnel_data,count($personnel_data));
        $names = $pagination->pagination($personnel_data,count($personnel_data),$count_personnel);
        
        return view('pacm01.pacm01',compact('departments','names','count_department','count_personnel','department_max','personnel_max'));
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
        //
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * 
     * @return \Illuminate\Http\Response
     */
    public function count(Request $request)
    {
        $client_id = "aa00000001";
        $count_department = $_GET['department_page'];
        $count_personnel = $_GET['personnel_page'];
        $department_data = DB::select('select * from dcbs01 where client_id = ?',[$client_id]);
        $personnel_data = DB::select('select * from dcji01 where client_id = ?',[$client_id]);
        $pagination = new Pagination();
        $department_max = $pagination->pageMax($department_data,count($department_data));
        $departments = $pagination->pagination($department_data,count($department_data),$count_department);
        $personnel_max= $pagination->pageMax($personnel_data,count($personnel_data));
        $names = $pagination->pagination($personnel_data,count($personnel_data),$count_personnel);
        return view('pacm01.pacm01',compact('departments','names','count_department','count_personnel','department_max','personnel_max'));
    }

}
