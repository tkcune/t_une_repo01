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
                @include('pscm01.pscm06', ['click_department_data' => $department_details_object, 'click_department_high_name' => $department_details_object->high_name])
                {{-- 管理者番号、管理者名、管理者検索 --}}
                @include('pscm01.pscm07', ['click_department_data' => $department_details_object, 'click_management_lists' => $click_management_lists, 'all_personnel_data' => $all_personnel_data])
                {{-- 状態、責任者、運用開始日、運用終了日 --}}
                @include('pscm01.pscm03', 
                    [
                        'click_department_data' => $department_details_object, 
                        'top_responsible' => $click_responsible_lists, 
                        'personnel_data' => $personnel_data
                    ]
                )
                {{-- 備考 --}}
                @include('pscm01.pscm05', ['click_department_data' => $department_details_object, 'click_responsible_lists' => $click_responsible_lists])

                <div class="row">
                    <div class="col">
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
            <input type="hidden" id="personnel_id" name="personnel_id" value="{{$personnel_details_object->personnel_id}}">
            <input type="hidden" name="client_id" value="{{ session('client_id') }}">

            @if(substr($click_id,0,2) == "ta")
            <div class="details-area border border-dark bg-info" style="padding:10px;" id="parent">
            @else
            <div class="details-area border border-dark bg-warning" style="padding:10px;" id="parent">
            @endif
                <!-- 人事詳細,名前,上位 -->
                @include('pscm01.pscm09', ['click_personnel_data' => $personnel_details_object])
                <!-- 管理者番号,管理者名,管理者検索 -->
                @include('pscm01.pscm10', ['click_personnel_data' => $personnel_details_object, 'all_personnel_data' => $all_personnel_data, 'click_management_lists' => $click_management_lists])

                <!-- メールアドレス、パスワード、メール送信 -->
                @include('pscm01.pscm11', ['click_personnel_data' => $personnel_details_object])

                {{-- 状態,システム管理者,運用開始日,運用終了日 --}}
                @include('pscm01.pscm02', ['click_personnel_data' => $personnel_details_object])
                <!-- 備考 -->
                @include('pscm01.pscm12', ['click_personnel_data' => $personnel_details_object])

                <div class="row margin-reset">
                    <div class="col">
                        <div style="display:inline-flex">
                        <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.ok')}}" alt="確定" onclick="submit();"
                        id="updateOn" data-toggle="tooltip" title="クリックにより、登録、更新を確定します" style="opacity: 0.3;" disabled>
    </form>
                        {{-- 操作ボタン --}}
                        @include('pscm01.pscm08', ['click_personnel_data' => $personnel_details_object, 'click_id' => $click_id])
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
                @include('plbs01.plbs01', ['department_high' => $department_high, 'departments' => $pagination_object->departments, 'responsible_lists' => $responsible_lists])
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
                @include('plji01.plji01', ['names' => $pagination_object->names, 'personnel_high' => $personnel_high])
            </div>
        </div>
    </div>
@endif
    {{-- コメント　詳細画面ここまで --}}

@endsection
