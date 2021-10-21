<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Librarys\php\DatabaseException;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use App\Librarys\php\Pagination;
use App\Librarys\php\ResponsiblePerson;
use App\Librarys\php\Hierarchical;
use App\Http\Controllers\PtcmtrController;



/**
 * 導入画面のコントローラー
 */

class Pa0001Controller extends Controller
{
    /**
     * ディスプレイ表示
     *
     * @param  string  $client_id 顧客ID　9/27現在　ダミーデータ
     * @param　int $count_department 部署ページネーションのページ数
     * @param　int $count_personnel  人員ページネーションのページ数
     * @param  array $top_department 最上位の部署データ
     * @param  array $department_data 全体部署データ
     * @param  array $personnel_data 全体人員データ
     * @param  App\Librarys\php\Pagination $pagination 
     * @param  int $department_max 部署データページネーションの最大値
     * @param  array $departments ページネーション後の部署データ
     * @param  int $personnel_max 人員データページネーションの最大値
     * @param  array $names ページネーション後の人員データ
     * @param  App\Librarys\php\ResponsiblePerson $responsible
     * @param  array $top_responsible 最上位の責任者データ
     * @param  array $responsible_lists 責任者リスト
     * @param  array $top_management 最上位の管理者データ
     * @param  array $management_lists 管理者データ
     * @param  App\Librarys\php\Hierarchical $hierarchical
     * @param  array $department_high 部署データの上位階層
     * @param  array $personnel_high 人員データの上位階層
     * @param  App\Http\Controllers\PtcmtrController $tree
     * @param  array $tree_data ツリーデータ
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$client_idの数値はダミー(ログイン時にセッション保存する予定)
        $client_id = "aa00000001";
        session(['client_id'=>$client_id]);

        $count_department = Config::get('startcount.count');
        $count_personnel = Config::get('startcount.count');

        //一番上の部署を取得
        try{
            $top_department = DB::select('select * from dcbs01 where client_id = ? limit 1',[$client_id]);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
        }

        //全体の部署データの取得
        try{
            $department_data = DB::select('select * from dcbs01 inner join dccmks on dcbs01.department_id = dccmks.lower_id and dcbs01.client_id = ?',[$client_id]);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
        }

        //全体の人員データの取得
        try{
            $personnel_data = DB::select('select * from dcji01 inner join dccmks on dcji01.personnel_id = dccmks.lower_id and dcji01.client_id = ?',[$client_id]);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
        }

        //ページネーション
        $pagination = new Pagination();
        $department_max = $pagination->pageMax($department_data,count($department_data));
        $departments = $pagination->pagination($department_data,count($department_data),$count_department);
        $personnel_max= $pagination->pageMax($personnel_data,count($personnel_data));
        $names = $pagination->pagination($personnel_data,count($personnel_data),$count_personnel);

        //責任者を名前で取得
        $responsible = new ResponsiblePerson();
        $top_responsible = $responsible->getResponsibleLists($client_id,$top_department);
        $responsible_lists = $responsible->getResponsibleLists($client_id,$departments);

        //管理者を名前で取得
        $top_management = $responsible->getManagementLists($client_id,$top_department);
        $management_lists = $responsible->getManagementLists($client_id,$departments);
       
        //上位階層取得
        $hierarchical = new Hierarchical();
        $department_high = $hierarchical->upperHierarchyName($departments);
        $personnel_high = $hierarchical->upperHierarchyName($names);
        
        //ツリーデータの取得
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        //クリックコードの保存
        session(['click_code'=>'bs']);

        return view('pacm01.pacm01',compact('top_management','management_lists',
        'top_department','top_responsible','departments','names','count_department',
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
     * ページネーションのページ数移動
     * 
     * @param  int  $client_id 顧客ID　9/27現在　ダミーデータ(route indexでセッション保存)
     * @param　int $count_department 部署ページネーションのページ数
     * @param　int $count_personnel  人員ページネーションのページ数
     * @param  array $top_department 最上位の部署データ
     * @param  array $department_data 部署データ
     * @param  array $personnel_data 人員データ
     * @param  App\Librarys\php\Pagination $pagination 
     * @param  int $department_max 部署データページネーションの最大値
     * @param  array $departments ページネーション後の部署データ
     * @param  int $personnel_max 人員データページネーションの最大値
     * @param  array $names ページネーション後の人員データ
     * @param  App\Librarys\php\ResponsiblePerson $responsible
     * @param  array $top_responsible 最上位の責任者データ
     * @param  array $responsible_lists 責任者リスト
     * @param  array $top_management 最上位の管理者データ
     * @param  array $management_lists 管理者データ
     * @param  App\Librarys\php\Hierarchical $hierarchical
     * @param  array $department_high 部署データの上位階層
     * @param  array $personnel_high 人員データの上位階層
     * @param  App\Http\Controllers\PtcmtrController $tree
     * @param  array $tree_data ツリーデータ
     *
     * @return \Illuminate\Http\Response
     */
    public function count(Request $request)
    {
        $client_id = session('client_id');
        $count_department = $_GET['department_page'];
        $count_personnel = $_GET['personnel_page'];

        //一番上の部署を取得
        try{
            $top_department = DB::select('select * from dcbs01 where client_id = ? limit 1',[$client_id]);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
        }
        
        //部署データの取得
        try{
            $department_data = DB::select('select * from dcbs01 inner join dccmks on dcbs01.department_id = dccmks.lower_id and dcbs01.client_id = ?',[$client_id]);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001','01');
            DatabaseException::common($e);
        }
        //人員データの取得
        try{
            $personnel_data = DB::select('select * from dcji01 inner join dccmks on dcji01.personnel_id = dccmks.lower_id and dcji01.client_id = ?',[$client_id]);
        }catch(\Exception $e){
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
            DatabaseException::common($e);
        }

        //ページネーション
        $pagination = new Pagination();
        $department_max = $pagination->pageMax($department_data,count($department_data));
        $departments = $pagination->pagination($department_data,count($department_data),$count_department);
        $personnel_max= $pagination->pageMax($personnel_data,count($personnel_data));
        $names = $pagination->pagination($personnel_data,count($personnel_data),$count_personnel);

        //責任者を名前で取得
        $responsible = new ResponsiblePerson();
        $top_responsible = $responsible->getResponsibleLists($client_id,$top_department);
        $responsible_lists = $responsible->getResponsibleLists($client_id,$departments);

        //管理者を名前で取得
        $top_management = $responsible->getManagementLists($client_id,$top_department);
        $management_lists = $responsible->getManagementLists($client_id,$departments);

        //上位階層取得
        $hierarchical = new Hierarchical();
        $department_high = $hierarchical->upperHierarchyName($departments);
        $personnel_high = $hierarchical->upperHierarchyName($names);
        
        //ツリーデータ取得
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        return view('pacm01.pacm01',compact('top_department','top_responsible','management_lists',
        'top_management','departments','names','count_department','count_personnel',
        'department_max','personnel_max','department_high',
        'personnel_high','responsible_lists'));
    }

