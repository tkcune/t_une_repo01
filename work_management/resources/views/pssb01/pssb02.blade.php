@extends('pc0001.pc0001')

@section('js')
<script src="{{ asset('js/pssb01/pssb01.js') }}" defer></script>
@endsection

@section('script')
{{-- テーブルソート --}}
<script>
    $(document).ready(function() {
        $('#sb-table').tablesorter({
            headers: {
                5: {
                    sorter: false
                }
            }
        });
    });
</script>

<!-- 郵便番号検索はいったん→を利用　https://dezanari.com/ajaxzip3/ -->
<script src="https://ajaxzip3.github.io/ajaxzip3.js"></script>
@endsection

@section('content')
<div class="col border border-primary" style="padding:10px;">
    <div class="row">
        <form action="{{ route('pssb01.update',$space_data[0]->space_id) }}" method="post">
            @csrf
            @method('patch')
            <input type="hidden" id="space_id" name="space_id" value="{{$space_data[0]->space_id}}">
            <input type="hidden" name="client_id" value="{{ session('client_id') }}">
            @if(substr($click_id,0,2) == "ta")
            <div class="details-area border border-dark bg-info" style="padding:10px;" id="parent">
                @else
                <div class="details-area border border-dark bg-info" style="padding:10px;" id="parent">
                    @endif
                    <div class="row">
                        <div class="col-4" style="margin-top:-5px; margin-right:-12px">
                            <h2>作業場所詳細</h2>
                        </div>
                        <div class="col-4" style="margin-right:-10px">
                            <p id="palent">
                                <span data-toggle="tooltip" id="id_number" title="番号:{{$space_data[0]->space_id}}">
                                    名称：<input type="text" name="name" value="{{$space_data[0]->name}}" placeholder="作業場所名" data-toggle="tooltip" title="ここに作業場所の名称が入ります。">
                            </p>
                        </div>

                        <div class="col-3">
                            @if(isset($space_data[0]->high_id))
                            <p>上位：<a href="{{ route('pssb01.show',[session('client_id'),$space_data[0]->high_id])}}" data-toggle="tooltip" title="クリックにより、上位部署に遷移します">{{$space_data[0]->high_name}}</a></p>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-4">
                                <p>管理者番号：<input type="text" id="management_number" name="management_number" maxlength="10" value="{{$space_data[0]->management_personnel_id}}" style="width:100px;" data-toggle="tooltip" title="作業場所情報を修正、抹消できる管理者を変更する場合、ここを修正します。管理者自身とシステム管理者だけが修正できます" readonly></p>
                            </div>
                            <div class="col-3" style="padding:0px">
                                <p>管理者名：<a href="{{ route('plbs01.show',[session('client_id'),$space_data[0]->management_personnel_id])}}">{{$space_data[0]->management_name}}</a></p>
                            </div>
                            <div class="col" style="padding:0px">
                                <p>管理者検索：
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
                            <div class="col-4">
                                <p>郵便番号：<input type="text" name="postcode" size="10" value="{{$space_data[0]->post_code}}" maxlength="8" title="郵便番号はハイフン不要の7桁で入力してください。" placeholder="0123456">
                                <button type="button" title="インターネット接続時にこのボタンを押すと、郵便番号に基づいた該当の住所が反映します。"  onclick="AjaxZip3.zip2addr('postcode', 'address1', 'address1', 'address1');">検索</button>
                            </div>
                            <div class="col" style="padding:0px">
                                住所:
                                <input type="text" name="address1" value="{{$space_data[0]->address1}}" size="29" placeholder="都道府県+市区町村" title="ここに都道府県名と以降の住所が入ります。">
                                <input type="text" name="address2" value="{{$space_data[0]->address2}}" size="22" placeholder="番地" title="ここに番地が入ります。番地なしの場合は無番地と入力します。">
                                </p>
                            </div>
                            <div class="row margin-reset">
                                <div class="col">
                                    <p>地図URL：<input type="url" name="URL" value="{{$space_data[0]->URL}}" size="76" placeholder="https://www.google.co.jp/maps/?hl=ja" title="ここに作業場所の地図のURLを入力します。">
                                        {{-- 作業場所のマップURL、値がNULLならグーグルマップが表示される --}}
                                        @if(($space_data[0]->URL) === NULL)
                                        <button class="main_button_style" type="button" id="remarks_change_display" onclick="window.open('https://www.google.com/maps/','_blank')" data-toggle="tooltip" title="クリックにより、地図を開きます。DBにURLの登録がない場合は、googleマップが開かれます。">
                                            <img class="remarks_button" src="data:image/png;base64,{{Config::get('base64.tizu')}}" alt="地図">
                                        </button>
                                        @else
                                        <button class="main_button_style" type="button" id="remarks_change_display" onclick="window.open('{{$space_data[0]->URL}}','_blank')" data-toggle="tooltip" title="クリックにより、地図を開きます。DBにURLの登録がない場合は、googleマップが開かれます。">
                                            <img class="remarks_button" src="data:image/png;base64,{{Config::get('base64.tizu')}}" alt="地図">
                                        </button>
                                        @endif

                                        <button class="main_button_style" type="button" id="remarks_change_display" onclick="remarksOn()" data-toggle="tooltip" title="クリックにより、備考及び登録日などの情報を開きます">
                                            <img class="remarks_button" src="data:image/png;base64,{{Config::get('base64.updown')}}" alt="開閉">
                                        </button>
                                    </p>
                                </div>
                            </div>

                            <input type="hidden" id="remarks" name="remarks" value="{{ $space_data[0]->remarks }}">

                        <div class="row margin-reset" id="remarks-field" style="display:none">
                            <div>
                                備考
                            </div>
                            <div>
                                <textarea id="remarks_set" onchange = "remarks(this value)" placeholder="備考欄は任意で入力します。" style="width:800px; height: 60px;">{{ $space_data[0]->remarks }}</textarea>
                            </div>
                        </div>

                        <div class="row" id="little-information-field" style="display:none">
                            <p>登録日:{{$space_data[0]->created_at}} 修正日:{{$space_data[0]->updated_at}}</p>
                        </div>

                        <div class="row margin-reset">
                            <div class="col">
                                <div style="display:inline-flex">
                                    <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.ok')}}" alt="確定" onclick="submit();" id="updateOn" data-toggle="tooltip" title="クリックにより、登録、更新を確定します" style="opacity: 0.3;" disabled>
        </form>

        {{-- 操作ボタン --}}
        @include('plsb01.plsb04', ['space_data' => $space_details_object, 'click_id' => $click_id])
        {{-- 削除有効化,更新有効化 --}}
        @include('plsb01.plsb03')
    </div>
</div>
</div>
</div>
</div>
</div>
</div>

<div id="list-open-button" style="display:none;" onclick="listOn()">
    <p style="text-align:center; cursor: hand; cursor:pointer; background:#99FCCF; border:solid 1px;">↓</p>
</div>

<div class="space-area">
    <div class="row">
        <div>
            {{-- 作業場所操作画面 --}}
            @include('plsb01.plsb02',
            [
            'click_data' => $click_data,
            'count_space' => $pagination_object->count_space,
            'space_max' => $pagination_object->space_max,
            'total_space' => $pagination_object->total_space,
            ]
            )
        </div>

        <div class="row margin-reset">
            <div class="col">
                {{-- 作業場所表示画面 --}}
                @include('plsb01.plsb01', ['space_details' => $pagination_object->space_details])
            </div>
        </div>
    </div>
</div>
@endsection
