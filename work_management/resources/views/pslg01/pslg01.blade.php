@extends('pc0001.pc0001')

@section('content')
<div class="col border border-primary" style="padding:10px;">
<div class="details-area border border-dark bg-warning" style="padding:10px;" id="parent">
<div class="container-fluid">
    <div class="row"></div>
    <form method="" action="">
        @csrf
        <P style="text-align: center">―　ログ確認　―</P>
        <div class="mb-3">
            <h7 style="color:red;"> 【注意点】 ※のマークは各項目の注意点やメモとして記載してます</h7>
            <h7 style="color:red;"> 現在レイアウトの時点なので、各項目の前に数字が記載中、完成ともに項目番号は消す。</h7>
        </div>

        <div class="mb-3">
            <!-- <label for="formGroupExampleInput" class="form-label"> 1 概要 </label> -->
            <p>1　概要　：　※　英数記号で表示する。　ログ認識の概要を説明 </p>
        </div>

        <div class="mb-3">
            <label for="formGroupExampleInput2" class="form-label"> 2　顧客番号　：</label>
            <div class="text_box" style="margin:-5px 0 0 3rem;">
                <input type="text" style="width:10rem;" class="form-control" id="formGroupExampleInput2" placeholder="半角英数字で入力">
                <p>※　統合システム管理者である場合のみ表示。出力対象とする顧客番号</p>
            </div>

        </div>

        <div class="mb-3">
            <!-- <label for="formGroupExampleInput3" class="form-label"> 3 顧客名 </label> -->
            <p>3　顧客名　： <a href="#" title="クリックにより顧客詳細に遷移します">　(例)前川一号生</a>　　※　統合システム管理者である場合のみ表示</p>
        </div>

        <div class="mb-3">
            <label for="formGroupExampleInput4" class="form-label" title="入力に該当した顧客の候補を一覧に表示します。表示された人員を選択した場合、その番号が顧客番号に表示されます。">4　顧客検索　：</label>
            <!-- <p title="入力に該当した顧客の候補を一覧に表示します。表示された人員を選択した場合、その番号が顧客番号に表示されます。">4　顧客検索 -->
            <div class="text_box" style="margin:-5px 0 0 3rem;">
                <select class="form-select" aria-label="Default select example" style="width:32rem;">
                    <option selected>※統合システム管理者である場合のみ表示、入力に該当した顧客の候補を一覧表示し、選択するとその番号が顧客番号に表示される</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label for="formGroupExampleInput5" class="form-label" title="入力に該当した顧客の候補を一覧に表示します。表示された人員を選択した場合、その番号が顧客番号に表示されます。">5　部署人員番号　：</label>　
            <div class="text_box" style="margin:-5px 0 0 3rem;">
                <input type="text" style="width:10rem;" class="form-control" id="formGroupExampleInput2" placeholder="半角英数字以外はNG出力対象とする部署番号　未入力で全ての部署人員">
            </div>

        </div>
        <div class="mb-3">
            <p>6　部署人員名　： <a href="#" title="クリックにより顧客詳細に遷移します">　(例)前川一号生</a> ※　クリックにより、顧客詳細に遷移する。 顧客番号に該当する部署の名称 </p>
            <!-- <label for="formGroupExampleInput6" class="form-label"> 6 部署人員名 </label> -->

        </div>
        <div class="mb-3">
            <label for="formGroupExampleInput7" class="form-label" title="入力に該当した人員の候補を一覧に表示します。表示された人員を選択した場合、その番号が部署番号に表示されます。"> 7　部署人員検索　：</label>
            <div class="text_box" style="margin:-5px 0 0 3rem;">
                <input list="lists" id="myList" name="myList" style="width:32rem" />
                <datalist id="lists">
                    <option value="全角、半角英数字">
                    <option value="入力に該当した部署の候補を一覧表示し、選択とその番号が部署番号に表示される">
                </datalist>
            </div>
        </div>
        <div class="mb-3">
            <div class="box">
                <?php $date = new DateTime('now'); ?>
                <p title="開始年月日時刻を表示します"> 8　開始年月日時刻　： <?= $date->format('m月d日'); ?>　0時0分</p>
                <!-- <label for="formGroupExampleInput" class="form-label"> 終了年月日時刻 : </label> -->
            </div>
            <div class="box">
                <p title="終了年月日時刻を表示します"> 8　終了年月日時刻　： <?= $date->format('m月d日'); ?></p>
                <!-- <label for="formGroupExampleInput" class="form-label"> 終了年月日時刻 : </label> -->
            </div>
        </div>

        <div class="mb-3">
            <p>&nbsp;&nbsp;&nbsp; 9,　<input type="checkbox" title="通常メッセージログを表示するかどうかを指定します"> 通常メッセージ</p>
            <p>&nbsp;&nbsp;10,　<input type="checkbox" title="警告メッセージログを表示するかどうかを指定します" checked> 警告メッセージ</p>
            <p>&nbsp;&nbsp;11,　<input type="checkbox" title="異常メッセージログを表示するかどうかを指定します" checked> 異常メッセージ</p>
            <p>&nbsp;&nbsp;12,　<input type="checkbox" title="正常メッセージログを表示するかどうかを指定します" checked> 正常メッセージ</p>
            <p>&nbsp;&nbsp;13,　<input type="checkbox" title="システム情報メッセージログを表示するかどうかを指定します"> システム情報メッセージ</p>
            <p>&nbsp;&nbsp;14,　<input type="checkbox" title="システム異常メッセージログを表示するかどうかを指定します"> システム異常メッセージ</p>
        </div>

        <div class="mb-3">
            <label for="formGroupExampleInput15" class="form-label" title="検索する文字"> 15　検索文字　：</label>
            <div class="text_box" style="margin:-5px 0 0 3rem;">
                <input type="text" style="width:32rem;" class="form-control" id="formGroupExampleInput2" placeholder="入力可能な文字数は３２  全角、半角英数字、一覧操作領域">
            </div>
        </div>

        <div class="mb-3">
            <!-- <label for="formGroupExampleInput16" class="form-label"> 16 件数 </label> -->
            <p>16　件数　：　　※　 ログ件数を表示する </p>
        </div>

        <div class="mb-3">
            <label for="no17" class="form-label"> 17　表示　：</label>
            <input type="submit" id="formGroupExampleInput17" value="表示する">
            <p>※　クリックにより、検索条件に従いログをログ表示領域に表示する</p>
        </div>

        <div class="mb-3">
            <label for="formGroupExampleInput18" class="form-label"> 18　ログ表示領域　：</label>
             <div class="scroll" style="width: 200px;  height: 200px; border: 1px solid #000;  overflow:scroll; margin:0 0 0 3rem;">
                <p>※ スクロール方式 </p>
                <p>※ スクロール方式 </p>
                <p>※ スクロール方式 </p>
                <p>※ スクロール方式 </p>
                <p>※ スクロール方式 </p>
                <p>※ スクロール方式 </p>
                <p>※ スクロール方式 </p>
            </div>
        </div>

        <div class="mb-3 mt-2">
            <label for="no19" class="form-label" title="クリックにより、表示しているログのダウンロードを実行します"> 19　ダウンロード　：</label>
            <input type="submit" id="formGroupExampleInput19" value="データをダウンロードする" 　onClick="location.href='http://...'" 　>
            <p>※　クリックにより、検索条件に従いログをログ表示領域に表示する</p>
        </div>

    </form>


</div>
</div>
</div>
@endsection