    /**
     * 部署を絞り込みした後のページネーション
     *
     * @param  array $lists 選択したIDを格納するリスト
     * @param  array $department_data 部署データ
     * @param  array $personnel_data 人員データ
     * @param  int  $client 顧客ID　9/27現在　ダミーデータ
     * @param  int  $select_id 選択したID
     * @param　int $count_department 部署ページネーションのページ数
     * @param　int $count_personnel  人員ページネーションのページ数
     * @param  App\Librarys\php\Hierarchical $hierarchical
     * @param  array $select_lists 選択した部署の配下データ
     * @param  string $code 機能コード
     * @param  array  $data 取得した配下データ
     * @param  array  $department_data 部署データ
     * @param  array  $personnel_data 人員データ
     * @param  App\Librarys\php\Pagination $pagination 
     * @param  int $department_max 部署データページネーションの最大値
     * @param  array $departments ページネーション後の部署データ
     * @param  int $personnel_max 人員データページネーションの最大値
     * @param  array $names ページネーション後の人員データ
     * @param  App\Librarys\php\ResponsiblePerson $responsible
     * @param  array $top_responsible 最上位の責任者データ
     * @param  array $responsible_lists 責任者リスト
     * @param  array $top_management 最上位の管理者データ
     * @param  array $management_lists 管理者データ
     * @param  App\Librarys\php\Hierarchical $hierarchical
     * @param  array $department_high 部署データの上位階層
     * @param  array $personnel_high 人員データの上位階層
     * @param  App\Http\Controllers\PtcmtrController $tree
     * @param  array $tree_data ツリーデータ
     * 
     * @return \Illuminate\Http\Response
     */
    public function count2(Request $request)
    {
        $lists = [];
        $department_data = [];
        $personnel_data = [];
    
        //リクエストの取得
        $client = $_GET['id'];
        $select_id = $_GET['id2'];
        $count_department = $_GET['department_page'];
        $count_personnel = $_GET['personnel_page'];

        //選択したツリーで場合分け
        if(substr($select_id,0,2) == "bs"){

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
                        DatabaseException::common($e);
                    }
                array_push($department_data,$data[0]);

                }elseif($code == "ji"){
                    try{
                        $data = DB::select('select * from dcji01 inner join dccmks on dcji01.personnel_id = dccmks.lower_id where dcji01.client_id = ?
                        and dcji01.personnel_id = ?',[$client,$select_list]);
                    }catch(\Exception $e){
                        OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                        DatabaseException::common($e);
                    }
                   array_push($personnel_data,$data[0]);
                }else{

                }
            }
            //選択したデータ及び配下データを取得
            $lists = $hierarchical->subordinateGet($select_lists,$client);
            $department_data = $lists[0];
            $personnel_data = $lists[1];

