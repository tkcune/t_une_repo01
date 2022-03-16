@extends('pc0001.pc0001')

@section('content')

    {{-- コメント　詳細画面ここから --}}
    {{-- 部署の詳細表示--}}
    <div class="col border border-primary" style="padding:10px;">

    @if( empty(session('click_code')) or session('click_code') == "bs")
    <form action="{{ route('psbs01.update') }}" method="post">
            @csrf
            @method('patch')
            <input type="hidden" id="department_id" name="department_id" value="{{$department_details_object->department_id}}">
            <input type="hidden" name="client_id" value="{{ session('client_id') }}">

            @if(isset($click_id) &&substr($click_id,0,2) == "ta")
            <div class="details-area border border-dark bg-info" style="padding:10px;" id="parent">
            @else
            <div class="details-area border border-dark bg-warning" style="padding:10px;" id="parent">
            @endif

                {{--  部署詳細,名称  --}}
                @include('pscm01.pscm06', ['click_department_data' => $department_details_object, 'click_department_high' => $click_department_high])
                {{-- 管理者番号、管理者名、管理者検索 --}}
                @include('pscm01.pscm07', ['click_department_data' => $department_details_object, 'click_management_lists' => $click_management_lists, 'all_personnel_data' => $all_personnel_data])
                {{-- 状態、責任者、運用開始日、運用終了日 --}}
                @include('pscm01.pscm03', 
                    [
                        'top_department' => $department_details_object, 
                        'top_responsible' => $top_responsible, 
                        'personnel_data' => $personnel_data,
                        'operation_date' => $operation_date
                    ]
                )
                {{-- 備考 --}}
                @include('pscm01.pscm05', ['click_department_data' => $department_details_object, 'click_responsible_lists' => $click_responsible_lists])

                <div class="row main_button_display">
                    <div class="col">
                    <p>
                    <div style="display:inline-flex">
                    <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.ok')}}" alt="確定" id="updateOn" onclick="submit();" data-toggle="tooltip" title="クリックにより、登録、更新を確定します" 
                    style="opacity: 0.3;" disabled>
    </form>
                    {{-- 操作ボタン --}}
                    @include('pscm01.pscm04', ['click_id' => $click_id, 'click_department_data' => $department_details_object])
                    {{-- 削除有効化、更新有効化 --}}
                    @include('pscm01.pscm01')
                    </div>
                </div>
            </div>
    </div>
    {{-- 部署の詳細表示　ここまで--}}
    {{-- 人員の詳細表示　--}}
    @else
    <form action="{{ route('psji01.update',session('client_id')) }}" method="post">
            @csrf
            @method('patch')
            <input type="hidden" id="personnel_id" name="personnel_id" value="{{$click_personnel_data[0]->personnel_id}}">
            <input type="hidden" name="client_id" value="{{ session('client_id') }}">

            @if(substr($click_id,0,2) == "ta")
            <div class="details-area border border-dark bg-info" style="padding:10px;" id="parent">
            @else
            <div class="details-area border border-dark bg-warning" style="padding:10px;" id="parent">
            @endif
                <div class="row">
                    <div class="col-4" id="page_name" style="margin-top:-5px; margin-right:-12px">
                        <h2>人員詳細</2>
                    </div>
                    <div class="col-4" style="margin-right:-10px">
                        <p id="palent">
                            <span data-toggle="tooltip" id="id_number" title="番号:{{$click_personnel_data[0]->personnel_id}}">名前</span>
                            <input type="text" name="name" maxlength="32" style="width:140px;" value="{{$click_personnel_data[0]->name}}" data-toggle="tooltip" title="人員の名称を入力します">
                        </p>
                    </div>

                    <div class="col">
                        上位:
                        @if(isset($top_department))
                        <a href="{{ route('index')}}">{{$top_department[0]->name}}</a>
                        @else
                        <a href="{{ route('plbs01.show',[session('client_id'),$click_personnel_data[0]->high_id])}}">{{$data[0]->name}}</a>
                        @endif
                    </div>

                    <div class="col" style="padding:0px">
            
                    </div>
                </div>

                <div class="row margin-reset">
                    <div class="col-4">
                        <p>管理者番号：<input type="text" id="management_number" name="management_number" maxlength="10" value="{{$click_personnel_data[0]->management_personnel_id}}" style="width:100px;"
                        data-toggle="tooltip" title="部署情報を修正、抹消できる管理者を変更する場合、ここを修正します 管理者自身とシステム管理者だけが修正できます"></p>
                    </div>
                    <div class="col-3" style="padding:0px">
                        <p>管理者名：<a href="{{ route('plbs01.show',[session('client_id'),$click_personnel_data[0]->management_personnel_id])}}">{{$click_management_lists[0]}}</a></p>
                    </div>
                    <div class="col" style="padding:0px">
                    <p>管理者検索：
                        <input type="search" id="search-list" list="keywords" style="width:150px;" autocomplete="on" maxlength="32"
                        
                        data-toggle="tooltip" title="入力に該当した人員の候補を一覧に表示します。表示された人員を選択した場合、その番号が管理者人員番号に表示されます。">
                        <datalist id="keywords">
                        
