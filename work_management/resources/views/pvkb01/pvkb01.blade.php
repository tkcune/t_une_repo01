@extends('pc0001.pc0001')

@section('js')
    <script src="{{ asset('js/pskb01/pskb01.js') }}" defer></script>
@endsection

@section('script')
    {{-- テーブルソート --}}
    <script>
    $(document).ready(function() {
        $('#kb-table').tablesorter({
            headers: {
               5: { sorter: false }
            }
        });
    });
    </script>
@endsection
    
@section('content')
    <div class="col border border-primary" style="padding:10px;">
        <div class="details-area border border-dark bg-warning" style="padding:10px;" id="parent">
            <div class="row">
                <div class="col-3" style="margin-top:-5px; margin-right:-12px">
                    <h2>掲示板概要</h2>
                </div>

                <div class="col">
                    <p>作業手順など様々な情報を記載する掲示板です</p>
                </div>
            </div>

            <div class="row">
                    <div class="col">
                        <div style="display:inline-flex">

                        <form action="{{ route('pa0001.clipboard',"kb00000000")}}" method="get">
                        @csrf
                        <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.copy')}}" alt="複写"  onclick="submit();" id="copyTarget" data-toggle="tooltip" title="クリックにより、詳細領域のデータをクリップボードに複写します">
                        </form>
                        <button class="main_button_style" type="button" id="tree_change_display" data-toggle="tooltip" title="ツリーを表示します" onclick="displayOn()">
                            <img class="main_button_img" src="data:image/png;base64,{{Config::get('base64.tree')}}" alt="開く" >
                        </button>

                        {{--動作の為に非表示で設置--}}
                        <form action="#" method="post">
                        @csrf
                        @method('post')
                        <input type="submit" id="delete" value="削除" style="display:none;" data-toggle="tooltip" title="削除有効化をチェックした状態でのクリックにより、詳細領域のデータを下位ツリーのデータを含めて削除します" disabled>
                        </form>
                        {{--ここまで--}}

                        <input type="checkbox" onclick="deleteOn3()" data-toggle="tooltip" title="チェックを入れることで削除ボタンがクリックできるようになります（削除権限がある場合）">
                        <font size="-2" color="red">削除有効化</font>
                        </div>
                    </div>
            </div>
        </div>

        <div id="list-open-button" style="display:none;" onclick="listOn()">
            <p style="text-align:center; cursor: hand; cursor:pointer; background:#99CCFF; border:solid 1px;">↓</p>
        </div>
    
        <div class="personnel-area" style="padding-top:5px">
                <div class="row">
                    {{-- ツリー操作機能　--}}
                    <div class="col-4" style="display:inline-flex">
                        <p>一覧画面</p>
                        <form action="{{ route('pskb01.create') }}" method="get">
                        <input type="hidden" id="kb_high_new" name="high" value="kb00000000">
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、詳細情報に属する下位情報を新規登録する詳細画面に遷移します">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.new')}}" alt="新規">
                        </button>
                        </form>

                        <form action="{{ route('psbs01.hierarchyUpdate',[session('client_id')]) }}" method="post">
                        @csrf
                        @method('patch')
                        <input type="hidden" id="high_move" name="high_id" value="">
                        <input type="hidden" id="lower_move" name="lower_id" value="{{session('clipboard_id')}}"> 
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧に移動します 移動元からは抹消されます">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.move')}}" alt="移動" disabled style="opacity:0.3">
                        </button>
                        </form>

                        <form action="{{ route('pskb01.copy') }}" method="post">
                        @csrf
                        @method('post')
                        <input type="hidden" name="client_id" value="{{ session('client_id') }}">
                        <input type="hidden" id="copy" name="copy_id" value="{{session('clipboard_id')}}">
                        <input type="hidden" id="high_insert" name="high_id" value="{{$board_lists[0]->board_id}}">
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧に挿入します 移動元は消えません">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.insert')}}" alt="挿入" disabled style="opacity:0.3">
                        </button>
                        </form>

                        <form action="{{ route('ptcm01.store') }}" method="post">
                        @csrf
                        @method('post')
                        <input type="hidden" name="client_id" value="{{ session('client_id') }}">
                        <input type="hidden" id="projection_source" name="projection_source_id" value="{{session('clipboard_id')}}">
                        <input type="hidden" id="high_projection" name="high_id" value="{{$board_lists[0]->board_id}}">
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧にショートカットして投影します 移動元は消えません">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.ji')}}" alt="投影" disabled style="opacity:0.3">
                        </button>
                        </form>
                    </div>
                    {{-- ツリー操作機能ここまで　--}}

                    {{-- ページネーション--}}
                    <div class="col-3">
                        <nav aria-label="Page navigation example">
                            <ul class="pagination pagination-sm">
                                <li class="page-item">
                                    <a class="page-link" href="#" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <li class="page-item">
                                <a class="page-link" href="#" aria-label="Previous">
                                    <span aria-hidden="true">&lt;</span>
                                </a>
                                </li>
                                0/0&nbsp;&nbsp;0件
                                <li class="page-item">
                                    <a class="page-link" href="#" aria-label="Next">
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    {{-- ページネーションここまで --}}

                    {{-- 検索機能　--}}
                    <div class="col-4" style="display:inline-flex">
                        <p>検索</p>
                        <form action="{{ route('pskb01.search',[session('client_id'),'kb00000000']) }}" method="post">
                        @csrf
                        @method('post')
                        @if(!empty($_POST['search']))
                            <input type="text" name="search" class="top" maxlength="32" value="{{ $_POST['search'] }}">
                        @else
                            <input type="text" name="search" class="top" maxlength="32">
                        @endif
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、検索文字に従い検索し、一覧に表示するレコードを限定します。文字が入力されていない場合は、全件を表示します" type="submit">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.search')}}" alt="検索">
                        </button>
                        </form>
                    </div>
                    {{-- 検索機能ここまで　--}}
                </div>
                
                <div class="row margin-reset">
                    <div class="col">
                        <div class="border border-dark">
                            <table id="kb-table" class="kb-table table_sticky table table-striped" style="margin-bottom:0px;margin-top:0px;">
                                <thead>
                                <tr>
                                    <th >番号</th>
                                    <th width="140">名称</th>
                                    <th width="140">上位</th>
                                    <th width="150">登録日</th>
                                    <th width="120">管理者</th>
                                    <th width="170">操作</th>
                                </thead>
                                <tbody>
                                @foreach($board_lists as $board_list)
                                    <tr>
                                    <td width="100">{{$board_list->board_id}}</td>
                                    <td width="140"><a href="{{ route('pskb01.show',[session('client_id'),$board_list->board_id])}}" data-toggle="tooltip" title="">{{$board_list->name}}</a></td>
                                    
                                    <td width="140">@if(isset($board_list->high_id))<a href="{{ route('pskb01.show',[session('client_id'),$board_list->high_id])}}" data-toggle="tooltip" title="">{{$board_list->high_name}}</a>@endif</td>
                        
                                    <td width="150">{{$board_list->created_at}}
                                    <td width="120"><a href="{{ route('plbs01.show',[session('client_id'),$board_list->management_personnel_id])}}" data-toggle="tooltip" title="">{{$board_list->management_name}}</a></td>
                                    <td width="170">【<a href="#">複写</a>】
                                    【<p id="kb_list_delete{{$loop->index}}" name="kb_delete" style="pointer-events: none; display:inline-block; text-decoration:underline; margin:0px;" onclick="event.preventDefault(); document.getElementById('delete{{$loop->index}}').submit();">削除</p>】
                                    <form id="delete{{$loop->index}}" action="#" method="post" style="display: none;">
                                    @csrf
                                    </form>
                                    </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        </div>
    </div>

@endsection