            //ページネーション
            $pagination = new Pagination();
            $department_max = $pagination->pageMax($department_data,count($department_data));
            $departments = $pagination->pagination($department_data,count($department_data),$count_department);
            $personnel_max= $pagination->pageMax($personnel_data,count($personnel_data));
            $names = $pagination->pagination($personnel_data,count($personnel_data),$count_personnel);
            //責任者を名前で取得
            $responsible = new ResponsiblePerson();
            $responsible_lists = $responsible->getResponsibleLists($client,$departments);

            //管理者を名前で取得
            if(isset($departments)){
                $management_lists = $responsible->getManagementLists($client,$departments);
            }
            if(isset($names)){
                $personnel_management_lists = $responsible->getManagementLists($client,$names);
            }
       
            //上位階層取得
            $hierarchical = new Hierarchical();
            $department_high = $hierarchical->upperHierarchyName($departments);
            $personnel_high = $hierarchical->upperHierarchyName($names);
           
            $tree = new PtcmtrController();
            $tree_data = $tree->set_view_treedata();

            return view('pacm01.pacm01',compact('management_lists','departments','personnel_management_lists',
            'names','count_department','count_personnel','department_max','personnel_max','department_high',
            'personnel_high','responsible_lists','client','select_id'));
        }else{
            //選択した人員のデータを取得
            try{
                $click_personnel_data = DB::select('select * from dcji01 inner join dccmks on dcji01.personnel_id = dccmks.lower_id where dcji01.client_id = ?
                and dcji01.personnel_id = ?',[$client,$select_id]);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
            }

            //選択した人員の所属部署を取得
            try{
                $affiliation_data = DB::select('select high_id from dccmks where client_id = ?
                and lower_id = ?',[$client,$select_id]);
            }catch(\Exception $e){
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
            }
            //取得した部署IDを元に部署データを取得
            try{
                $data = DB::select('select * from dcbs01 inner join dccmks on dcbs01.department_id = dccmks.lower_id where dcbs01.client_id = ?
                and dcbs01.department_id = ?',[$client,$affiliation_data[0]->high_id]);
            }catch(\Exception $e){

                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
            }
            array_push($department_data,$data[0]);

            array_push($lists,$affiliation_data[0]->high_id);
            //取得した部署IDを元に配下を取得
            $hierarchical = new Hierarchical();
            $select_lists = $hierarchical->subordinateSearch($lists,$client);

            //選択したデータ及び配下データを取得
            $lists = $hierarchical->subordinateGet($select_lists,$client);
            $department_data = $lists[0];
            $personnel_data = $lists[1];
        }

        //ページネーション
        $pagination = new Pagination();
        $department_max = $pagination->pageMax($department_data,count($department_data));
        $departments = $pagination->pagination($department_data,count($department_data),$count_department);
        $personnel_max= $pagination->pageMax($personnel_data,count($personnel_data));
        $names = $pagination->pagination($personnel_data,count($personnel_data),$count_personnel);

        //責任者を名前で取得
        $responsible = new ResponsiblePerson();
        $responsible_lists = $responsible->getResponsibleLists($client,$departments);

        //管理者を名前で取得
        if(isset($departments)){
            $management_lists = $responsible->getManagementLists($client,$departments);
        }
        if(isset($names)){
            $personnel_management_lists = $responsible->getManagementLists($client,$names);
        }
        if(isset($click_personnel_data)){
            $click_management_lists = $responsible->getManagementLists($client,$click_personnel_data);
        }

        //上位階層取得
        $hierarchical = new Hierarchical();
        $department_high = $hierarchical->upperHierarchyName($departments);
        $personnel_high = $hierarchical->upperHierarchyName($names);
        
        //ツリーデータ取得
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        return view('pacm01.pacm01',compact('management_lists','personnel_management_lists','departments','names','count_department','count_personnel',
        'department_max','personnel_max','department_high','click_management_lists',
        'personnel_high','responsible_lists','client','select_id','click_personnel_data'));
    }

    /**
     * 選択した部署のIDを複写するメソッド
     * 
     * @param $id 選択したID
     * 
     * @return \Illuminate\Http\Response
     */ 
    public function clipboard($id){

        session(['clipboard_id'=>$id]);

        return back();
    }

    /**
     * 複写したIDを削除するメソッド
     * 
     * @return \Illuminate\Http\Response
     */
    public function deleteClipboard(){

        session()->forget('clipboard_id');

        return back();
    }
}