@for($j = 0; $j < count($all_personnel_data);$j++)
@if($all_personnel_data[$j]->system_management == 1)
                            <option value="{{$all_personnel_data[$j]->name}}" label="{{$all_personnel_data[$j]->personnel_id}}"></option>
@endif
@endfor
                        </datalist>
                    </p>
                    </div>
                </div>

                <div class="row margin-reset">
                    <div class="col-5">
                        <p>メールアドレス<input type="email" name="email" maxlength="64" value="{{$click_personnel_data[0]->email}}"></p>
                    </div>
                    <div class="col-4" style="padding:0px;margin-left:-68px">
                        <p id="login">パスワード<input id="password" type="password" maxlength="32" name="password">
                    @if($click_personnel_data[0]->login_authority == "1") 
                        <input type="checkbox" onclick="passwordOn()">
                    @endif
                        </p>
                    </div>
                    <div class="col">
                        <button type="submit" formaction="{{route('psji01.send')}}" formmethod="post">メール送信</button>
                    </div>
                </div>

                {{-- 状態,システム管理者,運用開始日,運用終了日 --}}
                @include('pscm01.pscm02', ['click_personnel_data' => $click_personnel_data, 'operation_date' => $operation_date])

                <input type="hidden" id="remarks" name="remarks" value="{{$click_personnel_data[0]->remarks}}">

                <div class="row margin-reset" id="remarks-field" style="display:none"">
                    <div>
                        備考
                    </div>
                    <div>
                        <textarea id="remarks_set" onchange = "remarks(this value)" maxlength="512" style="width:800px; height: 60px;">{{$click_personnel_data[0]->remarks}}</textarea>
                    </div>
                </div>

                <div class="row" id="little-information-field" style="display:none">
                    <p>登録日:{{$click_personnel_data[0]->created_at}} 修正日:{{$click_personnel_data[0]->updated_at}}</p>
                </div>

                <div class="row margin-reset">
                    <div class="col">
                        <div style="display:inline-flex">
                        <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.ok')}}" alt="確定" onclick="submit();"
                        id="updateOn" data-toggle="tooltip" title="クリックにより、登録、更新を確定します" style="opacity: 0.3;" disabled>
    </form>
                        {{-- 操作ボタン --}}
                        @include('pscm01.pscm08', ['click_personnel_data' => $click_personnel_data, 'click_id' => $click_id])
                        {{-- 削除有効化,更新有効化 --}}
                        @include('pscm01.pscm01')
                        </div>
                    </div>
                </div>
            </div>
    {{-- 人員の詳細表示　ここまで--}}
    @endif
        <div id="list-open-button" style="display:none;" onclick="listOn()">
            <p style="text-align:center; cursor: hand; cursor:pointer; background:#99CCFF; border:solid 1px;">↓</p>
        </div>
        <div class="list-area" id="list">
            <div class="department-area margin-reset">
                <div class="row">
                    {{-- 部署一覧操作画面 --}}
                    @include('plbs01.plbs02', 
                        [
                            'top_department' => $top_department, 
                            'count_personnel' => $count_personnel,
                            'department_max' => $pagination_object->department_max,
                            'count_department' => $count_department,
                            'department_data' => $department_data
                        ]
                    )
                </div>
                {{-- 部署一覧表示画面 --}}
                @include('plbs01.plbs01', ['departments' => $pagination_object->departments])
            </div>
@if( empty(session('click_code')) or session('click_code') == "bs")
            <div class="personnel-area" style="padding-top:5px">
                <div class="row">
                    {{-- 人事操作画面 --}}
                    @include('plji01.plji02',
                        [
                            'top_department' => $top_department,
                            'count_department' => $count_department,
                            'count_personnel' => $count_personnel,
                            'personnel_max' => $pagination_object->personnel_max,
                            'personnel_data' => $personnel_data 
                        ]
                    )
                </div>
                {{-- 人事一覧表示画面 --}}
                @include('plji01.plji01', ['names' => $pagination_object->names])
            </div>
        </div>
    </div>
@endif
    {{-- コメント　詳細画面ここまで --}}

@endsection
