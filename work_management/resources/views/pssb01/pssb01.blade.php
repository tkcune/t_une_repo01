@extends('pc0001.pc0001')
<!-- 作業場所新規登録画面（0304編集開始） -->

@section('content')
<div class="col border border-primary" style="padding:10px;">
    <div class="row">
        <form action="{{ route('pssb01.store') }}" method="post">
            @csrf
            @method('post')
            {{-- hiddenのvalueはダミーデータ　--}}
            <input type="hidden" name="client_id" value="{{ session('client_id') }}">
            <input type="hidden" name="high" value="{{ $_GET["high"] }}">

            <div class="details-area border border-dark bg-info" style="padding:10px;" id="parent">
                <div class="row">
                    <div class="col-4" style="margin-top:-5px; margin-right:-12px">
                        <h2>作業場所登録</h2>
                    </div>
                    <div class="col-4" style="margin-right:-10px">
                        <p id="palent">名称：<input type="text" name="name" data-toggle="tooltip" value="" title="作業場所の名称を入力します"></p>
                    </div>

                    <div class="col-3">
                        @if(isset($space_details[0]->high_id))
                        <p>上位：<a href="{{ route('pssb01.show',[session('client_id'),$space_details[0]->high_id])}}" data-toggle="tooltip" title="クリックにより、上位部署に遷移します">{{$space_details[0]->high_name}}</a></p>
                        @endif
                    </div>
                    <div>

                        <div class="row">
                            <div class="col-4">
                                <p>管理者番号：<input type="text" id="management_number" name="management_number" maxlength="10" value="ji00000001" style="width:100px;" data-toggle="tooltip" title="作業場所情報を修正、抹消できる管理者を変更する場合、ここを修正します 管理者自身とシステム管理者だけが修正できます" readonly></p>
                            </div>
                            <div class="col-3" style="padding:0px">
                                <p>管理者名：</p>
                            </div>
                            <div class="col" style="padding:0px">
                                <p>管理者検索：
                                    <input type="search" id="search-list" list="keywords" style="width:150px;" autocomplete="on" maxlength="32" data-toggle="tooltip" title="入力に該当した人員の候補を一覧に表示します。表示された人員を選択した場合、その番号が管理者人員番号に表示されます。">
                                    <datalist id="keywords">
                                    </datalist>
                                </p>
                            </div>
                        </div>

                        <div class="row margin-reset">
                            <div class="col-4">
                                <p>郵便番号：<input type="text" name="postcode" size="10" value="" maxlength="8" onKeyUp="AjaxZip3.zip2addr(this,'','prefectural','address');">
                            </div>
                            <div class="col" style="padding:0px">
                                住所:
                                <input type="text" name="prefectural" value="" size="10" title="ここに都道府県名が入ります。">
                                <input type="text" name="address" value="" size="30" title="ここに市区町村名が入ります。">
                                </p>
                            </div>

                            <div class="row margin-reset">
                                <div class="col">
                                    <p>URL：<input type="text" name="URL" value="" size="81" title="ここに作業場所の地図のURLを入力します。">
                                    <button class="main_button_style" type="button" id="remarks_change_display" onclick="remarksOn()" data-toggle="tooltip" title="クリックにより、備考及び登録日などの情報を開きます">
                            <img class="remarks_button" src="data:image/png;base64,{{Config::get('base64.updown')}}" alt="開閉" >
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
                        <textarea id="remarks_set" onchange = "remarks(this value)" maxlength="512" style="width:800px; height: 60px;"></textarea>
                    </div>
                </div>

                <div class="row" id="little-information-field" style="display:none;">

                </div>

                <div class="row">
                    <div class="col">

                        <div style="display:inline-flex">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.ok')}}" alt="確定" onclick="submit();" data-toggle="tooltip" title="クリックにより、登録、更新を確定します">
                        </div>
            </form>
                        <button class="main_button_style" type="button" id="tree_change_display" data-toggle="tooltip" title="ツリーを表示します" onclick="displayOn()">
                            <img class="main_button_img" src="data:image/png;base64,{{Config::get('base64.tree')}}" alt="開く" >
                        </button>
                    </div>
                </div>
            </div>
    </div>
</div>

<!-- 郵便番号検索は→を一旦利用 https://www.webdesign-fan.com/ajaxzip3  -->
<script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>
@endsection
