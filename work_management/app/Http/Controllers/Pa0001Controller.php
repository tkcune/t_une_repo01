<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Librarys\php\Pagination;
use App\Librarys\php\Hierarchical;
use App\Http\Controllers\PtcmtrController;


/**
 * 導入画面のコントローラー
 */

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
        $responsible_lists = [];
        $count_department = 1;
        $count_personnel = 1;

        try{
        $department_data = DB::select('select * from dcbs01 inner join dccmks on dcbs01.department_id = dccmks.lower_id and dcbs01.client_id = ?',[$client_id]);
        }catch(\Exception $e){

            OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
            
        }

        try{
        $personnel_data = DB::select('select * from dcji01 inner join dccmks on dcji01.personnel_id = dccmks.lower_id and dcji01.client_id = ?',[$client_id]);
        }catch(\Exception $e){

        OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
        
        }

        //ページネーション
        $pagination = new Pagination();
        $department_max = $pagination->pageMax($department_data,count($department_data));
        $departments = $pagination->pagination($department_data,count($department_data),$count_department);
        $personnel_max= $pagination->pageMax($personnel_data,count($personnel_data));
        $names = $pagination->pagination($personnel_data,count($personnel_data),$count_personnel);

        

        //責任者を名前で取得
        foreach($departments as $department){
            try{
            $responsible = DB::select('select name from dcji01 where client_id = ? and personnel_id = ?',[$client_id,$department->responsible_person_id]);
            }catch(\Exception $e){

            OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
            
            }
            array_push($responsible_lists,$responsible[0]->name);
        }
       
        //上位階層取得
        $hierarchical = new Hierarchical();
        $department_high = $hierarchical->upperHierarchyName($departments);
        $personnel_high = $hierarchical->upperHierarchyName($names);
        
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();
        
        return view('pacm01.pacm01',compact('departments','names','count_department',
        'count_personnel','department_max','personnel_max','department_high',
        'personnel_high','responsible_lists'));
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
        $responsible_lists = [];
        $client_id = "aa00000001";
        $count_department = $_GET['department_page'];
        $count_personnel = $_GET['personnel_page'];
        
        try{
        $department_data = DB::select('select * from dcbs01 inner join dccmks on dcbs01.department_id = dccmks.lower_id and dcbs01.client_id = ?',[$client_id]);
        }catch(\Exception $e){

            OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
        
        }
        try{
        $personnel_data = DB::select('select * from dcji01 inner join dccmks on dcji01.personnel_id = dccmks.lower_id and dcji01.client_id = ?',[$client_id]);
        }catch(\Exception $e){

        OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
        
        }
        //ページネーション
        $pagination = new Pagination();
        $department_max = $pagination->pageMax($department_data,count($department_data));
        $departments = $pagination->pagination($department_data,count($department_data),$count_department);
        $personnel_max= $pagination->pageMax($personnel_data,count($personnel_data));
        $names = $pagination->pagination($personnel_data,count($personnel_data),$count_personnel);

        //責任者を名前で取得
        foreach($departments as $department){
            try{
                $responsible = DB::select('select name from dcji01 where client_id = ? and personnel_id = ?',[$client_id,$department->responsible_person_id]);
            }catch(\Exception $e){

                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
            
            }
            array_push($responsible_lists,$responsible[0]->name);
        }

        //上位階層取得
        $hierarchical = new Hierarchical();
        $department_high = $hierarchical->upperHierarchyName($departments);
        $personnel_high = $hierarchical->upperHierarchyName($names);
        
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();
        return view('pacm01.pacm01',compact('departments','names','count_department','count_personnel',
        'department_max','personnel_max','department_high',
        'personnel_high','responsible_lists'));
    }

    /**
     * 部署を絞り込みした後のページネーション
     *
     * 
     * @return \Illuminate\Http\Response
     */
    public function count2(Request $request)
    {
        $responsible_lists = [];
        $lists = [];
        $department_data = [];
        $personnel_data = [];
        

        $client = $_GET['id'];
        $select_id = $_GET['id2'];
        $count_department = $_GET['department_page'];
        $count_personnel = $_GET['personnel_page'];
        
        //選択した部署のIDをarray型に格納
        array_push($lists,$select_id);
        

        //選択した部署の配下を取得
        $hierarchical = new Hierarchical();
        $select_lists = $hierarchical->subordinateSearch($lists,$client);
         
        //選択したデータ及び配下データを取得
           foreach($select_lists as $select_list){
               //機能コードの判定
               $code = substr($select_list,0,2);

               //対応したデータの取得
               if ($code == "bs"){
                   try{
                   $data = DB::select('select * from dcbs01 inner join dccmks on dcbs01.department_id = dccmks.lower_id where dcbs01.client_id = ?
                   and dcbs01.department_id = ?',[$client,$select_list]);
                    }catch(\Exception $e){

                        OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                
                    }
                   array_push($department_data,$data[0]);

               }elseif($code == "ji"){
                   try{
                   $data = DB::select('select * from dcji01 inner join dccmks on dcji01.personnel_id = dccmks.lower_id where dcji01.client_id = ?
                   and dcji01.personnel_id = ?',[$client,$select_list]);
                    }catch(\Exception $e){

                    OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                
                    }
                   array_push($personnel_data,$data[0]);
               }else{

               }
               
           }

        //ページネーション
        $pagination = new Pagination();
        $department_max = $pagination->pageMax($department_data,count($department_data));
        $departments = $pagination->pagination($department_data,count($department_data),$count_department);
        $personnel_max= $pagination->pageMax($personnel_data,count($personnel_data));
        $names = $pagination->pagination($personnel_data,count($personnel_data),$count_personnel);

        //責任者を名前で取得
        foreach($departments as $department){
            try{
            $responsible = DB::select('select name from dcji01 where client_id = ? and personnel_id = ?',[$client,$department->responsible_person_id]);
            }catch(\Exception $e){

            OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
        
            }
            array_push($responsible_lists,$responsible[0]->name);
        }

        //上位階層取得
        $hierarchical = new Hierarchical();
        $department_high = $hierarchical->upperHierarchyName($departments);
        $personnel_high = $hierarchical->upperHierarchyName($names);
        
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        return view('psbs01.plbs01',compact('departments','names','count_department','count_personnel',
        'department_max','personnel_max','department_high',
        'personnel_high','responsible_lists','client','select_id'));
    }
}
