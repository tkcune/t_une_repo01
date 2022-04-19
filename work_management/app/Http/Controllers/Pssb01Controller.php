<?php

namespace App\Http\Controllers;

use App\Facades\OutputLog;
use App\Http\Controllers\PtcmtrController;
use App\Libraries\php\Domain\DepartmentDataBase;
use App\Libraries\php\Domain\PersonnelDataBase;
use App\Libraries\php\Domain\ProjectionDataBase;
use App\Libraries\php\Domain\WorkSpaceDataBase;
use App\Libraries\php\Domain\Hierarchical;
use App\Libraries\php\Service\DatabaseException;
use App\Libraries\php\Service\Message;
use App\Libraries\php\Service\Pagination;
use App\Libraries\php\Service\ZeroPadding;
use Illuminate\Http\Request;
use App\Http\Requests\WorkSpaceRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

/**
 * 作業場所データを操作するコントローラー
 */
class Pssb01Controller extends Controller
{
    /**
     * 作業場所トップ画面
     *
     * @var  string $client_id 顧客ID
     * @var  App\Http\Controllers\PtcmtrController $tree
     * @var  array $tree_data ツリーデータ
     * @var  App\Libraries\php\Domain\DepartmentDataBase $department_db
     * @var  App\Libraries\php\Domain\PersonnelDataBase $personnel_db
     * @var  App\Libraries\php\Domain\WorkSpaceDataBase $space_db
     * @var  array $department_data 部署一覧データ
     * @var  array $personnel_data 人員一覧データ
     * @var  array $space_data 作業場所一覧データ
     * @var  App\Libraries\php\Service\Pagination $pagination
     * @var int $count_department 部署のページ番号
     * @var int $count_personnel　人員のページ番号
     * @var int $count_space　作業場所のページ番号
     * @var  int $department_max 部署データページネーションの最大値
     * @var  array $departments 一覧に表示する部署データ
     * @var  int $personnel_max 人員データページネーションの最大値
     * @var  array $names 一覧に表示する人員データ
     * @var  int $space_max 作業場所データページネーションの最大値
     * @var  array $spaces 一覧に表示する作業場所データ
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //ログインしている顧客IDの取得
        $client_id = session('client_id');

        //ツリーデータの取得
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        //全体の部署データの取得
        $department_db = new DepartmentDataBase();
        $department_data = $department_db->getList($client_id);

        //全体の人員データの取得
        $personnel_db = new PersonnelDataBase();
        $personnel_data = $personnel_db->getList($client_id);

        //一覧に記載する作業場所データの取得
        $space_db = new WorkSpaceDataBase();
        $space_data = $space_db->getList($client_id);

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

        return view('pvsb01.pvsb01', compact(
            'department_max',
            'departments',
            'personnel_max',
            'names',
            'space_max',
            'spaces',
            'department_data',
            'personnel_data',
            'space_data',
            'count_department',
            'count_personnel',
            'count_space',
        ));
    }

    /**
     * 作業場所新規登録画面表示
     *
     * @var $client_id 顧客ID
     * @var  App\Http\Controllers\PtcmtrController $tree
     * @var  array $tree_data ツリーデータ
     * @var  App\Libraries\php\Domain\PersonnelDataBase $personnel_db
     * @var  array $system_managment_lists システム管理者リスト
     *
     */
    public function create()
    {
        //ログインしている顧客IDの取得
        $client_id = session('client_id');

        //システム管理者のリストを取得
        try {
            $personnel_db = new PersonnelDataBase();
            $system_management_lists = $personnel_db->getSystemManagement($client_id);
        } catch (\Exception $e) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }

