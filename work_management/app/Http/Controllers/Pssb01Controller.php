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
     * @var int $count_department 部署のページ番号
     * @var int $count_personnel　人員のページ番号
     * @var App\Libraries\php\Domain\DepartmentDataBase $department_db
     * @var array $top_department 部署トップデータ
     * @var App\Libraries\php\Domain\PersonnelDataBase personnel_db
     * @var array $personnel_data 人員データ
     * @var  App\Models\Date; $date
     * @var  App\Libraries\php\Logic\ResponsiblePerson $responsible
     * @var  array $top_responsible 最上位の責任者データ
     * @var  App\Libraries\php\Domain\Hierarchical $hierarchical
     * @var  App\Libraries\php\Service\Pagination $pagination
     * @var  int $department_max 部署データページネーションの最大値
     * @var  array $departments ページネーション後の部署データ
     * @var  int $personnel_max 人員データページネーションの最大値
     * @var  array $names ページネーション後の人員データ
     * @var  array $responsible_lists 責任者リスト
     * @var  array $department_high 部署データの上位階層
     * @var  array $personnel_high 人員データの上位階層
     * @var  App\Http\Controllers\PtcmtrController $tree
     * @var  array $tree_data ツリーデータ
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

        //一番上の部署を取得
        $department_db = new DepartmentDataBase();
        $top_department = $department_db->getTop($client_id);
        //全体の部署データの取得
        $department_data = $department_db->getAll($client_id);

        //全体の人員データの取得
        $personnel_db = new PersonnelDataBase();
        $personnel_data = $personnel_db->getAll($client_id);

        //一覧に記載する作業場所データの取得
        $space = new WorkSpaceDataBase();
        $space_dates = $space->getAll($client_id);
        $top_space = $space->getTop($client_id);

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

        $space_max = $pagination->pageMax($space_dates, count($space_dates));
        $spaces = $pagination->pagination($space_dates, count($space_dates), $count_space);

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
            'space_dates'
        ));
    }

    /**
     * 作業場所新規登録画面表示
     *
     * @var  App\Http\Controllers\PtcmtrController $tree
     * @var  array $tree_data ツリーデータ
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //  PtcmtrController：ツリーデータをblade側に渡すクラス
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();
        return view('pssb01.pssb01');
    }

    /**
     * 作業場所の新規登録
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @var  string $space_id 作業場所ID
     * @var  string $name 作業場所名称
     * @var  string $management_personnel_id 管理者ID
     * @var  string $high 上位作業場所のID番号
     * @var  string $id 作業場所IDに対応した最新の作業場所IDを格納する因数
     * @var  App\Libraries\php\Service\ZeroPadding $padding：作業管理システムIDの0埋め機能クラス
     *
     * @return \Illuminate\Http\Response
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
        try {
            $space_db = new WorkSpaceDataBase();
            $id = $space_db->getId($client_id);
        } catch (\Exception $e) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
            DatabaseException::common($e);
            echo $e->getMessage(), "\n";
            return redirect()->route('index');
        }
        if (empty($id)) {
            $space_id = "sb00000001";
        } else {
            //登録する番号を作成
            $padding = new ZeroPadding();
            $space_id = $padding->padding($id[0]->space_id);
        }

        //データベースに作業場所を登録
        try {
            $space_db = new WorkSpaceDataBase();
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
            echo "エラー：" . $e->getMessage();
            return redirect()->route('index');
        }

        //データベースに階層情報を登録
        try {
            $hierarchical = new Hierarchical();
            $hierarchical->insert($client_id, $space_id, $high);
        } catch (\Exception $e) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
            DatabaseException::common($e);
            echo "エラー：" . $e->getMessage();
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

        //詳細に記載する作業場所データの取得
        $space = new WorkSpaceDataBase();
        $space_details = $space->get($client_id, $select_id);

        //一覧に記載する作業場所データの取得
        $space_lists = $space->getList($client_id, $select_id);

        return view('pssb01.pssb02', compact('space_details', 'space_lists'));
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
     *
     * 作業場所情報の更新
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
        try {
            $space_db = new WorkSpaceDataBase();
            $management_personnel_id = $space_db->get($client_id, $space_id);
        } catch (\Exception $e) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
            DatabaseException::common($e);
            return redirect()->route('pssb01.index');
        }

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
     *
     * 作業場所の削除
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($client, $delete)
    {
        //選択の作業場所IDをarray型に格納
        $lists = [];
        $delete_id = [];
        array_push($lists, $delete);

        //選択した作業場所を取得
        try {
            $space_db = new WorkSpaceDataBase();
            $delete_data = $space_db->get($client, $delete);
        } catch (\Exception $e) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
            DatabaseException::common($e);
            return redirect()->route('pssb01.index');
        }

        if (empty($delete_data)) {
            $space_db = new WorkSpaceDataBase();
            $delete_data = $space_db->getClickTop($client, $delete);
            $delete_data[0]->high_id = "sb";
        }

        $hierarchical = new Hierarchical();
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
                        $space_db = new WorkSpaceDataBase();
                        $space_db->delete($client, $delete_list);

                        //削除予定の配下作業場所が元になった投影を削除
                        $projection_db = new ProjectionDataBase();
                        $delete_projections = $projection_db->getProjectionId($client, $delete_list);
                        foreach ($delete_projections as $delete_projection) {
                            $hierarchical = new Hierarchical();
                            $hierarchical->delete($client, $delete_projection->projection_id);
                            $projection_db = new ProjectionDataBase();
                            $projection_db->delete($client, $delete_projection->projection_id);
                        }
                        $projection_db = new ProjectionDataBase();
                        $projection_db->delete($client, $delete_list);
                    } elseif ($code == "ta") {
                        //投影の削除
                        $projection_db = new ProjectionDataBase();
                        $projection_db->delete($client, $delete_list);
                    } else {
                        //データの階層構造を削除
                        $hierarchical = new Hierarchical();
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
     *
     * 複製したデータを挿入するメソッド
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function copy(Request $request)
    {

        $client_id = $request->client_id;
        $copy_id = $request->copy_id;
        $high = $request->high_id;

        // 重複クリック対策
        $request->session()->regenerateToken();

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
                $projection_db = new ProjectionDataBase();
                $code = $projection_db->getId($copy_id);
            } catch (\Exception $e) {
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001');
                DatabaseException::common($e);
                return redirect()->route('index');
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
                $projection_db = new ProjectionDataBase();
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
                $space_db = new WorkSpaceDataBase();
                $copy_space = $space_db->getData($client_id, $copy_id);
            } catch (\Exception $e) {
                OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
                DatabaseException::common($e);
                return redirect()->route('pssb01.index');
            }

            //顧客IDに対応した最新の人員IDを取得
            try {
                $space_db = new WorkSpaceDataBase();
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
                $space_db = new WorkSpaceDataBase();
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
                $hierarchical = new Hierarchical();
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
