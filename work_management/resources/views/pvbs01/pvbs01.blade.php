@extends('pc0001.pc0001')

@section('content')
    {{-- 部署トップページ　--}}
    <div class="col border border-primary" style="padding:10px;">
        <div class="details-area border border-dark bg-warning" style="padding:10px;" id="parent">
            <div class="row">

                <div class="col-2" style="margin-top:-5px; margin-right:-12px">
                    <h2>概要</h2>
                </div>

                <div class="col">
                    <p>作業管理を行う部署、作業を行う部署、作業を行うにあたって対象となる取引先や部署を登録します</p>
                </div>
            </div>

            <div class="row">
                    <div class="col">
                        <div style="display:inline-flex">

                        <form action="{{ route('pa0001.clipboard',"bs00000000")}}" method="get">
                        @csrf
                        <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.copy')}}" alt="複写"  onclick="submit();" id="copyTarget" data-toggle="tooltip" title="クリックにより、詳細領域のデータをクリップボードに複写します">
                        </form>
                        <button class="main_button_style" type="button" id="tree_change_display" data-toggle="tooltip" title="ツリーを表示します" onclick="displayOn()">
                            <img class="main_button_img" src="data:image/png;base64,{{Config::get('base64.tree')}}" alt="開く" >
                        </button>

                        {{--動作の為に非表示で設置--}}
                        <form action="{{ route('psbs01.delete',[session('client_id'),$top_department[0]->department_id])}}" method="post">
                        @csrf
                        @method('post')
                        <input type="submit" id="delete" value="削除" style="display:none;" data-toggle="tooltip" title="削除有効化をチェックした状態でのクリックにより、詳細領域のデータを下位ツリーのデータを含めて削除します" disabled>
                        </form>
                        {{--ここまで--}}

                        <input type="checkbox" onclick="deleteOn()" data-toggle="tooltip" title="チェックを入れることで削除ボタンがクリックできるようになります（削除権限がある場合）">
                        <font size="-2" color="red">削除有効化</font>
                        </div>
                    </div>
            </div>
        </div>

        <div id="list-open-button" style="display:none;" onclick="listOn()">
            <p style="text-align:center; cursor: hand; cursor:pointer; background:#99CCFF; border:solid 1px;">↓</p>
        </div>
        <div class="list-area" id="list">
            <div class="department-area margin-reset">
                <div class="row">
                    {{-- ツリー操作機能　--}}
                    <div class="col-4" style="display:inline-flex; padding-top:15px;">
                        <p>配下部署</p>
                        <form action="{{ route('psbs01.index') }}" method="get">
                        @if(isset($top_department))
                            <input type="hidden" id="high" name="high" value="{{$top_department[0]->department_id}}">
                        @else
                            <input type="hidden" id="high" name="high" value="{{$departments[0]->department_id}}">
                        @endif
                        
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、詳細情報に属する下位情報を新規登録する詳細画面に遷移します">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.new')}}" alt="新規">
                        </button>
                        </form>

                        <form action="{{ route('psbs01.hierarchyUpdate',[session('client_id')]) }}" method="post">
                        @if(isset($top_department))
                            <input type="hidden" id="high_move" name="high_id" value="{{$top_department[0]->department_id}}">
                        @else
                            <input type="hidden" id="high_move" name="high_id" value="{{$departments[0]->department_id}}">
                        @endif
                        <input type="hidden" id="lower_move" name="lower_id" value="{{session('clipboard_id')}}">
                        @csrf
                        @method('patch')
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧に移動します 移動元からは抹消されます">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.move')}}" alt="移動" disabled style="opacity:0.3">
                        </button>
                        </form>

                        <form action="{{ route('psbs01.copy') }}" method="post">
                        @csrf
                        @method('post')
                        <input type="hidden" name="client_id" value="{{ session('client_id') }}">
                        <input type="hidden" id="copy" name="copy_id" value="{{session('clipboard_id')}}">
                        @if(isset($top_department))
                        <input type="hidden" id="high_insert" name="high_id" value="{{$top_department[0]->department_id}}">
                        @else
                        <input type="hidden" id="high_insert" name="high_id" value="{{$departments[0]->department_id}}">
                        @endif
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧に挿入します 移動元は消えません">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.insert')}}" alt="挿入" disabled style="opacity:0.3">
                        </button>
                        </form>

                        <form action="{{ route('ptcm01.store') }}" method="post">
                        @csrf
                        @method('post')
                        <input type="hidden" name="client_id" value="{{ session('client_id') }}">
                        <input type="hidden" id="projection_source" name="projection_source_id" value="{{session('clipboard_id')}}">
                        @if(isset($top_department))
                        <input type="hidden" id="high_projection" name="high_id" value="{{$top_department[0]->department_id}}">
                        @else
                        <input type="hidden" id="high_projection" name="high_id" value="{{$departments[0]->department_id}}">
                        @endif
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧にショートカットして投影します 移動元は消えません">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.ji')}}" alt="投影" disabled style="opacity:0.3">
                        </button>
                        </form>
                    </div>
                    {{-- ツリー操作機能ここまで　--}}

                    {{-- ページネーション--}}
                    <div class="col-3" style="padding-top:15px;">