        //ツリーデータの取得
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        return view('pssb01.pssb01', compact('system_management_lists'));
    }

    /**
     * 作業場所の新規登録
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @var $client_id　顧客ID
     * @var $name 作業場所名称
     * @var $management_pesonnel_id 管理者ID
     * @var $post_code 郵便番号
     * @var $prefectural_office_location　都道府県
     * @var $address 市区町村
     * @var $URL 地図URL
     * @var $high 上位ID
     * @var $remarks 備考
     * @var App\Libraries\php\Domain\WorkSpaceDataBase $space_db
     * @var $id 現時点でDBに存在している最新のID
     * @var $space_id 最新の作業場所ID
     * @var App\Libraries\php\Domain\Hierarchical $hierarchical
     *
     * @return \Illuminate\Http\Response
     */
    public function store(WorkSpaceRequest $request)
    {
        //リクエストの取得
        $client_id = session('client_id');
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
            DB::beginTransaction();
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

            //データベースに階層情報を登録
            $hierarchical = new Hierarchical();
            $hierarchical->insert($client_id, $space_id, $high);

            DB::commit();

            return redirect()->route('pssb01.show', [$client_id, $high]);
        } catch (\Exception $e) {
            DB::rollBack();
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
            DatabaseException::common($e);
            return redirect()->route('pssb01.index');
        }
    }

    /**
     * 作業場所の表示
     *
     * @param  int  $client_id 顧客ID
     * @param  int  $select_id 選択ID
     *
     * @var App\Http\Controllers\PtcmtrController $tree
     * @var array $tree_data ツリーデータ
     * @var App\Libraries\php\Domain\WorkSpaceDataBase $space_db
     * @var array $space_details 作業場所詳細データ
     * @var array $space_data 作業場所一覧データ
     * @var App\Libraries\php\Domain\PersonnelDataBase $personnel_db
     * @var $system_management_lists システム管理者リスト
     * @var  App\Libraries\php\Domain\ProjectionDataBase $projection_db
     * @var array  $projection_space 一覧の投影作業場所データ
     * @var  App\Libraries\php\Service\Pagination $pagination
     * @var int $count_space　作業場所のページ番号
     * @var  int $space_max 作業場所データページネーションの最大値
     * @var  array $spaces 一覧に表示する作業場所データ
     *
     * @return \Illuminate\Http\Response
     */
    public function show($client_id, $select_id)
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

        //インスタンス化
        $space_db = new WorkSpaceDataBase();
        $department_db = new DepartmentDataBase();
        $personnel_db = new PersonnelDataBase();
        $projection_db = new ProjectionDataBase();

        View::share('click_id', $select_id);

        if (substr($select_id, 0, 2) == "ta") {
            //選択部署が投影だった場合は対応するIDを取得
            $projection_code = $projection_db->getId($select_id);
            $select_id = $projection_code[0]->projection_source_id;
        }

        //詳細に記載する作業場所データの取得
        $space_details = $space_db->get($client_id, $select_id);

        //一覧に記載する作業場所データの取得
        $space_data = $space_db->getSelectList($client_id, $select_id);

        //一覧の投影作業場所データの取得
        try {
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
            $system_management_lists = $personnel_db->getSystemManagement($client_id);
        } catch (\Exception $e) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }

        //ページネーション設定
        $pagination = new Pagination();
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
            'space_details',
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
     * @param  int  $client_id 顧客ID
     * @param  int  $select_id 選択ID
     *
     * @var App\Http\Controllers\PtcmtrController $tree
     * @var array $tree_data ツリーデータ
     * @var  App\Libraries\php\Domain\DepartmentDataBase $department_db
     * @var  App\Libraries\php\Domain\PersonnelDataBase $personnel_db
     * @var  App\Libraries\php\Domain\WorkSpaceDataBase $space_db
     * @var array $space_details 作業場所詳細データ
     * @var array $space_data 作業場所一覧データ
     * @var array  $projection_space 一覧の投影作業場所データ
     * @var $system_management_lists システム管理者リスト
     * @var $personnel_data 人員一覧データ
     * @var projection_personnel 一覧の投影人員データ
     * @var  App\Libraries\php\Service\Pagination $pagination
     * @var int $count_department 部署のページ番号
     * @var int $count_personnel　人員のページ番号
     * @var int $count_space　作業場所のページ番号
     * @var  int $department_max 部署データページネーションの最大値
     * @var  array $departments 一覧に表示する部署データ
     * @var  int $personnel_max 人員データページネーションの最大値
     * @var  array $names 一覧に表示する人員データ
     * @var  int $space_max 作業場所データページネーションの最大値
     * @var  array $spaces 一覧に表示する作業場所データ
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request, $client_id, $select_id)
    {
        //ログインしている顧客IDの取得
        $client_id = session('client_id');

        //ツリーデータの取得
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

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

        if (isset($_GET['search'])) {
            $_POST['search2'] = $_GET['search'];
        }

        //インスタンス化
        $space_db = new WorkSpaceDataBase();
        $department_db = new DepartmentDataBase();
        $personnel_db = new PersonnelDataBase();
        $projection_db = new ProjectionDataBase();

        $select_code = substr($select_id, 0, 2);
        View::share('click_id', $select_id);

        if ($select_code == "ta") {
            //選択部署が投影だった場合は対応するIDを取得
            $projection_code = $projection_db->getId($select_id);
            $select_id = $projection_code[0]->projection_source_id;
            $select_code = substr($projection_code[0]->projection_source_id, 0, 2);
        }

        //詳細に記載する作業場所データの取得
        $space_details = $space_db->get($client_id, $select_id);

        //一覧に記載する作業場所データの取得
        try {
            if ($select_id == 'sb00000000') {
                $space_data = $space_db->getSearchTop($client_id, $request->search);
            } else {
                $space_data = $space_db->getSearchList($client_id, $select_id, $request->search);
            }
        } catch (\Exception $e) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }

        //一覧の投影作業場所データの取得
        try {
            $projection_space = $projection_db->getSpaceList($client_id, $select_id);
        } catch (\Exception $e) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }

        //投影データを一覧に追加
        $space_data = array_merge($space_data, $projection_space);

        //検索結果が0件なら戻る
        if (empty($space_data)) {
            OutputLog::message_log(__FUNCTION__, 'mhcmwn0001');
            $message = Message::get_message_handle('mhcmwn0001', [0 => '']);
            session(['message' => $message[0], 'handle_message' => $message[3]]);
            if ($select_id == 'sb00000000') {
                return redirect()->route('pssb01.index');
            }
            return redirect()->route('pssb01.show', [$client_id, $select_id]);
        }

        //概要画面での人員検索表示に必要な内容
        try {
            if ($select_id == 'sb00000000') {
                //一覧に記載する人員データの取得
                $personnel_data = $personnel_db->getSearchTop($client_id, $request->search2);
                //一覧の投影人員データの取得
                $projection_personnel = $projection_db->getPersonnelSearch($client_id, $select_id, $request->search2);
                //投影データを一覧人員に追加
                $personnel_data = array_merge($personnel_data, $projection_personnel);
                //検索結果が0件なら戻る
                if (empty($personnel_data)) {
                    OutputLog::message_log(__FUNCTION__, 'mhcmwn0001');
                    $message = Message::get_message_handle('mhcmwn0001', [0 => '']);
                    session(['message' => $message[0], 'handle_message' => $message[3]]);
                    return redirect()->route('plbs01.show', [$client_id, $select_id]);
                }
                //ページネーション
                $pagination = new Pagination();
                $count_personnel = Config::get('startcount.count');
                $count_department = Config::get('startcount.count');
                $personnel_max = $pagination->pageMax($personnel_data, count($personnel_data));
                $names = $pagination->pagination($personnel_data, count($personnel_data), $count_personnel);
                $department_data = $department_db->getList($client_id);
                $departments = $pagination->pagination($department_data, count($department_data), $count_department);
            }
        } catch (\Exception $e) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
            DatabaseException::common($e);
            echo "エラー：" . $e->getMessage();
        }

        //システム管理者のリストを取得
        try {
            $system_management_lists = $personnel_db->getSystemManagement($client_id);
        } catch (\Exception $e) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
            DatabaseException::common($e);
            return redirect()->route('pa0001.errormsg');
        }

        //基本ページネーション設定
        $pagination = new Pagination();
        $space_max = $pagination->pageMax($space_data, count($space_data));
        $spaces = $pagination->pagination($space_data, count($space_data), $count_space);

        if ($count_space < Config::get('startcount.count')) {
            $count_space = Config::get('startcount.count');
        }
        if ($count_space > $space_max) {
            $count_space = $space_max;
        }

        if ($select_id == 'sb00000000') {
            return view('pvsb01.pvsb01', compact(
                'space_data',
                'space_max',
                'count_space',
                'spaces',
                'department_data',
                'count_department',
                'departments',
                'personnel_data',
                'count_personnel',
                'personnel_max',
                'names',
            ));
        }

        return view('pssb01.pssb02', compact(
            'space_details',
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
     * @param  \Illuminate\Http\Request  $request
     * @param  int $space_id 作業場所ID
     *
     * @var $client_id　顧客ID
     * @var $name 作業場所名称
     * @var $management_pesonnel_id 管理者ID
     * @var $post_code 郵便番号
     * @var $prefectural_office_location　都道府県
     * @var $address 市区町村
     * @var $URL 地図URL
     * @var $remarks 備考
     * @var App\Libraries\php\Domain\PersonnelDataBase $personnel_db
     * @var $management_personnel_id 管理者人員ID
     * @var App\Libraries\php\Domain\WorkSpaceDataBase $space_db
     *
     * @return \Illuminate\Http\Response
     */
    public function update(WorkSpaceRequest $request)
    {
        //リクエストの取得
        $client_id = session('client_id');
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
        $personnel_db = new PersonnelDataBase();
        $management_personnel_id = $personnel_db->getData($client_id, $management_number);

        if ($management_personnel_id == null) {
            return redirect()->route('pssb01.index');
        }

        //情報の更新
        try {
            $space_db = new WorkSpaceDataBase();
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
            //エラー処理
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
     * @param  int  $delete　削除ID
     *
     * @var  $client_id 顧客ID
     * @var  array $lists 削除予定のIDを格納した配列
     * @var  array $delete_id 選択作業場所の配下の削除予定のID
     * @var  array $delete_data 削除作業場所の上位ID
     * @var  App\Libraries\php\Domain\WorkspaceDataBase $space_db
     * @var  App\Libraries\php\Domain\Hierarchical $hierarchical
     * @var  array $delete_lists 削除予定のIDを格納した配列
     * @var  int   $code 機能コードの頭2文字
     * @var  array $delete_projections 削除元
     * @var  App\Libraries\php\Domain\ProjectionDataBase $projection_db
     * @var  string $message メッセージ
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($client_id, $delete)
    {
        //選択の作業場所IDをarray型に格納
        $client_id = session('client_id');
        $lists = [];
        $delete_id = [];
        array_push($lists, $delete);

        //インスタンス化
        $space_db = new WorkSpaceDataBase();
        $projection_db = new ProjectionDataBase();
        $hierarchical = new Hierarchical();

        //選択した作業場所を取得
        $delete_data = $space_db->get($client_id, $delete);

        //削除予定の作業場所の上位IDを取得(削除後のページ遷移に必要)
        if (substr($delete, 0, 2) == 'ta') {
            $delete_data = $space_db->getHigh($client_id, $delete);
        } else {
            $delete_data = $space_db->get($client_id, $delete);
        }

        //選択した作業場所の配下を取得
        $delete_lists = $hierarchical->subordinateSearchRoop($lists, $client_id, $delete_id);

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
                        $space_db->delete($client_id, $delete_list);

                        //削除予定の配下作業場所が元になった投影を削除
                        $delete_projections = $projection_db->getProjectionId($client_id, $delete_list);

                        foreach ($delete_projections as $delete_projection) {
                            $hierarchical->delete($client_id, $delete_projection->projection_id);
                            $projection_db->delete($client_id, $delete_projection->projection_id);
                        }

                        $projection_db->delete($client_id, $delete_list);
                    } elseif ($code == "ta") {
                        //投影の削除
                        $projection_db->delete($client_id, $delete_list);
                    } else {
                    }
                    //データの階層構造を削除
                    $hierarchical->delete($client_id, $delete_list);
                }

                DB::commit();
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


        if (!isset($delete_data[0]->high_id)) {
            $delete_data[0]->high_id = "sb";
        }

        PtcmtrController::delete_node($delete_data[0]->high_id);

        if ($delete_data[0]->high_id == "sb") {
            return redirect()->route('pssb01.index');
        }
        return redirect()->route('pssb01.show', [$client_id, $delete_data[0]->high_id]);
    }


    /**
     * 複製したデータを挿入するメソッド
     * @param  \Illuminate\Http\Request  $request
     *
     * @var string $client_id 顧客ID
     * @var string $copy_id 複製するID
     * @var string $high 複製IDが所属する上位階層ID
     * @var App\Libraries\php\Domain\ProjectionDataBase $projection_db
     * @var string $id 複製前の最新の作業場所ID
     * @var string $id_num 複製前の最新の作業場所IDの数字部分
     * @var string $number 8桁に0埋めした複製前の最新の作業場所IDの数字部分
     * @var  App\Libraries\php\Domain\WorkspaceDataBase $space_db
     * @var App\Libraries\php\Domain\Hierarchical $hierarchical
     *
     * @return \Illuminate\Http\Response
     */
    public function copy(Request $request)
    {
        //リクエストの取得
        $client_id = session('client_id');
        $copy_id = $request->copy_id;
        $high = $request->high_id;

        if ($request->copy_id == null) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0009', '01');
            $message = Message::get_message_handle('mhcmer0009', [0 => '']);
            session(['message' => $message[0], 'handle_message' => $message[3]]);
            return redirect()->route('pssb01.index');
        }

        //投影を複製する場合
        if (substr($copy_id, 0, 2) == "ta") {
            try {
                $projection_db = new ProjectionDataBase();
                $code = $projection_db->getId($copy_id);
            } catch (\Exception $e) {
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('pssb01.index');
            }
            $projection_source_id = $code[0]->projection_source_id;
            //最新の投影番号を生成
            try {
                $projection_db = new ProjectionDataBase();
                $projection_id = $projection_db->getNewId($client_id);
            } catch (\Exception $e) {
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('pssb01.index');
            }

            try {
                //トランザクション
                DB::beginTransaction();

                //データベースに投影情報を登録
                $projection_db = new ProjectionDataBase();
                $projection_db->insert($client_id, $projection_id, $projection_source_id);

                //データベースに階層情報を登録
                $hierarchical = new Hierarchical();
                $hierarchical->insert($client_id, $projection_id, $high);

                DB::commit();
            } catch (\Exception $e) {
                //ロールバック
                DB::rollBack();
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('pssb01.index');
            }
            //ログ処理
            OutputLog::message_log(__FUNCTION__, 'mhcmok0009');
            $message = Message::get_message('mhcmok0009', [0 => '']);
            session(['message' => $message[0]]);
            return back();
        } else {

            //複製前の最新の作業場所番号を取得
            try {
                $space_db = new WorkSpaceDataBase();
                $id = $space_db->getId($client_id);
            } catch (\Exception $e) {
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
                DatabaseException::common($e);
                return redirect()->route('pssb01.index');
            }

            $id_num = substr($id[0]->space_id, 3);
            $number = str_pad($id_num, 8, '0', STR_PAD_LEFT);

            //作業場所情報の複製
            try {
                $space_db = new WorkSpaceDataBase();
                $space_db->copy($copy_id, $client_id, $high, $number);
            } catch (\Exception $e) {
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
                DatabaseException::common($e);
                return redirect()->route('pssb01.index');
            }

            //ログ処理
            OutputLog::message_log(__FUNCTION__, 'mhcmok0009');
            $message = Message::get_message('mhcmok0009', [0 => '']);
            session(['message' => $message[0]]);
            return back();
        }
    }
}
