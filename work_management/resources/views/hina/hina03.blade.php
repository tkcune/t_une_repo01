@extends('pc0001.pc0001')
<!-- 概要画面　雛形テンプレート -->
@section('content')
<div class="col border border-primary" style="padding:10px;">
    <div class="details-area border border-dark bg-warning" style="padding:10px;" id="parent">
        <div class="row">
            <div class="col-2" style="margin-top:-5px; margin-right:-12px">
                <h2>概要</h2>
            </div>

            <div class="col">
                <p>ここは概要画面です</p>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div style="display:inline-flex">
                    {{-- 操作ボタン 複写・再表示・削除--}}
                    @include('hina.parts.parts05')
                </div>
            </div>
        </div>
    </div>

    <div id="list-open-button" style="display:none;" onclick="listOn()">
        <p style="text-align:center; cursor: hand; cursor:pointer; background:#99FCCF; border:solid 1px;">↓</p>
    </div>

    {{-- 画面上部の詳細部分 --}}
    <div class="list-area" id="list">
        <div class="area">
            <div class="row">
                <div>
                    {{-- 操作画面 --}}
                    @include('hina.parts.parts02')
                    <!-- ページネーションオブジェクトをコントローラーで設定し変数を受け渡す -->
                </div>

                <div class="row margin-reset">
                    <div class="col">
                        {{-- 一覧画面 --}}
                        @include('hina.parts.parts06')
                        <!-- ページネーションオブジェクトをコントローラーで設定し変数を受け渡す -->
                    </div>
                </div>
            </div>
        </div>
        {{-- 画面上部の表示ここまで--}}

        {{-- 画面中部の詳細部分 --}}
        <div class="area">
            <div class="row">
                <div>
                    {{-- 操作画面 --}}
                    @include('hina.parts.parts07')
                    <!-- ページネーションオブジェクトをコントローラーで設定し変数を受け渡す -->
                </div>

                <div class="row margin-reset">
                    <div class="col">
                        {{-- 一覧画面 --}}
                        @include('hina.parts.parts01')
                        <!-- ページネーションオブジェクトをコントローラーで設定し変数を受け渡す -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
