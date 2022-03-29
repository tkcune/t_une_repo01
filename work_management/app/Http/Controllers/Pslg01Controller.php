<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PtcmtrController;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Pslg01Controller extends Controller
{
    /**
     * ログページ表示(ツリーからまたは、直接入力の場合)
     * 
     *  @var  array $tree_data ツリーデータ
     *  @var array $name_data 部署人員のデータ
     *  @var array  $personnel_data セッション保存した部署人員データ
     *  
     *  @return \Illuminate\Http\Response
     */

    public function index()
    {
        // ツリーのデーターを宣言する
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        $name_data = DB::table('dcji01')->get();

        session()->put('name_data', $name_data);
        $personnel_data = session()->get('name_data');

        if(session('device') != 'mobile'){
            return view('pslg01.pslg01', ['personnel_data' => $personnel_data]);
        }else{
            return view('pslg01.pslg02', ['personnel_data' => $personnel_data]);
        }
    }

     /**
     * ログページ表示(データ表示後「クリアーする」を押した時の表示)
     * 
     *  @var  array $tree_data ツリーデータ
     *  @var array $session_names セッション保存した部署人員データ
     *  
     *  @return \Illuminate\Http\Response
     */

    public function clear()
    {
        // ツリーのデーターを宣言する
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        $personnel_data = session()->get('name_data');

        if(session('device') != 'mobile'){
            return view('pslg01.pslg01', ['personnel_data' => $personnel_data]);
        }else{
            return view('pslg01.pslg02', ['personnel_data' => $personnel_data]);
        }
    }


    /**
     * 表示するをクリックによりログ一覧を表示する
     * 
     *  @param  \Illuminate\Http\Request  $request
     *  @var array $tree_data ツリーデータ
     *  @var array $items 検索内容一覧表示
     *  @var int $count 検索結果に基づくログの件数
     *  @var string $select_name　選択された部員名
     *  @var array $session_names　セッション保存した部署人員データ
     * 
     *  @return \Illuminate\Http\Response
     */

    public function create(Request $request)
    {
        // ツリーのデーターを宣言する
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();


        //　時間に入っている「T」を正規表現で半角スペースに置き換える
        $startdate = str_replace('T', ' ', $request->startdate);
        $finishdate = str_replace('T', ' ', $request->finishdate);


        //  時間入力のエラーチェック
        $now_time = date("Y-m-d H:i");
        if($now_time < $startdate || $now_time < $finishdate){
            $time_error = "日時が今日以降の入力がされました。再度入力してください。";
            return back()->with('time_error',$time_error)->withInput();
        }elseif($startdate > $finishdate){
            $time_error = "終了日時が開始日時より過去の日時を入力されました。再度入力してください。";
            return back()->with('time_error',$time_error)->withInput();
        }
       

        if ($request->management_number == null && $request->management_name == null) {
            // パターン⓵　部署人員番号および部署人員名が未記入の設定　＝　部員全員分の一覧表示

            $items = DB::table('dclg01')
                ->join('dcji01', 'dclg01.user', '=', 'dcji01.email')
                ->select('dclg01.*', 'dcji01.name', 'dcji01.personnel_id','dcji01.system_management')
                ->whereIn('dclg01.type', $request->check)
                ->where('dclg01.created_at', '>=', $startdate)
                ->where('dclg01.updated_at', '<=', $finishdate)
                // ->where('dclg01.program_pass','like',"%$request->search%")
                ->get();
        } else {
            // パターン⓶　部署人員番号が記入あり設定　＝　選択された部署人員の一覧表示

            $items = DB::table('dclg01')
                ->join('dcji01', 'dclg01.user', '=', 'dcji01.email')
                ->select('dclg01.*', 'dcji01.name', 'dcji01.personnel_id','dcji01.system_management')
                ->whereIn('dclg01.type', $request->check)
                ->where('dcji01.name', '=', $request->management_name )
                ->orwhere('dcji01.personnel_id', '=', $request->management_number)
                ->where('dclg01.created_at', '>=', $startdate)
                ->where('dclg01.updated_at', '<=', $finishdate)
                // ->where('dclg01.program_pass','like',"%$request->search%")
                ->get();
        }


        // ログの結果の件数を抽出する
        $count = count($items);

        // 　セッションに保存する
        session()->put('items', $items);

        // sessionからname_dataを抽出する
        $personnel_data = session()->get('name_data');

        if(session('device') != 'mobile'){
            return view('pslg01.pslg01', [
                'items' => $items,
                'count' => $count,
                'personnel_data' => $personnel_data,
    
            ]);
        }else{
            return view('pslg01.pslg02', [
                'items' => $items,
                'count' => $count,
                'personnel_data' => $personnel_data,
    
            ]);
        }
    }

 

    /**
     * 一覧表示された内容をダウンロードする
     * 
     *  @param  \Illuminate\Http\Request  $request
     *  @var array $tree_data       ツリーデータ
     *  @var array $session_items 　sessionで保存された検索内容一覧表示
     *  @var array $csvList         csv化するデーター
     *  @var array $title　         csv化するタイトルテーマ名
     *  @var array $session_names　セッション保存した部署人員データ
     *  @var array $download_data   ダウンロードするデータ（$titleと$csvListを合わせたもの）
     *  @var $stream    書き込みをオープンにする 
     * 
     *  @return \Illuminate\Http\Response
     */
    public function download(Request $request)
    {
        // ツリーのデーターを宣言する
        $tree = new PtcmtrController();
        $tree_data = $tree->set_view_treedata();

        $session_items = session()->get('items');


        foreach ($session_items as $items) {
                $csvList[] = [$items->created_at, $items->type, $items->user, $items->function, $items->program_pass, $items->log];
        }
        $title[] = ["出力日時", "類別", "アクセスユーザ", "機能", "プログラムパス", "ログ"];
        $download_data = (array_merge($title, $csvList));

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
