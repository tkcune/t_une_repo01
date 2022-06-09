@extends('pc0001.pc0001')

@section('js')
<script src="{{ asset('js/pssb01/pssb01.js') }}" defer></script>
@endsection

{{-- テーブルソート --}}
@section('script')
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
@endsection

@section('content')
{{--作業場所トップページ　--}}
<div class="col border border-primary" style="padding:10px;">
    <div class="details-area border border-dark bg-info" style="padding:10px;" id="parent">
        <div class="row">

            <div class="col-2" style="margin-top:-5px; margin-right:-12px">
                <h2>概要</h2>
            </div>

            <div class="col">
                <p>作業を行う場所を登録します</p>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div style="display:inline-flex">
                    {{-- 操作ボタン --}}
                    @include('plsb01.plsb05')
                </div>
            </div>
        </div>
    </div>

    <div id="list-open-button" style="display:none;" onclick="listOn()">
        <p style="text-align:center; cursor: hand; cursor:pointer; background:#99FCCF; border:solid 1px;">↓</p>
    </div>

    {{-- 作業場所操作画面 --}}
    <div class="list-area" id="list">
        <div class="space-area">
            <div class="row">
                <div>
                    @include('plsb01.plsb06',
                    [
                    'count_space' => $pagination_object->count_space,
                    'space_max' => $pagination_object->space_max,
                    'total_space' => $pagination_object->total_space,
                    ]
                    )
                </div>

                {{-- 作業場所一覧部分 --}}
                <div class="row margin-reset">
                    <div class="col">
                        {{-- 作業場所表示画面 --}}
                        @include('plsb01.plsb01', ['space_details' => $pagination_object->space_details])
                    </div>
                </div>
            </div>
        </div>
        {{-- 作業場所一覧部分ここまで--}}
    </div>
</div>
@endsection
