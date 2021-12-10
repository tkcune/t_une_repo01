<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Libraries\php\DatabaseException;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use App\Libraries\php\Pagination;
use App\Libraries\php\Hierarchical;
use App\Http\Controllers\PtcmtrController;
use App\Libraries\php\OutputLog;
use DateTime;
use App\Models\Psnw;

class PsnwController extends Controller
{
    public function index(Request $request)
    {

        //$client_idの数値はダミー(ログイン時にセッション保存する予定)
        $client_id = "aa00000002";
        session(['client_id' => $client_id]);

        $count_department = Config::get('startcount.count');
        $count_personnel = Config::get('startcount.count');

        //一番上の部署を取得
        try {
            $top_department = DB::select('select * from dcbs01 where client_id = ? limit 1', [$client_id]);
        } catch (\Exception $e) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
            DatabaseException::common($e);
        }

        //全体の部署データの取得
        try {
            $department_data = DB::select('select 
            dcbs01.client_id, department_id,responsible_person_id,name,status,management_personnel_id,operation_start_date,operation_end_date,lower_id, high_id, dcbs01.created_at, dcbs01.updated_at
            from dcbs01 inner join dccmks on dcbs01.department_id = dccmks.lower_id and dcbs01.client_id = ?', [$client_id]);
        } catch (\Exception $e) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
            DatabaseException::common($e);
        }

        //全体の人員データの取得
        try {
            $personnel_data = DB::select('select 
            dcji01.client_id ,personnel_id,name,email,password,password_update_day,status,management_personnel_id,login_authority,system_management,operation_start_date,operation_end_date,dcji01.created_at, dcji01.updated_at ,high_id ,lower_id
            from dcji01 inner join dccmks on dcji01.personnel_id = dccmks.lower_id and dcji01.client_id = ?', [$client_id]);
        } catch (\Exception $e) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
            DatabaseException::common($e);
        }



        //基本ページネーション設定
        $pagination = new Pagination();
        $department_max = $pagination->pageMax($department_data, count($department_data));
        $departments = $pagination->pagination($department_data, count($department_data), $count_department);
        $personnel_max = $pagination->pageMax($personnel_data, count($personnel_data));
        $names = $pagination->pagination($personnel_data, count($personnel_data), $count_personnel);



        //上位階層取得
        $hierarchical = new Hierarchical();
        try {
            $department_high = $hierarchical->upperHierarchyName($departments);
            $personnel_high = $hierarchical->upperHierarchyName($names);
        } catch (\Exception $e) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
            DatabaseException::dataCatchMiss($e);
            return redirect()->route('errormsg');
        }

        // ツリーデータの取得
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        //クリックコードの保存
        session(['click_code' => 'bs']);


        return view('psnw01/psnw01', compact(
            'department_max',
            'departments',
            'personnel_max',
            'names',
            'top_department',
            'count_department',
            'department_high',
            'personnel_high',
            'count_personnel',
            'personnel_data'
        ));
    }

    public function create(Request $request)
    {
       
        // ネットワーク設定のフォームから値を取得
        $client_id = "aa00000001";
        $name = $request->name;
        $email = $request->email;
        $password = $request->password;
        $recieving_server = $request->recieving_server;
        $recieving_server_way = $request->recieving_server_way;
        $recieving_port_number = $request->recieving_port_number;
        $sending_server = $request->sending_server;
        $sending_server_way = $request->sending_server_way;
        $sending_port_number = $request->sending_port_number;
        $date = new DateTime();
        $created_at = $date->format('Y-m-d');

        $network_datas = [
            'client_id' => $client_id,
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'recieving_server' => $recieving_server,
            'recieving_server_way' => $recieving_server_way,
            'recieving_port_number' => $recieving_port_number,
            'sending_server' => $sending_server,
            'sending_server_way' => $sending_server_way,
            'sending_port_number' => $sending_port_number,
            'created_at' => $created_at,
        ];

        
        $psnw = new Psnw();
        $psnw->get_data($network_datas);
        
        return redirect('psnw01');
    }
}
