@extends('pc0001.pc0002')

@section('content')

    {{-- コメント　詳細画面ここから --}}
    {{-- 部署の詳細表示--}}
    <div class="col border border-primary">

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
                <div class="row">
                    <div class="col-4" id="page_name">
                        <h2>部署詳細</h2>
                    </div>
                    <div id="name_value" class="col-4" style="display: flex;">
                        <p>
                            <span data-toggle="tooltip" id="id_number" title="番号:{{$department_details_object->department_id}}">部署名</span>
                            <input type="text" name="name" maxlength="32" style="width:140px;"
                            data-toggle="tooltip" title="部署の名称を入力します" @if(!empty(old('name'))) value="{{ old('name') }}" @else value= "{{$department_details_object->name}}"@endif>
                            <custom-tooltip title="番号:{{$department_details_object->department_id}}"></custom-tooltip>
                        </p>
                    </div>
                    @if(isset($department_details_object->high_name))
                    <div class="col" style="margin-top:5px;">
                        <p>上位:<a href="{{ route('plbs01.show',[session('client_id'),$department_details_object->high_id])}}" data-toggle="tooltip" title="クリックにより、上位部署に遷移します">{{$department_details_object->high_name}}</a></p>
                    </div>
                    @endif
                </div>
                {{-- 管理者番号、管理者名、管理者検索 --}}
                <div class="margin-reset row" id="management_line">
                    <div class="col-4"">
                        <p>管理者番号：<input type="text" id="management_number" name="management_number" maxlength="10" 
                            @if(!empty(old('management_number'))) value="{{ old('management_number') }}" @else value="{{$department_details_object->management_personnel_id}}" @endif 
                            style="width:100px;" data-toggle="tooltip" title="部署情報を修正、抹消できる管理者を変更する場合、ここを修正します 管理者自身とシステム管理者だけが修正できます">
                            <custom-tooltip title="部署情報を修正、抹消できる管理者を変更する場合、ここを修正します 管理者自身とシステム管理者だけが修正できます"></custom-tooltip>
                        </p>
                    </div>
                    <div class="col-3" style="padding:0px">
                        <p>管理者名：<a href="{{ route('plbs01.show',[session('client_id'),$department_details_object->management_personnel_id])}}">{{$department_details_object->management_name}}</a></p>
                    </div>
                    <div class="col" style="padding:0px;">
                        <p>管理者検索：
                            <input type="search" id="search-list" list="keywords" style="width: auto;" autocomplete="on" maxlength="32"
                            data-toggle="tooltip" title="入力に該当した人員の候補を一覧に表示します。表示された人員を選択した場合、その番号が管理者人員番号に表示されます。">
                            <datalist id="keywords">
                            @foreach($system_management_lists as $system_management_list)
                                <option value="{{$system_management_list->name}}" label="{{$system_management_list->personnel_id}}"></option>
                            @endforeach
                            </datalist>
                            <custom-tooltip title="入力に該当した人員の候補を一覧に表示します。表示された人員を選択した場合、その番号が管理者人員番号に表示されます。"></custom-tooltip>
                        </p>
                    </div>
                </div>
                {{-- 状態、責任者、運用開始日、運用終了日 --}}
                <div style="display: flex;">
                    <div>
                        状態:
                    </div>
                    <div style="display: flex;">
                        <select name="status" data-toggle="tooltip" title="部署の状態を選択します">
                            @if(!empty(old('status')))
                            <option value="10" @if(old('status') == "10") selected @endif>開設提案</option>
                            <option value="11" @if(old('status') == "11") selected @endif>審査</option>
                            <option value="12" @if(old('status') == "12") selected @endif>開設待</option>
                            <option value="13" @if(old('status') == "13") selected @endif>稼働中</option>
                            <option value="14" @if(old('status') == "14") selected @endif>休止</option>
                            <option value="18" @if(old('status') == "18") selected @endif>廃止</option>
                            @else
                            <option value="10" @if($department_details_object->status == "10") selected @endif>開設提案</option>
                            <option value="11" @if($department_details_object->status == "11") selected @endif>審査</option>
                            <option value="12" @if($department_details_object->status == "12") selected @endif>開設待</option>
                            <option value="13" @if($department_details_object->status == "13") selected @endif>稼働中</option>
                            <option value="14" @if($department_details_object->status == "14") selected @endif>休止</option>
                            <option value="18" @if($department_details_object->status == "18") selected @endif>廃止</option>
                            @endif
                        </select>
                        <div><custom-tooltip title="部署の状態を選択します"></custom-tooltip></div>
                    </div>
                    <div>
                        責任者:
                    </div>
                    <div style="display: flex;">
                        <select name="responsible_person_id" style="width:90px; margin:0px;" data-toggle="tooltip" title="部署の責任者を選択します">
                            <option value="{{$department_details_object->responsible_person_id}}">{{$department_details_object->responsible}}</option>
                       
                            @for($i = 0;$i < count($personnel_data);$i++)
                            @if($personnel_data[$i]->high_id == $department_details_object->department_id)
                            <option value="{{$personnel_data[$i]->personnel_id}}" >{{$personnel_data[$i]->name}}</option>
                            @endif
                            @endfor
                        </select>
                        <div><custom-tooltip title="部署の責任者を選択します"></custom-tooltip></div>
                    </div>
                    <div>
                        運用開始日<input name="start_day" type="date" style="width:140px; margin:0px;" @if(!empty(old('start_day'))) value="{{ old('start_day')}}" @else value="{{$department_details_object->operation_start_date}}" @endif>
                    </div>
                    <div>
                        運用終了日<input name="finish_day" type="date" style="width:140px; margin:0px;" @if(!empty(old('finish_day'))) value="{{ old('finish_day')}}" @else value="{{$department_details_object->operation_end_date}}" @endif>
                    </div>

                    &emsp;&emsp;

                    <div style="display: flex;">        
                        <button class="main_button_style" type="button" id="remarks_change_display" onclick="remarksOn()" data-toggle="tooltip" title="クリックにより、備考及び登録日などの情報を開きます">
                            <img class="remarks_button" src="data:image/png;base64,{{Config::get('base64.updown')}}" alt="開閉" >
                        </button>
                        <div><custom-tooltip title="クリックにより、備考及び登録日などの情報を開きます"></custom-tooltip></div>
                    </div>
                </div>
                {{-- 備考 --}}
                <input type="hidden" id="remarks" name="remarks" value="{{$department_details_object->remarks}}">

                <div class="row margin-reset" id="remarks-field" style="display:none">
                    <div>
                        備考
                    </div>
                    <div>
                        <textarea id="remarks_set" onchange = "remarks(this value)" maxlength="512" style="width: 100%; height: 60px;">{{$department_details_object->remarks}}</textarea>
                    </div>
                </div>

                <div class="row" id="little-information-field" style="display:none">
                    <p>
                        登録日:{{$department_details_object->created_at}} 修正日:{{$department_details_object->updated_at}}
                        登録者:<a href="{{ route('plbs01.show',[session('client_id'),$department_details_object->responsible_person_id])}}">{{$department_details_object->responsible}}</a>
                    </p>
                </div>
                <!-- 操作ボタン領域 -->
                <div class="row" style="margin-top: 3px;">
                    <div class="col">
                    <div style="display:inline-flex">
                    <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.ok')}}" alt="確定" id="updateOn" onclick="submit();" data-toggle="tooltip" title="クリックにより、登録、更新を確定します" 
                    style="opacity: 0.3;" disabled>
    </form>
                    {{-- 操作ボタン --}}
                    @include('pscm01.pscm02', ['click_id' => $click_id, 'click_department_data' => $department_details_object])
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
            <input type="hidden" id="personnel_id" name="personnel_id" value="{{$personnel_details_object->personnel_id}}">
            <input type="hidden" name="client_id" value="{{ session('client_id') }}">

            @if(substr($click_id,0,2) == "ta")
            <div class="details-area border border-dark bg-info" style="padding:10px;" id="parent">
            @else
            <div class="details-area border border-dark bg-warning" style="padding:10px;" id="parent">
            @endif
                <!-- 人事詳細,名前,上位 -->
                <div class="row">
                    <div class="col-4" id="page_name" style="margin-top:-5px; margin-right:-12px">
                        <h2>人員詳細</2>
                    </div>
                    <div class="col-4" style="margin-right:-10px">
                        <p id="palent">
                            <span data-toggle="tooltip" id="id_number" title="番号:{{$personnel_details_object->personnel_id}}">名前</span>
                            <input type="text" name="name" maxlength="32" style="width:140px;" value="{{$personnel_details_object->name}}" data-toggle="tooltip" title="人員の名称を入力します">
                            <custom-tooltip title="人員の名称を入力します"></custom-tooltip>
                        </p>
                    </div>

                    <div class="col">
                        上位:
                        <a href="{{ route('plbs01.show',[session('client_id'),$personnel_details_object->high_id])}}">{{$personnel_details_object->high_name}}</a>
                    </div>
                </div>
                <!-- 管理者番号,管理者名,管理者検索 -->
                <div class="row margin-reset">
                    <div class="col-4">
                        <p>管理者番号：<input type="text" id="management_number" name="management_number" maxlength="10" value="{{$personnel_details_object->management_personnel_id}}" style="width:100px;"
                        data-toggle="tooltip" title="部署情報を修正、抹消できる管理者を変更する場合、ここを修正します 管理者自身とシステム管理者だけが修正できます">
                        <custom-tooltip title="部署情報を修正、抹消できる管理者を変更する場合ここを修正します。
                        管理者自身とシステム管理者だけが修正できます"></custom-tooltip></p>
                    </div>
                    <div class="col-3" style="padding:0px">
                        <p>管理者名：<a href="{{ route('plbs01.show',[session('client_id'),$personnel_details_object->management_personnel_id])}}">{{$personnel_details_object->management_name}}</a></p>
                    </div>
                    <div class="col" style="padding:0px">
                        <p>管理者検索：
                            <input type="search" id="search-list" list="keywords" style="width:150px;" autocomplete="on" maxlength="32"        
                            data-toggle="tooltip" title="入力に該当した人員の候補を一覧に表示します。表示された人員を選択した場合、その番号が管理者人員番号に表示されます。">
                            <datalist id="keywords">
                        
                            @foreach($system_management_lists as $system_management_list)
                                <option value="{{$system_management_list->name}}" label="{{$system_management_list->personnel_id}}"></option>
                            @endforeach
                            </datalist>
                            <custom-tooltip title="入力に該当した人員の候補を一覧に表示します。
                            表示された人員を選択した場合その番号が管理者人員番号に表示されます"></custom-tooltip>
                        </p>
                    </div>
                </div>

                <!-- メールアドレス、パスワード、メール送信 -->
                <div class="row margin-reset">
                    <div class="col-5">
                        <p>メールアドレス<input type="email" name="email" maxlength="64" value="{{$personnel_details_object->email}}"></p>
                    </div>
                    <div class="col-4" style="padding:0px;margin-left:-68px">
                        <p id="login">パスワード<input id="password" type="password" maxlength="32" name="password">
                        @if($personnel_details_object->login_authority == "1") 
                            <input type="checkbox" onclick="passwordOn()">
                        @endif
                        </p>
                    </div>
                    <div class="col">
                        <button type="submit" formaction="{{route('psji01.send')}}" formmethod="post">メール送信</button>
                    </div>
                </div>

                {{-- 状態,システム管理者,運用開始日,運用終了日 --}}
                <div class="row margin-reset">
                    <div class="col">
                        <p>状態:
                            <select name="status" data-toggle="tooltip" title="人員の状態を選択します">
                                <option value="10" @if($personnel_details_object->status == "10") selected @endif>応募</option>
                                <option value="11" @if($personnel_details_object->status == "11") selected @endif>審査</option>
                                <option value="12" @if($personnel_details_object->status == "12") selected @endif>入社待</option>
                                <option value="13" @if($personnel_details_object->status == "13") selected @endif>在職</option>
                                <option value="14" @if($personnel_details_object->status == "14") selected @endif>休職</option>
                                <option value="18" @if($personnel_details_object->status == "18") selected @endif>退職</option>
                            </select>
                            <custom-tooltip title="人員の状態を選択します"></custom-tooltip>
                            システム管理者:
                            <input type="checkbox" name="system_management" value="1" data-toggle="tooltip" title="人員がシステム管理者かどうかを設定します"
                            @if($personnel_details_object->system_management == "1") checked @endif>
                            <custom-tooltip title="人員がシステム管理者かどうかを設定します"></custom-tooltip>
                            ログイン:
                            <input name="login_authority" type="checkbox" value="1" onclick="loginDisabled()" @if($personnel_details_object->login_authority == "1") checked @endif>
                        
                            運用開始日<input name="start_day" type="date" style="margin:0px;" value="{{$personnel_details_object->operation_start_date}}">
                            運用終了日<input name="finish_day" type="date" style="margin:0px;" value="{{$personnel_details_object->operation_end_date}}">

                            <button class="main_button_style" type="button" id="remarks_change_display" onclick="remarksOn()" data-toggle="tooltip" title="クリックにより、備考及び登録日などの情報を開きます">
                                <img class="remarks_button" src="data:image/png;base64,{{Config::get('base64.updown')}}" alt="開閉" >
                            </button>
                            <custom-tooltip title="備考及び登録日などの情報を開きます"></custom-tooltip>
                        </p>
                    </div>
                </div>
                <!-- 備考ボタン -->
                <input type="hidden" id="remarks" name="remarks" value="{{$personnel_details_object->remarks}}">

                <!-- 備考 -->
                <div class="row margin-reset" id="remarks-field" style="display:none"">
                    <div>
                        備考
                    </div>
                    <div>
                        <textarea id="remarks_set" onchange = "remarks(this value)" maxlength="512" style="width:800px; height: 60px;">{{$personnel_details_object->remarks}}</textarea>
                    </div>
                </div>

                <div class="row" id="little-information-field" style="display:none">
                    <p>登録日:{{$personnel_details_object->created_at}} 修正日:{{$personnel_details_object->updated_at}}</p>
                </div>
                <!-- 操作ボタン領域 -->
                <div class="row margin-reset">
                    <div class="col">
                        <div style="display:inline-flex">
                        <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.ok')}}" alt="確定" onclick="submit();"
                        id="updateOn" data-toggle="tooltip" title="クリックにより、登録、更新を確定します" style="opacity: 0.3;" disabled>
    </form>
                        {{-- 操作ボタン --}}
                        @include('pscm01.pscm03', ['click_personnel_data' => $personnel_details_object, 'click_id' => $click_id])
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
            <div class="department-area">
                <div>
                    {{-- 部署一覧操作画面 --}}
                    @include('plbs01.plbs02', 
                        [
                            'click_data' => $click_data,
                            'count_personnel' => $pagination_object->count_personnel,
                            'department_max' => $pagination_object->department_max,
                            'count_department' => $pagination_object->count_department,
                            'total_department' => $pagination_object->total_department,
                            'select_id' => $select_id
                        ]
                    )
                </div>
                {{-- 部署一覧表示画面 --}}
                @include('plbs01.plbs01', ['departments' => $pagination_object->departments])
            </div>
@if( empty(session('click_code')) or session('click_code') == "bs")
            <div class="personnel-area" style="padding-top:5px">
                <div>
                    {{-- 人事操作画面 --}}
                    @include('plji01.plji02',
                        [
                            'click_data' => $click_data,
                            'count_department' => $pagination_object->count_department,
                            'count_personnel' => $pagination_object->count_personnel,
                            'personnel_max' => $pagination_object->personnel_max,
                            'total_personnel' => $pagination_object->total_personnel,
                            'select_id' => $select_id
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
