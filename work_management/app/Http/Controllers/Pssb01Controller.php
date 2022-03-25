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
            $space_db = new WorkspaceDataBase();
            $id = $space_db->getId($client_id);
        } catch (\Exception $e) {
            OutputLog::message_log(__FUNCTION__, 'mhcmer0001', '01');
            DatabaseException::common($e);
            echo $e->getMessage(), "\n";
            echo "エラー：" . $e->getMessage();
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
            $space_db = new WorkspaceDataBase();
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
        $space_lists = $space->getClickSub($client_id, $select_id);

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function top()
    {
        //
    }
}
