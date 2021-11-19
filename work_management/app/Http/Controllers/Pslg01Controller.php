<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PtcmtrController;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Pslg01Controller extends Controller
{

    //
    public function index()
    {
        // ツリーのデーターを宣言する
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        $name_data = DB::table('dcji01')->get();

        session()->put('name_data', $name_data);
        $session_names = session()->get('name_data');


        return view('pslg01.pslg01', ['session_names' => $session_names]);
    }



    /**
     * セレクトボックスの名前から部員のIDと名前を表示する
     */
    public function select(Request $request)
    {
        // ツリーのデーターを宣言する
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        // 空白を選択されたらそのままバックする
        if ($request->personnel_id == '0') {
            return back();
        } else {
            // セレクトボックスで選択された部員の情報を抽出する
            $select_all = DB::table('dcji01')->where('personnel_id', '=', $request->personnel_id)->get();

            // 抽出したデータの部員IDと部員名をsessionで保存する
            session()->put('select_id', $select_all[0]->personnel_id);
            session()->put('select_name', $select_all[0]->name);

            // sessionから部員IDと部位名および部員検索で表示する部員一覧を取得する
            $select_id = session()->get('select_id');
            $select_name = session()->get('select_name');
            $session_names = session()->get('name_data');


            return view('pslg01.pslg01', [
                'select_id' => $select_id,
                'select_name' => $select_name,
                'session_names' => $session_names
            ]);
        }
    }


    /**
     * 表示するをクリックによりログ一覧を表示する
     * 
     */

    public function create(Request $request)
    {
        // ツリーのデーターを宣言する
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        // dd($request->personnel_id);
              

        // ログ表示に該当するものをselectする　

        if ($request->personnel_id == null) {
            // パターン⓵　部署人員番号が未記入の設定　＝　部員全員分の当日一覧表示

            $items = DB::table('dclg01')
                ->join('dcji01', 'dclg01.user', '=', 'dcji01.email')
                ->select('dclg01.*', 'dcji01.name', 'dcji01.personnel_id')
                ->whereIn('dclg01.type', $request->check)
                ->where('dclg01.created_at', 'like', "%$request->startdate%")
                // ->where('dclg01.updated_at', 'like', "%$request->finishdate%")
                ->where('dclg01.log', 'like', "%$request->kensaku%")
                ->get();
        } else {
            // パターン⓶　部署人員番号が記入あり設定　＝　選択された部署人員の当日分を一覧表示

            $items = DB::table('dclg01')
                ->join('dcji01', 'dclg01.user', '=', 'dcji01.email')
                ->select('dclg01.*', 'dcji01.name', 'dcji01.personnel_id')
                ->whereIn('dclg01.type', $request->check)
                ->where('dcji01.name', '=', $request->select_name)
                ->where('dcji01.personnel_id', '=', $request->personnel_id)
                ->where('dclg01.created_at', 'like', "%$request->startdate%")
                // ->where('dclg01.updated_at', 'like', "%$request->finishdate%")
                ->where('dclg01.log', 'like', "%$request->kensaku%")
                ->get();
        }

         session()->put('items', $items);

        // ログの結果の件数を抽出する
        $count = count($items);

        // データを抽出かつ、sessinからname_dataとselect_nameを抽出する
        $name_data = DB::table('dcji01')->get();
        $session_names = session()->get('name_data');
        $select_name = session()->get('select_name');


        return view('pslg01.pslg01', [
            'items' => $items,
            'count' => $count,
            'name_data' => $name_data,
            'session_names' => $session_names,
            'select_name' => $select_name
        ]);
    }


    public function download(Request $request)
    {
        // ツリーのデーターを宣言する
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();


        $session_items = session()->get('items');
        foreach ($session_items as $items) {
            $cvsList[] = [$items->created_at, $items->type, $items->user, $items->function, $items->program_pass, $items->log];
        }

        $tuika[] = ["出力日時", "類別", "アクセスユーザ", "機能", "プログラムパス", "ログ"];


        $download_data = (array_merge($tuika, $cvsList));

        $response = new StreamedResponse(function () use ($request, $download_data) {
            $stream = fopen('php://output', 'w');

            //　文字化け回避
            stream_filter_prepend($stream, 'convert.iconv.utf-8/cp932//TRANSLIT');

            // CSVデータ
            foreach ($download_data as $key => $value) {
                fputcsv($stream, $value);
            }
            fclose($stream);
        });
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename="facmsl.log.csv"');

        return $response;

    }
}
