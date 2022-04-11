@extends('pc0001.pc0002')

    
@section('content')
<div class="col border border-primary" style="padding:10px;">
    <div class="row">
            <form action="{{ route('psji01.store') }}" method="post">
            @csrf
            @method('post')
            {{-- hiddenのvalueはダミーデータ --}}
            <input type="hidden" name="client_id" value="aa00000001">
            <input type="hidden" name="high" value="{{ $_GET["high"] }}">

            <div class="details-area border border-dark bg-warning" style="padding:10px;" id="parent">
                <div class="row">
                    <div class="col-4" style="margin-top:-5px; margin-right:-12px">
                        <h2>人員登録</h2>
                    </div>
                    <div class="col-4" style="margin-right:-10px">
                        <p id="palent">名前<input type="text" name="name" value="" data-toggle="tooltip" title="人員の名称を入力します">
                        <custom-tooltip title="人員の名称を入力します"></custom-tooltip>
                        </p>
                    </div>
                    
                    <div class="col-3">
                        上位:
                    </div>
                <div>

                <div class="row">
                    <div class="col-4">
                        <p>管理者番号：<input type="text" id="management_number" name="management_number" maxlength="10" value="ji00000001" style="width:100px;"
                        data-toggle="tooltip" title="部署情報を修正、抹消できる管理者を変更する場合、ここを修正します 管理者自身とシステム管理者だけが修正できます" readonly>
                        <custom-tooltip title="部署情報を修正、抹消できる管理者を変更する場合ここを修正します
                        管理者自身とシステム管理者だけが修正できます"></custom-tooltip></p>
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
                        <custom-tooltip title="入力に該当した人員の候補を一覧に表示します
                        表示された人員を選択した場合その番号が管理者人員番号に表示されます"></custom-tooltip>
                    </p>
                    </div>
                </div>

                    <div class="row">
                        <div class="col-5">
                            <p>メールアドレス<input type="email" name="email" value=""></p>
                        </div>
                        <div class="col-4" style="padding:0px;margin-left:-68px">
                            <p id="login">パスワード<input id="password" type="password" name="password">
                            <input type="checkbox" data-toggle="tooltip" title="チェック時にパスワードを見えるようにする" onclick="passwordOn()">
                            <custom-tooltip title="チェック時にパスワードを見えるようにする"></custom-tooltip>
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
                            <custom-tooltip title="人員の状態を選択します"></custom-tooltip>
                            システム管理者:
                            <input type="hidden" name="system_management" value="0">
                            <input type="checkbox" name="system_management" value="1" data-toggle="tooltip" title="人員がシステム管理者かどうかを設定します">
                            <custom-tooltip title="人員がシステム管理者かどうかを設定します"></custom-tooltip>
                            ログイン：
                            <input type="hidden" name="login_authority" value="0">
                            <input type="checkbox" name="login_authority" value="1">
                            
                            運用開始日<input name="start_day" type="date" style="width:140px; margin:0px;" value="{{date('Y-m-d')}}" readonly>
                            運用終了日<input name="finish_day" type="date" style="width:140px; margin:0px;" value="" readonly>

                            <button class="main_button_style" type="button" id="remarks_change_display" onclick="remarksOn()" data-toggle="tooltip" title="クリックにより、備考及び登録日などの情報を開きます">
                                <img class="remarks_button" src="data:image/png;base64,{{Config::get('base64.updown')}}" alt="開閉" >
                            </button>
                            <custom-tooltip title="備考及び登録日などの情報を開きます"></custom-tooltip>
                            </p>
                        </div>
                    </div>

                    <input type="hidden" id="remarks" name="remarks" value="">

                    <div class="row margin-reset" id="remarks-field" style="display:none"">
                        <div>
                            備考
                        </div>
                        <div>
                            <textarea id="remarks_set" onchange = "remarks(this value)" maxlength="512" style="width:800px; height: 60px;"></textarea>
                        </div>
                    </div>

                    <div class="row" id="little-information-field" style="display:none">
                        
                    </div>

                    <div class="row">
                        <div class="col">
                            <p>
                            <div style="display:inline-flex">
                                <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.ok')}}" alt="確定" onclick="submit();" data-toggle="tooltip" title="クリックにより、登録、更新を確定します">
                                <custom-tooltip title="登録を確定します"></custom-tooltip>
                            </div>
            </form>
                        </div>
                    </div>
            </div>
    </div>     
</div>

@endsection