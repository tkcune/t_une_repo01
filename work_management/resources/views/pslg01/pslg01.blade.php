@extends('pc0001.pc0001')

@section('content')
<div class="container-fluid">
    <div class="row"></div>
    <form method="" action="">
        @csrf


        <div class="mb-3">
            <label for="formGroupExampleInput" class="form-label"> 1 概要 </label>
            <p>※　英数記号で表示する。　ログ認識の概要を説明 </p>
            <!-- <input type="text" class="form-control" id="formGroupExampleInput" placeholder="Example input placeholder"> -->
        </div>
        <div class="mb-3">
            <label for="formGroupExampleInput2" class="form-label"> 2 顧客番号</label>
            <input type="text" style="width:10rem;" class="form-control" id="formGroupExampleInput2" placeholder="統合システム管理者である場合のみ表示。出力対象とする顧客番号" 　>
        </div>
        <div class="mb-3">
            <label for="formGroupExampleInput" class="form-label"> 3 顧客名 </label>
            <p>※　統合システム管理者である場合のみ表示、クリックにより、顧客詳細に顧客番号に該当する顧客の名称 </p>
            <!-- <input type="text" class="form-control" id="formGroupExampleInput" placeholder="Example input placeholder"> -->
        </div>
        <div class="mb-3">
            <label for="formGroupExampleInput" class="form-label"> 4 顧客検索 </label>
            <select class="form-select" aria-label="Default select example" style="width:32rem;">
                <option selected>※統合システム管理者である場合のみ表示、入力に該当した顧客の候補を一覧表示し、選択するとその番号が顧客番号に表示される</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="formGroupExampleInput2" class="form-label"> 5 部署人員番号</label>
            <input type="text" style="width:10rem;" class="form-control" id="formGroupExampleInput2" placeholder="半角英数字以外はNG出力対象とする部署番号　未入力で全ての部署人員">
        </div>
        <div class="mb-3">
            <label for="formGroupExampleInput" class="form-label"> 6 部署人員名 </label>
            <br>
            <a href="#">例：　前川一号生</a>
            <p>※　クリックにより、顧客詳細に遷移する。 顧客番号に該当する部署の名称 </p>

        </div>
        <div class="mb-3">
            <label for="formGroupExampleInput" class="form-label"> 7 部署人員検索 </label>
            <br>
            <input list="lists" id="myList" name="myList" style="width:32rem" />
            <datalist id="lists">
                <option value="全角、半角英数字">
                <option value="入力に該当した部署の候補を一覧表示し、選択とその番号が部署番号に表示される">
            </datalist>

        </div>
        <div class="mb-3">
            <label for="formGroupExampleInput" class="form-label"> 8 開始年月日時刻 </label>
            <input type="datetime-local">
            <!-- デフォルトは開始、本画面を表示した日の０時０分にする（現在まだ調べ中） -->
     
            <label for="formGroupExampleInput" class="form-label">  終了年月日時刻 </label>
            <input type="datetime-local">
            <!-- デフォルトは開始、本画面を表示した日の０時０分にする（現在まだ調べ中） -->
        </div>

        <div class="mb-3">
            <input type="checkbox"> 9 通常メッセージ
            <input type="checkbox" checked>10 警告メッセージ
            <input type="checkbox" checked> 11 異常メッセージ
            <input type="checkbox" checked> 12 正常メッセージ
            <input type="checkbox"> 13 システム情報メッセージ
            <input type="checkbox" 　> 14 システム異常メッセージ
        </div>

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
</div>
@endsection