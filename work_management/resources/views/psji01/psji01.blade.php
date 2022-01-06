@extends('pc0001.pc0001')

    
@section('content')
<div class="col border border-primary" style="padding:10px;">
    <div class="row">
            <form action="{{ route('psji01.store') }}" method="post">
            @csrf
            @method('post')
            {{-- hiddenのvalueはダミーデータ　--}}
            <input type="hidden" name="client_id" value="aa00000001">
            <input type="hidden" name="high" value="{{ $_GET["high"] }}">

            <div class="details-area border border-dark bg-warning" style="padding:10px;" id="parent">
                <div class="row">
                    <div class="col-4">
                        <p id="palent">名前<input type="text" name="name" value="" data-toggle="tooltip" title="人員の名称を入力します"></p>
                    </div>
                    <div class="col-3">
                        <p>番号:</p>
                    </div>
                    <div class="col-3">
                        上位:
                    </div>
                    <div class="col-2">
                    <input type="button" value="ツリー表示" onclick="displayOn()"
                    data-toggle="tooltip" title="ツリーを表示します">
                    </div>
                <div>

                <div class="row">
                    <div class="col-4">
                        <p>管理者番号：<input type="text" id="management_number" name="management_number" maxlength="10" value="ji00000001" style="width:100px;"
                        data-toggle="tooltip" title="部署情報を修正、抹消できる管理者を変更する場合、ここを修正します 管理者自身とシステム管理者だけが修正できます" readonly></p>
                    </div>
                    <div class="col-3" style="padding:0px">
                        <p>管理者名：</p>
                    </div>
                    <div class="col" style="padding:0px">
                    <p>管理者検索：
                        <input type="search" id="search-list" list="keywords" style="width:150px;" autocomplete="on" maxlength="32"
                        
                        data-toggle="tooltip" title="入力に該当した人員の候補を一覧に表示します。表示された人員を選択した場合、その番号が管理者人員番号に表示されます。">
                        <datalist id="keywords">
                        </datalist>
                    </p>
                    </div>
                </div>

                    <div class="row">
                        <div class="col-5">
                            <p>メールアドレス<input type="email" name="email" value=""></p>
                        </div>
                        <div class="col-4" style="padding:0px">
                            <p id="login">パスワード<input id="password" type="password" name="password">
                            <input type="checkbox" data-toggle="tooltip" title="チェック時にパスワードを見えるようにする" onclick="passwordOn()"></p>
                        </div>
                        <div class="col">
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
                            システム管理者:
                            <input type="hidden" name="system_management" value="0">
                            <input type="checkbox" name="system_management" value="1" data-toggle="tooltip" title="人員がシステム管理者かどうかを設定します">
                            ログイン：
                            <input type="hidden" name="login_authority" value="0">
                            <input type="checkbox" name="login_authority" value="1">
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            運用開始日<input name="start_day" type="date" value="{{date('Y-m-d')}}" readonly>
                        </div>
                        <div class="col">
                            運用終了日<input name="finish_day" type="date" value="" readonly>
                        </div>
                    </div>

                    <input type="hidden" id="remarks" name="remarks" value="">

                    <div class="row">
                        <div class="col">
                            <div style="display:inline-flex">
                            <input type="submit" value="確定"
                            data-toggle="tooltip" title="クリックにより、登録、更新を確定します">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div>
                            備考
                        </div>
                        <div>
                            <textarea id="remarks_set" onchange = "remarks(this value)" maxlength="512" style="width:800px; height: 100px;"></textarea>
                        </div>
                    </div>
            </div>
            </form>
    </div>     
</div>

@endsection