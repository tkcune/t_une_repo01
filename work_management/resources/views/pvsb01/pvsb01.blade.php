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

                    <form action="{{ route('pa0001.clipboard',"sb00000000")}}" method="get">
                        @csrf
                        <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.copy')}}" alt="複写" onclick="submit();" id="copyTarget" data-toggle="tooltip" title="クリックにより、詳細領域のデータをクリップボードに複写します">
                    </form>
                    <button class="main_button_style" type="button" id="tree_change_display" data-toggle="tooltip" title="ツリーを表示します" onclick="displayOn()">
                        <img class="main_button_img" src="data:image/png;base64,{{Config::get('base64.tree')}}" alt="開く">
                    </button>

                    {{--動作の為に非表示で設置--}}
                    <form action="#" method="post">
                        @csrf
                        @method('post')
                        <input type="submit" id="delete" value="削除" style="display:none;" data-toggle="tooltip" title="削除有効化をチェックした状態でのクリックにより、詳細領域のデータを下位ツリーのデータを含めて削除します" disabled>
                    </form>
                    {{--ここまで--}}

                    <input type="checkbox" onclick="deleteOn4()" data-toggle="tooltip" title="チェックを入れることで削除ボタンがクリックできるようになります（削除権限がある場合）">
                    <font size="-2" color="red">削除有効化</font>
                </div>
            </div>
        </div>
    </div>

    <div id="list-open-button" style="display:none;" onclick="listOn()">
        <p style="text-align:center; cursor: hand; cursor:pointer; background:#99FCCF; border:solid 1px;">↓</p>
    </div>

    <div class="list-area" id="list">
        <div class="department-area margin-reset">
            <div class="row">
                {{-- ツリー操作機能　--}}
                <div class="col-4" style="display:inline-flex; padding-top:15px;">
                    <p>配下場所</p>
                    <form action="{{ route('pssb01.create') }}" method="get">
                        <input type="hidden" id="high_new" name="high" value="sb00000001">
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

                    <form action="{{ route('pssb01.copy') }}" method="post">
                        @csrf
                        @method('post')
                        <input type="hidden" name="client_id" value="{{ session('client_id') }}">
                        <input type="hidden" id="copy" name="copy_id" value="{{session('clipboard_id')}}">
                        <input type="hidden" id="high_insert" name="high_id" value="sb00000000">
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧に挿入します 移動元は消えません">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.insert')}}" alt="挿入" disabled style="opacity:0.3">
                        </button>
                    </form>

                    <form action="{{ route('ptcm01.store') }}" method="post">
                        @csrf
                        @method('post')
                        <input type="hidden" name="client_id" value="{{ session('client_id') }}">
                        <input type="hidden" id="projection_source" name="projection_source_id" value="{{session('clipboard_id')}}">
                        <input type="hidden" id="high_projection" name="high_id" value="sb00000000">
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧にショートカットして投影します 移動元は消えません">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.ji')}}" alt="投影" disabled style="opacity:0.3">
                        </button>
                    </form>
                </div>
                {{-- ツリー操作機能ここまで　--}}

                {{-- ページネーション--}}
                <div class="col-3" style="padding-top:15px;">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination pagination-sm">
                            <li class="page-item">
                                @if(!empty($_POST['search']))
                                    <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),'sb00000000','count'=>1,'search'=>$_POST['search']]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                @else
                                    <a class="page-link" href="{{ route('pssb01.index',['count'=>1]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                @endif
                            </li>

                            <li class="page-item">
                                @if(!empty($_POST['search']))
                                    @if($count_space <= 1)
                                    <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),'sb00000000','count'=>1,'search'=>$_POST['search']]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&lt;</span>
                                    </a>
                                    @else
                                    <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),'sb00000000','count'=>$count_space-1,'search'=>$_POST['search']]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&lt;</span>
                                    </a>
                                    @endif
                                @else
                                    @if($count_space <= 1)
                                    <a class="page-link" href="{{ route('pssb01.index',['count'=>$count_space-1]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&lt;</span>
                                    </a>
                                    @else
                                    <a class="page-link" href="{{ route('pssb01.index',['count'=>$count_space-1]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&lt;</span>
                                    </a>
                                    @endif
                                @endif
                            </li>

                            {{$count_space}}/{{$space_details['max']}}&nbsp;&nbsp;{{$space_details['count']}}件

                            <li class="page-item">
                                @if(!empty($_POST['search']))
                                    @if($count_space < $space_details['max'])
                                    <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),'sb00000000','count'=>$count_space+1,'search'=>$_POST['search']]) }}" aria-label="Next">
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                    @else
                                    <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),'sb00000000','count'=>$space_details['max'],'search'=>$_POST['search']]) }}" aria-label="Next">
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                    @endif
                                @else
                                    @if($count_space < $space_details['max'])
                                    <a class="page-link" href="{{ route('pssb01.index',['count'=>$count_space+1]) }}" aria-label="Next">
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                    @else
                                    <a class="page-link" href="{{ route('pssb01.index',['count'=>$space_details['max']]) }}" aria-label="Next">
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                    @endif
                                @endif
                            </li>

                            <li class="page-item">
                                @if(!empty($_POST['search']))
                                    <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),'sb00000000','count'=>$space_details['max'],'search'=>$_POST['search']]) }}" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                @else
                                    <a class="page-link" href="{{ route('pssb01.index',['count'=>$space_details['max']]) }}" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                @endif
                            </li>
                        </ul>
                    </nav>
                </div>
                {{-- ページネーションここまで--}}

                {{-- 検索機能　--}}
                <div class="col-4" style="display:inline-flex; padding-top:15px">
                    <p>場所</p>
                    <form action="{{ route('pssb01.search',[session('client_id'),'sb00000000']) }}" method="post">
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
                <div class="col" style="padding-top:15px" onclick="listOn()">
                    <p style="cursor: hand; cursor:pointer;">✕</p>
                </div>
            </div>

            {{-- 検索機能ここまで　--}}

            {{-- 作業場所一覧部分 --}}
            <div class="row margin-reset">
                <div class="col">
                    <div class="border border-dark">
                        <table id="sb-table" class="sb-table table_sticky-info table table-striped sort-table" style="margin-bottom:0px;margin-top:0px;">
                            <thead>
                                <tr>
                                    <th widht="100">番号</th>
                                    <th width="300">名称</th>
                                    <th width="300">上位</>
                                    <th width="300">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($space_details['data'] as $space['data'])
                                <tr>
                                    <td>{{$space['data']->space_id}}</td>
                                    <td><a href="{{ route('pssb01.show',[session('client_id'),$space['data']->space_id])}}" data-toggle="tooltip" title="クリックにより、当該作業場所に遷移します">{{$space['data']->name}}</a></td>
                                    <td>@if(isset($space['data']->high_id))<a href="{{ route('pssb01.show',[session('client_id'),$space['data']->high_id])}}" data-toggle="tooltip" title="">{{$space['data']->high_name}}</a>@endif</td>
                                    <td>【<a href="{{ route('pa0001.clipboard',$space['data']->space_id)}}">複写</a>】
                                        【<p id="sb_list_delete{{$loop->index}}" name="sb_delete" style="pointer-events: none; display:inline-block; text-decoration:underline; margin:0px;" onclick="event.preventDefault(); document.getElementById('delete{{$loop->index}}').submit();">削除</p>】
                                        <form id="delete{{$loop->index}}" action="{{ route('pssb01.destroy',[session('client_id'),$space['data']->space_id])}}" method="post" style="display: none;">
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
        {{-- 作業場所一覧部分ここまで--}}

        {{-- 人員詳細部分 --}}
        <div class="personnel-area" style="padding-top:5px">
            <div class="row">
                <div class="col-4" style="display:inline-flex;">
                    {{-- ツリー操作機能　--}}
                    <p>所属人員</p>
                    <form action="{{ route('psji01.index') }}" method="get">
                        <input type="hidden" id="ji_high_new" name="high" value="ji00000001">
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、詳細情報に属する下位情報を新規登録する詳細画面に遷移します">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.new')}}" alt="新規">
                        </button>
                    </form>

                    <form action="{{ route('psbs01.hierarchyUpdate',[session('client_id')]) }}" method="post">
                        @csrf
                        @method('patch')
                        <input type="hidden" id="ji_high_move" name="high_id" value="">
                        <input type="hidden" id="ji_lower_move" name="lower_id" value="{{session('clipboard_id')}}">
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧に移動します 移動元からは抹消されます">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.move')}}" alt="移動" disabled style="opacity:0.3">
                        </button>
                    </form>

                    <form action="{{ route('psji01.copy') }}" method="post">
                        @csrf
                        @method('post')
                        <input type="hidden" name="client_id" value="{{ session('client_id') }}">
                        <input type="hidden" id="copy" name="copy_id" value="{{session('clipboard_id')}}">
                        <input type="hidden" id="high_insert" name="high_id" value="ji00000000">
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧に挿入します 移動元は消えません">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.insert')}}" alt="挿入" disabled style="opacity:0.3">
                        </button>
                    </form>

                    <form action="{{ route('ptcm01.store') }}" method="post">
                        @csrf
                        @method('post')
                        <input type="hidden" name="client_id" value="{{ session('client_id') }}">
                        <input type="hidden" id="projection_source" name="projection_source_id" value="{{session('clipboard_id')}}">
                        <input type="hidden" id="high_projection" name="high_id" value="ji00000000">
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
                                @if(!empty($_POST['search2']))
                                <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),'sb00000000','count'=>1,'search'=>$_POST['search2']]) }}" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                                @else
                                <a class="page-link" href="{{ route('pssb01.index',['count'=>1]) }}" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                                @endif
                            </li>

                            <li class="page-item">
                                @if(!empty($_POST['search2']))
                                    @if($count_personnel <= 1)
                                    <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),'sb00000000','count'=>1,'search'=>$_POST['search2']]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&lt;</span>
                                    </a>
                                    @else
                                    <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),'sb00000000','count'=>$count_personnel-1,'search'=>$_POST['search2']]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&lt;</span>
                                    </a>
                                    @endif
                                @else
                                    @if($count_personnel <= 1)
                                    <a class="page-link" href="{{ route('pssb01.index',['count'=>$count_personnel-1]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&lt;</span>
                                    </a>
                                    @else
                                    <a class="page-link" href="{{ route('pssb01.index',['count'=>$count_persoonel-1]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&lt;</span>
                                    </a>
                                    @endif
                                @endif
                            </li>

                            {{$count_personnel}}/{{$personnel_details['max']}}&nbsp;&nbsp;{{$personnel_details['count']}}件

                            <li class="page-item">
                                @if(!empty($_POST['search2']))
                                    @if($count_personnel < $personnel_details['max'])
                                    <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),'sb00000000','count'=>$count_personnel+1,'search'=>$_POST['search2']]) }}" aria-label="Next">
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                    @else
                                    <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),'sb00000000','count'=>$personnel_details['max'],'search'=>$_POST['search2']]) }}" aria-label="Next">
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                    @endif
                                @else
                                    @if($count_personnel < $personnel_details['max'])
                                    <a class="page-link" href="{{ route('pssb01.index',['count'=>$count_personnel+1]) }}" aria-label="Next">
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                    @else
                                    <a class="page-link" href="{{ route('pssb01.index',['count'=>$personnel_details['max']]) }}" aria-label="Next">
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                    @endif
                                @endif
                            </li>

                            <li class="page-item">
                                @if(!empty($_POST['search2']))
                                    <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),'sb00000000','count'=>$personnel_details['max'],'search'=>$_POST['search2']]) }}" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                @else
                                    <a class="page-link" href="{{ route('pssb01.index',['count'=>$personnel_details['max']]) }}" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                @endif
                            </li>
                        </ul>
                    </nav>
                </div>
                {{-- ページネーションここまで--}}

                {{-- 検索機能　--}}
                <div class="col-4" style="display:inline-flex;">
                    <p>氏名</p>
                    <form action="{{ route('pssb01.search',[session('client_id'),'sb00000000'])}}" method="post">
                        @csrf
                        @method('post')
                        @if(!empty($_POST['search2']))
                        <input type="text" name="search2" class="top" maxlength="32" value="{{ $_POST['search2'] }}">
                        @else
                        <input type="text" name="search2" class="top" maxlength="32">
                        @endif
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、検索文字に従い検索し、一覧に表示するレコードを限定します。文字が入力されていない場合は、全件を表示します" type="submit">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.search')}}" alt="検索">
                        </button>
                    </form>
                </div>
                {{-- 検索機能ここまで　--}}

                {{-- 人員一覧部分 --}}
                <div class="row margin-reset">
                    <div class="col">
                        <div class="border border-dark">
                            <table id="ji-table" class="ji-table table_sticky table table-striped" style=" margin-bottom:0px;margin-top:0px;">
                                <thead>
                                    <tr>
                                        <th width="102">人員番号</th>
                                        <th width="100">氏名</th>
                                        <th width="130">所属部署</th>
                                        <th width="80">状態</th>
                                        <th width="60">ID</th>
                                        <th width="100">PW更新</th>
                                        <th width="80">権限</th>
                                        <th width="160">操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($personnel_details['data'] as $name['data'])
                                    <tr>
                                        <td width="100">{{ $name['data']->personnel_id}}</td>
                                        @if($name['data']->operation_start_date > \Carbon\Carbon::today()->format('Y-m-d') || (!(null == $name['data']->operation_end_date) && \Carbon\Carbon::today()->format('Y-m-d') > $name['data']->operation_end_date))
                                        <td width="100"><s><a href="{{ route('plbs01.show',[session('client_id'),$name['data']->personnel_id])}}">{{$name['data']->name}}</a> </s> </td>
                                        @else
                                        <td width="100"><a href="{{ route('plbs01.show',[session('client_id'),$name['data']->personnel_id])}}">{{$name['data']->name}}</a></td>
                                        @endif
                                        <td width="130"><a href="{{ route('plbs01.show',[session('client_id'),$name['data']->high_id])}}" data-toggle="tooltip" title="クリックにより、所属部署に遷移します">{{$name['data']->high_name}}</a>
                                        <td width="80">
                                            @switch($name['data']->status)
                                            @case(10)
                                            応募
                                            @break
                                            @case(11)
                                            審査中
                                            @break
                                            @case(12)
                                            入社待
                                            @break
                                            @case(13)
                                            在職
                                            @break
                                            @case(14)
                                            休職
                                            @break
                                            @case(18)
                                            退職
                                            @break
                                            @endswitch
                                        </td>
                                        <td width="60">aaa02</td>
                                        <td width="100">{{$name['data']->password_update_day}}</td>
                                        <td width="80">---------</td>
                                        <td width="162">【<a href="{{ route('pa0001.clipboard',$name['data']->personnel_id)}}">複写</a>】
                                            【<p id="list_delete{{$loop->index}}" name="sb_delete" style="pointer-events: none; display:inline-block; text-decoration:underline; margin:0px;" onclick="event.preventDefault(); document.getElementById('delete{{$loop->index}}').submit();">削除</p>】
                                            <form id="delete{{$loop->index}}" action="{{ route('psji01.destroy',[session('client_id'),$name['data']->personnel_id])}}" method="post" style="display: none;">
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
    </div>
    <!-- 郵便番号検索は→を一旦利用 https://www.webdesign-fan.com/ajaxzip3  -->
    <script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>
    @endsection
