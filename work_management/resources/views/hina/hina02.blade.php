@extends('pc0001.pc0001')
<!-- 詳細画面　雛形テンプレート -->
@section('content')
<div class="col border border-primary" style="padding:10px;">
    <div class="row">
        <form action="#" method="post">
            @csrf
            @method('post')

            <div class="details-area border border-dark bg-warning" style="padding:10px;" id="parent">
                <div class="row">
                    <div class="col-4" style="margin-top:-5px; margin-right:-12px">
                        <h2>詳細画面</h2>
                    </div>
                    <div class="col-4" style="margin-right:-10px">
                        <p id="palent">名前<input type="text" name="name" value=""  placeholder="名前" data-toggle="tooltip" title="ここに説明文を入力"></p>
                    </div>

                    <div class="col-3">
                        <p>上位:：<a href="#" data-toggle="tooltip" title="クリックにより、上位部署に遷移します"></a></p>
                    </div>
                    <div>

                        <div class="row">
                            <div class="col-4">
                                <p>管理者番号：<input type="text" id="management_number" name="management_number" maxlength="10" value="" style="width:100px;" data-toggle="tooltip" title="修正、抹消できる管理者を変更する場合、ここを修正します 管理者自身とシステム管理者だけが修正できます" readonly></p>
                            </div>
                            <div class="col-3" style="padding:0px">
                                <p>管理者名：</p>
                            </div>
                            <div class="col" style="padding:0px">
                                <p>管理者検索：
                                    <input type="search" id="search-list" list="keywords" style="width:150px;"  placeholder="管理者名を選択" autocomplete="on" maxlength="32" data-toggle="tooltip" title="入力に該当した人員の候補を一覧に表示します。表示された人員を選択した場合、その番号が管理者人員番号に表示されます。">
                                    <datalist id="keywords">
                                    <option value="#" label="#"></option>
                                    </datalist>
                                </p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <p>状態:
                                    <select name="status" data-toggle="tooltip" title="人員の状態を選択します">
                                        <option value="10">応募</option>
                                        <option value="11">審査</option>
                                        <option value="12">入社待</option>
                                        <option value="13">在職</option>
                                        <option value="14">休職</option>
                                        <option value="18">退職</option>
                                    </select>

                                    運用開始日<input name="start_day" type="date" style="width:140px; margin:0px;" value="{{date('Y-m-d')}}" readonly>
                                    運用終了日<input name="finish_day" type="date" style="width:140px; margin:0px;" value="" readonly>

                                    <button class="main_button_style" type="button" id="remarks_change_display" onclick="remarksOn()" data-toggle="tooltip" title="クリックにより、備考及び登録日などの情報を開きます">
                                        <img class="remarks_button" src="data:image/png;base64,{{Config::get('base64.updown')}}" alt="開閉">
                                    </button>
                                </p>
                            </div>
                        </div>

                        <input type="hidden" id="remarks" name="remarks" value="">

                    <div class="row margin-reset" id="remarks-field" style="display:none"">
                        <div>
                         備考
                        </div>
                        <div>
                            <textarea id="remarks_set" onchange = "remarks(this value)" maxlength="512" placeholder="備考欄" style="width:800px; height: 60px;"></textarea>
                        </div>
                    </div>

                    <div class="row" id="little-information-field" style="display:none">
                        <p>登録日: 修正日:</p>
                    </div>

                    <div class="row margin-reset">
                        <div class="col">
                            <div style="display:inline-flex">
                                <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.ok')}}" alt="確定" onclick="submit();" id="updateOn" data-toggle="tooltip" title="クリックにより、登録、更新を確定します" style="opacity: 0.3;" disabled>
        </form>

        {{-- 操作ボタン --}}
        @include('hina.parts.parts04')
        <!-- 詳細画面を表示するメソッド上で、詳細画面に必要な変数を渡すクラスをインスタンス化し、コントローラーで設定し変数を受け渡す
        （参照例：Psbs01ControllerやPssb01Controllerの、showメソッドの、「部署詳細オブジェクトの設定」のコメント部分） -->
        {{-- 削除有効化,更新有効化 --}}
        @include('hina.parts.parts03')
    </div>
</div>
</div>
</div>
</div>
</div>

<div id="list-open-button" style="display:none;" onclick="listOn()">
    <p style="text-align:center; cursor: hand; cursor:pointer; background:#99FCCF; border:solid 1px;">↓</p>
</div>

<div class="area">
    <div class="row">
        <div>
            {{-- 操作画面 --}}
            @include('hina.parts.parts02')
            <!-- 詳細画面を表示するメソッド上で、ページネーションオブジェクト機能クラスをインスタンス化し、コントローラーで設定し変数を受け渡す
            （参照例：Psbs01ControllerやPssb01Controllerの、showメソッドの、「ページネーションオブジェクト設定」のコメント部分）、
            もしくは、views/pssb01/pssb02.blade.phpの記載方法 -->
        </div>

        <div class="row margin-reset">
            <div class="col">
                {{-- 一覧画面 --}}
                @include('hina.parts.parts01')
            <!-- 詳細画面を表示するメソッド上で、ページネーションオブジェクト機能クラスをインスタンス化し、コントローラーで設定し変数を受け渡す
            （参照例：Psbs01ControllerやPssb01Controllerの、showメソッドの、「ページネーションオブジェクト設定」のコメント部分、
            もしくは、views/pssb01/pssb02.blade.phpの記載方法 -->
            </div>
        </div>
    </div>
</div>

@endsection
