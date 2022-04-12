<?php

namespace App\Http\Controllers;

use App\Models\Date;
use App\Facades\OutputLog;
use App\Http\Controllers\PtcmtrController;
use App\Http\Requests\WorkSpaceRequest;
use App\Libraries\php\Domain\DepartmentDataBase;
use App\Libraries\php\Domain\PersonnelDataBase;
use App\Libraries\php\Domain\ProjectionDataBase;
use App\Libraries\php\Domain\WorkSpaceDataBase;
use App\Libraries\php\Domain\Hierarchical;
use App\Libraries\php\Logic\JudgmentHierarchy;
use App\Libraries\php\Logic\ResponsiblePerson;
use App\Libraries\php\Service\DatabaseException;
use App\Libraries\php\Service\Message;
use App\Libraries\php\Service\OperationCheck;
use App\Libraries\php\Service\Pagination;
use App\Libraries\php\Service\ZeroPadding;
use Hamcrest\Type\IsString;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class Pssb01Controller extends Controller
{
    /**
     * 作業場所トップを表示するメソッド
     *
     */
    public function index()
    {
        //ログインしている顧客IDの取得
        $client_id = session('client_id');
        if (isset($_GET['count'])) {
            $count_space = $_GET['count'];
        } else {
            $count_space = Config::get('startcount.count');
        }

        //ツリーデータの取得
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        //DBインスタンス化
        $department_db = new DepartmentDataBase();
        $personnel_db = new PersonnelDataBase();
        $space_db = new WorkSpaceDataBase();

        //部署DB
        //一番上の部署を取得
        $top_department = $department_db->getTop($client_id);
        //全体の部署データの取得
        $department_data = $department_db->getAll($client_id);

        //人員DB
        //全体の人員データの取得
        $personnel_data = $personnel_db->getAll($client_id);

        //作業場所DB
        //一覧に記載する作業場所データの取得
        $space_data = $space_db->getAll($client_id);
        $top_space = $space_db->getTop($client_id);

        //日付フォーマット変更
        $date = new Date();
        $operation_date = $date->formatOperationDate($top_department);
        $date->formatDate($department_data);
        $date->formatDate($personnel_data);

        //基本ページネーション設定
        $pagination = new Pagination();
        $count_department = Config::get('startcount.count');
        $count_personnel = Config::get('startcount.count');
        $count_space = Config::get('startcount.count');
        $department_max = $pagination->pageMax($department_data, count($department_data));
        $departments = $pagination->pagination($department_data, count($department_data), $count_department);
        $personnel_max = $pagination->pageMax($personnel_data, count($personnel_data));
        $names = $pagination->pagination($personnel_data, count($personnel_data), $count_personnel);
        $space_max = $pagination->pageMax($space_data, count($space_data));
        $spaces = $pagination->pagination($space_data, count($space_data), $count_space);

        //ページネーションの最大値・最小値チェック
        if ($count_space < Config::get('startcount.count')) {
            $count_space = Config::get('startcount.count');
        }
        if ($count_space > $space_max) {
            $count_space = $space_max;
        }

        //責任者を名前で取得
        $responsible = new ResponsiblePerson();
        $top_responsible = $responsible->getResponsibleLists($client_id, $top_department);
        $responsible_lists = $responsible->getResponsibleLists($client_id, $departments);

        //上位階層取得
        $hierarchical = new Hierarchical();
        $department_high = $hierarchical->upperHierarchyName($departments);
        $personnel_high = $hierarchical->upperHierarchyName($names);

        return view('pvsb01.pvsb01', compact(
            'department_max',
            'departments',
            'personnel_max',
            'names',
            'top_department',
            'top_responsible',
            'top_space',
            'count_department',
            'responsible_lists',
            'department_high',
            'personnel_high',
            'department_data',
            'spaces',
            'count_personnel',
            'personnel_data',
            'operation_date',
            'space_data',
            'space_max',
            'count_space',
        ));
    }

    /**
     * 作業場所新規登録画面表示
     *
     */
    public function create()
    {
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();
        return view('pssb01.pssb01');
    }

    /**
     * 作業場所の新規登録
     *
     */
    public function store(WorkSpaceRequest $request)
    {
        //リクエストの取得
        $client_id = $request->client_id;
        $space_id = $request->space_id;
        $name = $request->name;
        $management_personnel_id = $request->management_number;
        $post_code = $request->postcode;
        $prefectural_office_location = $request->prefectural;
        $address = $request->address;
        $URL = $request->URL;
        $high = $request->high;
        $remarks = $request->remarks;

        // 重複クリック対策
        $request->session()->regenerateToken();

        //顧客IDに対応した最新の作業場所IDを取得
        $space_db = new WorkSpaceDataBase();
        $id = $space_db->getId($client_id);

        //登録する番号を作成
        if (empty($id)) {
            $space_id = "sb00000001";
        } else {
            $padding = new ZeroPadding();
            $space_id = $padding->padding($id[0]->space_id);
        }

        //データベースに作業場所を登録
        try {
            $space_db->insert(
                $client_id,
                $space_id,
                $name,
                $management_personnel_id,
                $post_code,
                $prefectural_office_location,
                $address,
                $URL,
                $remarks,
            );
        } catch (\Exception $e) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
            DatabaseException::common($e);
            return redirect()->route('pssb01.index');
        }

        //データベースに階層情報を登録
        try {
            $hierarchical = new Hierarchical();
            $hierarchical->insert($client_id, $space_id, $high);
        } catch (\Exception $e) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
            DatabaseException::common($e);
        }
        OutputLog::message_log(__FUNCTION__, 'mhcmok0001');

        //メッセージの表示
        $request->session()->put('message', Config::get('message.mhcmok0001'));
        PtcmtrController::open_node($space_id);

        return redirect()->route('pssb01.show', [$client_id, $high]);
    }

    /**
     * 作業場所の表示
     *
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($client_id, $select_id)
    {
        //ログインしている顧客IDの取得
        $client_id = session('client_id');

        //ツリーデータの取得
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        View::share('click_id', $select_id);

        if (substr($select_id, 0, 2) == "ta") {
            //選択部署が投影だった場合は対応するIDを取得
            $projection_db = new ProjectionDataBase();
            $projection_code = $projection_db->getId($select_id);
            $select_id = $projection_code[0]->projection_source_id;
        }

        //詳細に記載する作業場所データの取得
        $space_db = new WorkSpaceDataBase();
        $click_space_data = $space_db->get($client_id, $select_id);

        //一覧に記載する作業場所データの取得
        $space_data = $space_db->getList($client_id, $select_id);

        //一覧の投影部署データの取得
        try {
            $projection_db = new ProjectionDataBase();
            $projection_space = $projection_db->getSpaceList($client_id, $select_id);
        } catch (\Exception $e) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }

        //投影データを一覧に追加
        $space_data = array_merge($space_data, $projection_space);

        //システム管理者のリストを取得
        try {
            $personnel_db = new PersonnelDataBase();
            $system_management_lists = $personnel_db->getSystemManagement($client_id);
        } catch (\Exception $e) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }

        //ページネーション設定
        $pagination = new Pagination();
        if (isset($_GET['count'])) {
            $count_space = $_GET['count'];
        } else {
            $count_space = Config::get('startcount.count');
        }
        $space_max = $pagination->pageMax($space_data, count($space_data));

        //ページネーションの最大値・最小値チェック
        if ($count_space < Config::get('startcount.count')) {
            $count_space = Config::get('startcount.count');
        }
        if ($count_space > $space_max) {
            $count_space = $space_max;
        }

        $spaces = $pagination->pagination($space_data, count($space_data), $count_space);

        return view('pssb01.pssb02', compact(
            'click_space_data',
            'space_data',
            'system_management_lists',
            'count_space',
            'space_max',
            'spaces',
        ));
    }

    /**
     * 検索
     *
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request, $client_id, $select_id)
    {
        //ログインしている顧客IDの取得
        $client_id = session('client_id');

        //ページネーションの番号チェック
        if (isset($_GET['count'])) {
            $count_space = $_GET['count'];
        } else {
            $count_space = Config::get('startcount.count');
        }

        //検索語のチェック
        if (isset($_GET['search'])) {
            $_POST['search'] = $_GET['search'];
        }

        $select_code = substr($select_id, 0, 2);
        View::share('click_id', $select_id);

        if ($select_code == "ta") {
            //選択部署が投影だった場合は対応するIDを取得
            $projection_db = new ProjectionDataBase();
            $projection_code = $projection_db->getId($select_id);
            $select_id = $projection_code[0]->projection_source_id;
            $select_code = substr($projection_code[0]->projection_source_id, 0, 2);
        }

        //詳細に記載する作業場所データの取得
        $space_db = new WorkSpaceDataBase();
        $click_space_data = $space_db->get($client_id, $select_id);

        //一覧に記載する作業場所データの取得
        try {
            if ($select_id == 'sb00000000') {
                $space_data = $space_db->getSearchTop($client_id, $request->search);
            } else {
                $space_data = $space_db->getSearchList($client_id, $select_id, $request->search);
            }
        } catch (\Exception $e) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');

            return redirect()->route('pa0001.errormsg');
        }

        //一覧の投影部署データの取得
        try {
            $projection_db = new ProjectionDataBase();
            $projection_space = $projection_db->getSpaceList($client_id, $select_id);
        } catch (\Exception $e) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }

        //投影データを一覧に追加
        $space_data = array_merge($space_data, $projection_space);

        if (empty($space_data)) {
            OutputLog::message_log(__FUNCTION__, 'mhcmwn0001');
            $message = Message::get_message_handle('mhcmwn0001', [0 => '']);
            session(['message' => $message[0], 'handle_message' => $message[3]]);
            if ($select_id == 'sb00000000') {
                return redirect()->route('pssb01.index');
            }
            return redirect()->route('pssb01.show', [$client_id, $select_id]);
        }

        //システム管理者のリストを取得
        try {
            $personnel_db = new PersonnelDataBase();
            $system_management_lists = $personnel_db->getSystemManagement($client_id);
        } catch (\Exception $e) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }

        //基本ページネーション設定
        $pagination = new Pagination();
        $space_max = $pagination->pageMax($space_data, count($space_data));

        if ($count_space < Config::get('startcount.count')) {
            $count_space = Config::get('startcount.count');
        }
        if ($count_space > $space_max) {
            $count_space = $space_max;
        }

        $spaces = $pagination->pagination($space_data, count($space_data), $count_space);

        //ツリーデータの取得
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        if ($select_id == 'sb00000000') {
            return view('pvsb01.pvsb01', compact(
                'space_data',
                'space_max',
                'count_space',
                'spaces',
            ));
        }

        return view('pssb01.pssb02', compact(
            'click_space_data',
            'space_data',
            'system_management_lists',
            'count_space',
            'space_max',
            'spaces',
        ));
    }

    /**

     * 作業場所情報の更新
     * Update the specified resource in storage.
     *
     */
    public function update(Request $request)
    {
        //リクエストの取得
        $client_id = $request->client_id;
        $space_id = $request->space_id;
        $name = $request->name;
        $management_number = $request->management_number;
        $post_code = $request->postcode;
        $prefectural_office_location = $request->prefectural;
        $address = $request->address;
        $URL = $request->URL;
        $remarks = $request->remarks;

        // 重複クリック対策
        $request->session()->regenerateToken();

        //入力されたIDの作業場所が存在するかの確認
        $space_db = new WorkSpaceDataBase();
        $management_personnel_id = $space_db->get($client_id, $space_id);

        if ($management_personnel_id == null) {
            return redirect()->route('pssb01.index');
        }

        //情報の更新
        try {
            $space_db->update(
                $client_id,
                $space_id,
                $name,
                $management_number,
                $post_code,
                $prefectural_office_location,
                $address,
                $URL,
                $remarks,
            );
        } catch (\Exception $e) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
            DatabaseException::common($e);
            return redirect()->route('pssb01.index');
        }

        //ログ処理
        OutputLog::message_log(__FUNCTION__, 'mhcmok0002');
        $message = Message::get_message('mhcmok0002', [0 => '']);
        session(['message' => $message[0]]);

        return back();
    }




    /**
     * 作業場所の削除
     * Remove the specified resource from storage.
     *
     */
    public function destroy($client, $delete)
    {
        //選択の作業場所IDをarray型に格納
        $lists = [];
        $delete_id = [];
        array_push($lists, $delete);

        //インスタンス化
        $space_db = new WorkSpaceDataBase();
        $projection_db = new ProjectionDataBase();
        $hierarchical = new Hierarchical();

        //選択した作業場所を取得
        $delete_data = $space_db->get($client, $delete);

        if (empty($delete_data)) {
            $delete_data = $space_db->getClickTop($client, $delete);
            $delete_data[0]->high_id = "sb";
        }

        $delete_lists = $hierarchical->subordinateSearchRoop($lists, $client, $delete_id);

        //削除リストの作成
        array_unshift($delete_lists, $delete);

        //選択したデータ及び配下データを削除
        if (!empty($delete_lists)) {
            try {
                //トランザクション
                DB::beginTransaction();

                foreach ($delete_lists as $delete_list) {
                    //機能コードの判定
                    $code = substr($delete_list, 0, 2);

                    //対応したデータの削除
                    if ($code == "sb") {
                        $space_db->delete($client, $delete_list);

                        //削除予定の配下作業場所が元になった投影を削除
                        $delete_projections = $projection_db->getProjectionId($client, $delete_list);
                        foreach ($delete_projections as $delete_projection) {
                            $hierarchical->delete($client, $delete_projection->projection_id);
                            $projection_db->delete($client, $delete_projection->projection_id);
                        }
                        $projection_db->delete($client, $delete_list);
                    } elseif ($code == "ta") {
                        //投影の削除
                        $projection_db->delete($client, $delete_list);
                    } else {
                        //データの階層構造を削除
                        $hierarchical->delete($client, $delete_list);
                    }
                    DB::commit();
                }
            } catch (\Exception $e) {
                //ロールバック
                DB::rollBack();
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
                DatabaseException::common($e);
                return redirect()->route('pssb01.index');
            }
        }

        //ログ処理
        OutputLog::message_log(__FUNCTION__, 'mhcmok0003');
        $message = Message::get_message('mhcmok0003', [0 => '']);
        session(['message' => $message[0]]);

        PtcmtrController::delete_node($delete_data[0]->high_id);

        if (!isset($delete_data[0]->high_id)) {
            return redirect()->route('pssb01.index');
        }
        return redirect()->route('pssb01.show', [$client, $delete_data[0]->high_id]);
    }



    /**
     * 複製したデータを挿入するメソッド
     * @param  \Illuminate\Http\Request  $request
     *
     */
    public function copy(Request $request)
    {
        //リクエストの取得
        $client_id = $request->client_id;
        $copy_id = $request->copy_id;
        $high = $request->high_id;

        // 重複クリック対策
        $request->session()->regenerateToken();

        //インスタンス化
        $space_db = new WorkSpaceDataBase();
        $projection_db = new ProjectionDataBase();
        $hierarchical = new Hierarchical();

        $date = new Date();

        //複写番号が空白の場合エラーメッセージを表示
        if ($request->copy_id == null) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0009', '01');
            $message = Message::get_message_handle('mhcmer0009', [0 => '']);
            session(['message' => $message[0], 'handle_message' => $message[3]]);
            return redirect()->route('pssb01.index');
        }

        //投影を複製する場合
        if (substr($copy_id, 0, 2) == "ta") {
            try {
                $code = $projection_db->getId($copy_id);
            } catch (\Exception $e) {
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('pssb01.index');
            }
            $projection_source_id = $code[0]->projection_source_id;

            //人員の配下に部署を複製しないように分岐
            if (substr($projection_source_id, 0, 2) == "sb") {
                //エラーメッセージ表示
                OutputLog::message_log(__FUNCTION__, 'mhcmer0010');
                $message = Message::get_message_handle('mhcmer0010', [0 => '']);
                session(['message' => $message[0], 'handle_message' => $message[3]]);
                return redirect()->route('pssb01.index');
            }

            //最新の投影番号を生成
            try {
                $projection_id = $projection_db->getNewId($client_id);
            } catch (\Exception $e) {
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('pssb01.index');
            }

            //データベースに投影情報を登録
            try {
                //トランザクション
                DB::beginTransaction();

                //データベースに投影情報を登録
                $projection_db->insert($client_id, $projection_id, $projection_source_id);
                //データベースに階層情報を登録
                $hierarchical->insert($client_id, $projection_id, $high);

                DB::commit();
            } catch (\Exception $e) {
                //ロールバック
                DB::rollBack();
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
            }
            //ログ処理
            OutputLog::message_log(__FUNCTION__, 'mhcmok0009');
            $message = Message::get_message('mhcmok0009', [0 => '']);
            session(['message' => $message[0]]);
            return back();
        } else {
            //複製する人員情報の取得
            try {
                $copy_space = $space_db->getData($client_id, $copy_id);
            } catch (\Exception $e) {
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
                DatabaseException::common($e);
                return redirect()->route('pssb01.index');
            }

            //顧客IDに対応した最新の人員IDを取得
            try {
                $space_id = $space_db->getNewId($client_id);
            } catch (\Exception $e) {
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
                DatabaseException::common($e);
                return redirect()->route('pssb01.index');
            }

            //データベースに登録
            try {
                //トランザクション
                DB::beginTransaction();

                //データベースに人員情報を登録
                $space_db->copy(
                    $client_id,
                    $space_id,
                    $copy_space[0]->name,
                    $copy_space[0]->management_personnel_id,
                    $copy_space[0]->post_code,
                    $copy_space[0]->prefectural_office_location,
                    $copy_space[0]->address,
                    $copy_space[0]->URL,
                    $copy_space[0]->remarks
                );


                //データベースに階層情報を登録
                $hierarchical->insert($client_id, $space_id, $high);

                DB::commit();
            } catch (\Exception $e) {
                //ロールバック
                DB::rollBack();
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
                DatabaseException::common($e);
                return redirect()->route('pssb01.index');
            }
            //ツリー開閉
            PtcmtrController::open_node($client_id);

            //ログ処理
            OutputLog::message_log(__FUNCTION__, 'mhcmok0009');
            $message = Message::get_message('mhcmok0009', [0 => '']);
            session(['message' => $message[0]]);
            return back();
        }
    }
}
