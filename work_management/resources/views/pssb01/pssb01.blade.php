@extends('pc0001.pc0001')

<!-- 郵便番号検索はいったん→を利用　https://dezanari.com/ajaxzip3/ -->
<script src="https://ajaxzip3.github.io/ajaxzip3.js"></script>

@section('content')
<div class="col border border-primary" style="padding:10px;">
    <div class="row">
        <form action="{{ route('pssb01.store') }}" method="post">
            @csrf
            @method('post')
            {{-- hiddenのvalueはダミーデータ--}}
            <input type="hidden" name="client_id" value="{{ session('client_id') }}">
            <input type="hidden" name="high" value="{{ $_GET["high"] }}">

            <div class="details-area border border-dark bg-info" style="padding:10px;" id="parent">
                <div class="row">
                    <div class="col-4" style="margin-top:-5px; margin-right:-12px">
                        <h2>作業場所登録
                        </h2>
                    </div>

                    <div class="col-4" style="margin-right:-10px">
                        <p id="palent">名称<span class="kome">*</span>：<input type="text" name="name" data-toggle="tooltip" placeholder="作業場所名" value="{{ old('name') }}" title="作業場所の名称を32文字以内で入力します"></p>
                    </div>

                    <div class="col-3 hissu">
                        <span class="kome">*</span>は必須入力項目です
                    </div>

                    <div class="row">
                        <div class="col-4">
                            <p>管理者番号：<input type="text" id="management_number" name="management_number" maxlength="10" value="ji00000001" style="width:100px;" data-toggle="tooltip" title="作業場所情報を修正、抹消できる管理者を変更する場合、ここを修正します 管理者自身とシステム管理者だけが修正できます" readonly></p>
                        </div>
                        <div class="col-3" style="padding:0px">
                            <p>管理者名：</p>
                        </div>
                        <div class="col" style="padding:0px">
                            <p>管理者検索<span class="kome">*</span>：
                                <input type="search" id="search-list" list="keywords" style="width:150px;" autocomplete="on" maxlength="32" placeholder="管理者名を選択" data-toggle="tooltip" title="入力に該当した人員の候補を一覧に表示します。表示された人員を選択した場合、その番号が管理者人員番号に表示されます。">
                                <datalist id="keywords">
                                    @foreach($system_management_lists as $system_management_list)
                                    <option value="{{$system_management_list->name}}" label="{{$system_management_list->personnel_id}}"></option>
                                    @endforeach
                                </datalist>
                            </p>
                        </div>
                    </div>

                    <div class="row margin-reset">
                        <div class="col-4" style="padding-right:0px">
                            <p>郵便番号<span class="kome">*</span>：
                                @if(!empty($post_code))
                                <input type="text" name="postcode" size="10" value="{{ $post_code }}" maxlength="8" placeholder="0123456" title="郵便番号はハイフン不要の7桁で入力してください。">
                                @else
                                <input type="text" name="postcode" size="10" value="{{ old('post_code') }}" maxlength="8" placeholder="0123456" title="郵便番号はハイフン不要の7桁で入力してください。">
                                @endif
                                <button type="button" title="インターネット接続時にこのボタンを押すと、郵便番号に基づいた該当の住所が反映します。" onclick="AjaxZip3.zip2addr('postcode', 'address1', 'address1', 'address1');">検索</button>
                            </div>

                        <div class="col" style="padding-right:0px">
                            住所<span class="kome">*</span>:
                            @if(!empty($address1))
                            <input type="text" name="address1" value="{{ $address1 }}" size="25" placeholder="都道府県+市区町村" title="ここに都道府県名と市区町村が入ります。">
                            @else
                            <input type="text" name="address1" value="{{ old('address1') }}" size="25" placeholder="都道府県+市区町村" title="ここに都道府県名と市区町村が入ります。">
                            @endif

                            @if(!empty($address2))
                            <input type="text" name="address2" value="{{ $address2 }}" size="15" placeholder="番地" title="ここに番地が入ります。番地なしの場合は無番地と入力します。">
                            @else
                            <input type="text" name="address2" value="{{ old('address2') }}" size="15" placeholder="番地" title="ここに番地が入ります。番地なしの場合は無番地と入力します。">
                            @endif
                            </p>
                        </div>

                        <div class="row margin-reset">
                            <div class="col">
                                <p>地図URL：
                                    @if(!empty($URL))
                                    <input type="url" name="URL" value="{{ $URL }}" size="76" placeholder="https://www.google.co.jp/maps/?hl=ja" title="ここに作業場所の地図のURLを入力します。">
                                    @else
                                    <input type="url" name="URL" value="{{ old('URL') }}" size="76" placeholder="https://www.google.co.jp/maps/?hl=ja" title="ここに作業場所の地図のURLを入力します。">
                                    @endif
                                    <button class="main_button_style" type="button" id="remarks_change_display" onclick="remarksOn()" data-toggle="tooltip" title="クリックにより、備考及び登録日などの情報を開きます">
                                        <img class="remarks_button" src="data:image/png;base64,{{Config::get('base64.updown')}}" alt="開閉">
                                    </button>
                                </p>
                            </div>
                        </div>

                        <input type="hidden" id="remarks" name="remarks" value="{{ old('remarks') }}">

                        <div class="row margin-reset" id="remarks-field" style="display:none"">
                        <div>
                         備考
                        </div>
                        <div>
                            <textarea id="remarks_set" onchange = "remarks(this value)" maxlength="512" placeholder="備考欄は任意で入力します。" style="width:800px; height: 60px;"></textarea>
                        </div>
                    </div>

                    <div class="row" id="little-information-field" style="display:none;">
                    </div>

                    <div class="row">
                        <div class="col">
                            <div style="display:inline-flex">
                                <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.ok')}}" alt="確定" onclick="submit();" data-toggle="tooltip" title="クリックにより、登録、更新を確定します">
                            </div>
                            <button class="main_button_style" type="button" id="tree_change_display" data-toggle="tooltip" title="ツリーを表示します" onclick="displayOn()">
                                <img class="main_button_img" src="data:image/png;base64,{{Config::get('base64.tree')}}" alt="開く">
                            </button>
                        </div>
                    </div>
                </div>
        </form>
    </div>
</div>



@endsection