@if(isset($select_id))
                        <nav aria-label="Page navigation example">
                            <ul class="pagination pagination-sm">
                                <li class="page-item">
                                    <a class="page-link" href="{{ route('pa0001.count_top',['department_page'=>1,'personnel_page'=>$count_personnel]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <li class="page-item">
    @if($count_department == 1)
                                <a class="page-link" href="{{ route('pa0001.count_top',['department_page'=>$count_department,'personnel_page'=>$count_personnel]) }}" aria-label="Previous">
    @else
                                <a class="page-link" href="{{ route('pa0001.count_top',['department_page'=>$count_department-1,'personnel_page'=>$count_personnel]) }}" aria-label="Previous">
    @endif
                                    <span aria-hidden="true">&lt;</span>
                                </a>
                                </li>
                                {{$count_department}}/{{$department_max}}
                                <li class="page-item">
    @if($count_department<$department_max)
                                    <a class="page-link" href="{{ route('pa0001.count_top',['department_page'=>$count_department+1,'personnel_page'=>$count_personnel,'id'=>session('client_id')]) }}" aria-label="Next">
    @else
                                    <a class="page-link" href="{{ route('pa0001.count_top',['department_page'=>$department_max,'personnel_page'=>$count_personnel,'id'=>session('client_id')]) }}" aria-label="Next">
    @endif
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="{{ route('pa0001.count_top',['department_page'=>$department_max,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$select_id]) }}" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
@else
                        <nav aria-label="Page navigation example">
                            <ul class="pagination pagination-sm">
                                <li class="page-item">
                                    <a class="page-link" href="{{ route('pa0001.count_top',['department_page'=>1,'personnel_page'=>$count_personnel]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <li class="page-item">
    @if($count_department == 1)
                                <a class="page-link" href="{{ route('pa0001.count_top',['department_page'=>$count_department,'personnel_page'=>$count_personnel]) }}" aria-label="Previous">
    @else
                                <a class="page-link" href="{{ route('pa0001.count_top',['department_page'=>$count_department-1,'personnel_page'=>$count_personnel]) }}" aria-label="Previous">
    @endif
                                    <span aria-hidden="true">&lt;</span>
                                </a>
                                </li>
                                {{$count_department}}/{{$department_max}}&nbsp;&nbsp;{{count($department_data)}}件
                                <li class="page-item">
    @if($count_department<$department_max)
                                    <a class="page-link" href="{{ route('pa0001.count_top',['department_page'=>$count_department+1,'personnel_page'=>$count_personnel]) }}" aria-label="Next">
    @else
                                    <a class="page-link" href="{{ route('pa0001.count_top',['department_page'=>$department_max,'personnel_page'=>$count_personnel]) }}" aria-label="Next">
    @endif
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="{{ route('pa0001.count_top',['department_page'=>$department_max,'personnel_page'=>$count_personnel]) }}" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                
@endif
                    </div>
                    {{-- ページネーションここまで--}}
                    {{-- 検索機能　--}}
                    <div class="col-4" style="display:inline-flex; padding-top:15px">
                        <p>部署</p>
                        @if(isset($top_department))
                        <form action="{{ route('psbs01.search',[session('client_id'),$top_department[0]->department_id])}}" method="post">
                        @elseif(isset($click_department_data))
                        <form action="{{ route('psbs01.search',[session('client_id'),$click_department_data[0]->department_id])}}" method="post">
                        @else
                        <form action="{{ route('psbs01.search',[session('client_id'),$click_personnel_data[0]->personnel_id])}}" method="post">
                        @endif
                        @csrf
                        @method('post')
                        @if(!empty($_POST['search']))
                        <input type="text" name="search" class="top" maxlength="32" value="{{ $_POST['search'] }}">
                        @else
                        <input type="text" name="search" class="top" maxlength="32">
                        @endif
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、検索文字に従い検索し、一覧に表示するレコードを限定します。文字が入力されていない場合は、全件を表示します" type="submit">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.search')}}" alt="検索" disabled style="opacity:0.3">
                        </button>
                        </form>
                    </div>

                    <div class="col" style="padding-top:15px" onclick="listOn()">
                        <p style="cursor: hand; cursor:pointer;">✕</p>
                    </div> 
                </div>

                <div class="row margin-reset">
                    <div class="col">
                        <div class="border border-dark">
                            <table class="bs-table head-table table table-striped">
                                <thead>
                                    <tr>
                                    <th width="102">部署番号</th>
                                    <th width="160">部署名</th>
                                    <th width="160">上位部署</th>
                                    <th width="80">状態</th>
                                    <th width="120">責任者</th>
                                    <th width="190">操作</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="table border border-dark">
                        <table id="bs-table" class="bs-table table table-striped border-dark table-hover" style="margin-bottom:0px">
                            <tbody>
                            @foreach($departments as $department)
                            <tr>
                            <td width="100">{{$department->department_id}}</td>
                            @if($department->operation_start_date > \Carbon\Carbon::today()->format('Y-m-d') || (!(null == $department->operation_end_date) && \Carbon\Carbon::today()->format('Y-m-d') > $department->operation_end_date))
                                <td width="160"><s><a href="{{ route('plbs01.show',[session('client_id'),$department->department_id])}}">{{$department->name}}</a></s></td>
                            @else
                                <td width="160"><a href="{{ route('plbs01.show',[session('client_id'),$department->department_id])}}">{{$department->name}}</a></td>
                            @endif
                            <td width="160"><a href="{{ route('plbs01.show',[session('client_id'),$department_high[$loop->index]->department_id])}}">{{$department_high[$loop->index]->name}}</a></td>
                            <td width="80">
                            @switch($department->status)
                                @case(10)
                                開設提案
                                @break
                                @case(11)
                                審査
                                @break
                                @case(12)
                                開設待
                                @break
                                @case(13)
                                稼働中
                                @break
                                @case(14)
                                休止
                                @break
                                @case(18)
                                廃止
                                @break
                            @endswitch
                            </td>
                            <td width="120"><a href="{{ route('plbs01.show',[session('client_id'),$department->responsible_person_id])}}">{{ $responsible_lists[$loop->index] }}</a></td>
                            <td width="192">
                            【<a href="{{ route('pa0001.clipboard',$department->department_id)}}">複写</a>】
                            【<p id="bs_list_delete{{$loop->index}}" name="bs_delete" style="pointer-events: none; display:inline-block; text-decoration:underline; margin:0px;" onclick="event.preventDefault(); document.getElementById('bs_delete{{$loop->index}}').submit();">削除</p>】
                            <form id="bs_delete{{$loop->index}}" action="{{ route('psbs01.delete',[session('client_id'),$department->department_id])}}" method="post" style="display: none;">
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
            <div class="personnel-area margin-reset">
                <div class="row">
                    <div class="col-4" style="display:inline-flex;">
                        {{-- ツリー操作機能　--}}
                        <p>所属人員</p>
                        <form action="{{ route('psji01.index') }}" method="get">
                        @if(isset($top_department))
                        <input type="hidden" id="ji_high_new" name="high" value="{{$top_department[0]->department_id}}">
                        @else
                        <input type="hidden" id="ji_high_new" name="high" value="{{$departments[0]->department_id}}">
                        @endif
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、詳細情報に属する下位情報を新規登録する詳細画面に遷移します">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.new')}}" alt="新規">
                        </button>
                        </form>

                        <form action="{{ route('psbs01.hierarchyUpdate',[session('client_id')]) }}" method="post">
                        @csrf
                        @method('patch')
                        @if(isset($top_department))
                        <input type="hidden" id="ji_high_move" name="high_id" value="{{$top_department[0]->department_id}}">
                        @else
                        <input type="hidden" id="ji_high_move" name="high_id" value="{{$departments[0]->department_id}}">
                        @endif
                        <input type="hidden" id="ji_lower_move" name="lower_id" value="{{session('clipboard_id')}}"> 
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧に移動します 移動元からは抹消されます">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.move')}}" alt="移動" disabled style="opacity:0.3">
                        </button>
                        </form>

                        <form action="{{ route('psji01.copy') }}" method="post">
                        @csrf
                        @method('post')
                        <input type="hidden" name="client_id" value="{{ session('client_id') }}">
                        @if(isset($top_department))
                        <input type="hidden" name="high_id" value="{{$top_department[0]->department_id}}">
                        @else
                        <input type="hidden" name="high_id" value="{{$departments[0]->department_id}}">
                        @endif
                        <input type="hidden" id="ji_copy_id" name="copy_id" value="{{session('clipboard_id')}}">
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧に挿入します 移動元は消えません">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.insert')}}" alt="挿入" disabled style="opacity:0.3">
                        </button>
                        </form>

                        <form action="{{ route('ptcm01.store') }}" method="post">
                        @csrf
                        @method('post')
                        <input type="hidden" name="projection_source_id" value="{{session('clipboard_id')}}">
                        <input type="hidden" name="client_id" value="{{ session('client_id') }}">
                        @if(isset($top_department))
                        <input type="hidden" id="ji_high_projection" name="high_id" value="{{$top_department[0]->department_id}}">
                        @else
                        <input type="hidden" id="ji_high_projection" name="high_id" value="{{$departments[0]->department_id}}">
                        @endif
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
                                    <a class="page-link" href="{{ route('pa0001.count_top',['department_page'=>1,'personnel_page'=>$count_personnel]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <li class="page-item">
    @if($count_personnel == 1)
                                <a class="page-link" href="{{ route('pa0001.count_top',['department_page'=>$count_department,'personnel_page'=>$count_personnel]) }}" aria-label="Previous">
    @else
                                <a class="page-link" href="{{ route('pa0001.count_top',['department_page'=>$count_department,'personnel_page'=>$count_personnel-1]) }}" aria-label="Previous">
    @endif
                                    <span aria-hidden="true">&lt;</span>
                                </a>
                                </li>
                                {{$count_personnel}}/{{$personnel_max}}&nbsp;&nbsp;{{count($personnel_data)}}件
                                <li class="page-item">
    @if($count_personnel<$personnel_max)
                                    <a class="page-link" href="{{ route('pa0001.count_top',['department_page'=>$count_department,'personnel_page'=>$count_personnel+1]) }}" aria-label="Next">
    @else
                                    <a class="page-link" href="{{ route('pa0001.count_top',['department_page'=>$count_department,'personnel_page'=>$personnel_max]) }}" aria-label="Next">
    @endif
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="{{ route('pa0001.count_top',['department_page'=>$count_department,'personnel_page'=>$personnel_max]) }}" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                        {{-- ページネーションここまで --}}

                        {{-- 検索機能　--}}
                    <div class="col-4" style="display:inline-flex;">
                        <p>氏名</p>
                        @if(isset($top_department))
                        <form action="{{ route('psji01.search',[session('client_id'),$top_department[0]->department_id])}}" method="post">
                        @elseif(isset($click_department_data))
                        <form action="{{ route('psji01.search',[session('client_id'),$click_department_data[0]->department_id])}}" method="post">
                        @else
                        <form action="{{ route('psji01.search',[session('client_id'),$click_personnel_data[0]->personnel_id])}}" method="post">
                        @endif
                        @csrf
                        @method('post')
                        @if(!empty($_POST['search2']))
                            <input type="text" name="search2" class="top" maxlength="32" value="{{ $_POST['search2'] }}">
                        @else
                            <input type="text" name="search2" class="top" maxlength="32">
                        @endif
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、検索文字に従い検索し、一覧に表示するレコードを限定します。文字が入力されていない場合は、全件を表示します" type="submit">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.search')}}" alt="検索" disabled style="opacity:0.3">
                        </button>
                        </form>
                        {{-- 検索機能ここまで　--}}
                    
                    </div>
                </div>
                
                <div class="row margin-reset">
                    <div class="col">
                        <div class="border border-dark">
                            <table class="ji-table table table-striped">
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
                            </table>
                        </div>
                        <div class="table border border-dark" style="margin-bottom:0px">
                        <table id ="ji-table" class="table table table-striped border-dark table-hover" style="margin-bottom:0px">
                            <tbody>
                            @foreach($names as $name)
                                <tr>
                                <td width="100">{{ $name->personnel_id}}</td>
                                @if($name->operation_start_date > \Carbon\Carbon::today()->format('Y-m-d') || (!(null == $name->operation_end_date) && \Carbon\Carbon::today()->format('Y-m-d') > $name->operation_end_date))
                                <td width="100"><s><a href="{{ route('plbs01.show',[session('client_id'),$name->personnel_id])}}">{{$name->name}}</a> </s> </td>
                                @else
                                <td width="100"><a href="{{ route('plbs01.show',[session('client_id'),$name->personnel_id])}}">{{$name->name}}</a></td>
                                @endif 
                                <td width="130"><a href="{{ route('plbs01.show',[session('client_id'),$personnel_high[$loop->index]->department_id])}}">{{$personnel_high[$loop->index]->name}}</a>
                                <td width="80">
                                @switch($name->status)
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
                                <td width="100">{{$name->password_update_day}}</td>
                                <td width="80">---------</td>
                                <td width="162">【<a href="{{ route('pa0001.clipboard',$name->personnel_id)}}">複写</a>】
                                【<p id="list_delete{{$loop->index}}" name="bs_delete" style="pointer-events: none; display:inline-block; text-decoration:underline; margin:0px;" onclick="event.preventDefault(); document.getElementById('delete{{$loop->index}}').submit();">削除</p>】
                                <form id="delete{{$loop->index}}" action="{{ route('psji01.destroy',[session('client_id'),$name->personnel_id])}}" method="post" style="display: none;">
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

@endsection