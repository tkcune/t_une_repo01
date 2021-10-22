@extends('pc0001.pc0001')

@section('content')
<form="" action="">
    @csrf
    <label>
        1 概要
        <!-- <p>英数記号</p>　ログ認識の概要を説明 -->
    </label>

    <label>
        2 顧客番号
        <input type="text" style="width:10px;">
        <!-- 統合システム管理者である場合のみ表示。出力対象とする顧客番号 -->
    </label>

    <label>
        3 顧客名
        <!-- <p></p>　 -->
        <!-- 統合システム管理者である場合のみ表示、クリックにより、顧客詳細に顧客番号に該当する顧客の名称 --> -->
    </label>

    <label>
        4 顧客検索
        <select name="" id="" style="width:32px;">
            <option value="">----</option>
            <option value=""></option>
            <!-- 統合システム管理者である場合のみ表示、入力に該当した顧客の候補を一覧表示し、選択するとその番号が顧客番号に表示される -->
        </select>
    </label>

    <label>
        5 部署人員番号
        <input type="text" name="" style="width:10px;">
        <!-- 半角英数字以外はNG 　出力対象とする部署番号　未入力で全ての部署人員-->
    </label>

    <label>
        6 部署人員名
        <!-- <p></p> -->
        <!-- クリックにより、顧客詳細に遷移する -->
        <!-- 顧客番号に該当する部署の名称 -->
    </label>

    <label>
        7 部署人員検索
        <input type="search" style="width:32px" name="" autocomplete="on" list="">
        <datalist id="">
            <option value="">
            <option value="">

        </datalist>
        <!-- 全角、半角英数字 -->
        <!-- 入力に該当した部署の候補を一覧表示し、選択とその番号が部署番号に表示される -->
    </label>

    <label>
        8 開始年月日時刻
        <input type="datetime-local">
        <!-- デフォルトは開始、本画面を表示した日の０時０分にする -->
    </label>

    <label>
        8 終了年月日時刻
        <input type="datetime-local">
        <!-- デフォルトは開始、本画面を表示した日の０時０分にする -->
    </label>

    <label>
        9 通常メッセージ
        <input type="checkbox">
        <!-- デフォルトはオフ -->
    </label>

    <label>
    10 警告メッセージ
        <input type="checkbox"　checked>
        <!-- デフォルトはオン -->
    </label>

    <label>
    11 異常メッセージ
        <input type="checkbox"　checked>
        <!-- デフォルトはオン -->
    </label>

    <label>
    12 正常メッセージ
        <input type="checkbox"　checked>
        <!-- デフォルトはオン -->
    </label>

    <label>
    13 システム情報メッセージ
        <input type="checkbox"　>
        <!-- デフォルトはオフ -->
    </label>

    <label>
    14 システム異常メッセージ
        <input type="checkbox"　>
        <!-- デフォルトはオフ -->
    </label>

    <label>
        15 検索文字
        <input type="text" style="width:32px;">
        <!-- 全角、半角英数字、一覧操作領域 -->
        <!-- 入力可能な文字数は３２ -->
    </label>

    <label>
        16 件数
        <!-- <p></p> -->
        <!-- ログ件数を表示する -->
    </label>

    <label>
        17 表示
        <input type="button">
        <!--クリックにより、検索条件に従いログをログ表示領域に表示する -->
    </label>

    <label>
        18 ログ表示領域
        <!-- <p class=''   style="overflow: scroll";> </p>　テキスト -->
        <!-- スクロール方式で動作ログを表示する -->
    </label>

    <label>
        19 ダウンロード
        <INPUT TYPE=button VALUE="データD/L" onClick="location.href='http://...'">
        <!-- クリックにより、ログをダウンロードする -->
    </label>

    </form>
    @endsection