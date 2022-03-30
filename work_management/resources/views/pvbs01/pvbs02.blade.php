@extends('pc0001.pc0002')

@section('content')
    {{-- 部署トップページ　--}}
    <div class="col border border-primary">
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
                        </div>
                    </div>
            </div>
        </div>

        <div id="list-open-button" style="display:none;" onclick="listOn()">
            <p style="text-align:center; cursor: hand; cursor:pointer; background:#99CCFF; border:solid 1px;">↓</p>
        </div>
        <div class="list-area" id="list">
            <div class="department-area m-0">
                <div class="row m-0">
                    {{-- ツリー操作機能　--}}
                    <div class="col-2" style="display:inline-flex; padding-top:15px;">
                        <p>配下部署</p>
                        <form action="{{ route('psbs01.index') }}" method="get">
                            <input type="hidden" id="high" name="high" value="{{$department_details[0]->department_id}}">
                        
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、詳細情報に属する下位情報を新規登録する詳細画面に遷移します">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.new')}}" alt="新規">
                        </button>
                        </form>
                    </div>
                    {{-- ツリー操作機能ここまで　--}}

                    {{-- ページネーション--}}
                    <div class="col-3" style="padding-top:15px;">
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
                                    <a class="page-link" href="{{ route('pa0001.count_top',['department_page'=>$count_department+1,'personnel_page'=>$count_personnel,'id'=>session('client_id')]) }}" aria-label="Next">
    @else
                                    <a class="page-link" href="{{ route('pa0001.count_top',['department_page'=>$department_max,'personnel_page'=>$count_personnel,'id'=>session('client_id')]) }}" aria-label="Next">
    @endif
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="{{ route('pa0001.count_top',['department_page'=>$department_max,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>session('client_id')]) }}" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    {{-- ページネーションここまで--}}
                    {{-- 検索機能　--}}
                    <div class="col-4" style="display:inline-flex; padding-top:15px">
                        <p>部署</p>
                        <form action="{{ route('psbs01.search',[session('client_id'),$department_details[0]->department_id])}}" method="post">
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

                    <div class="col p-0" style="padding-top:15px" onclick="listOn()">
                        <p style="cursor: hand; cursor:pointer;">✕</p>
                    </div> 
                </div>

                <div class="row m-0">
                    <div class="col">
                        <div class="border border-dark" style="max-height: 5em; overflow-y:scroll;">
                            <table id="bs-table" class="tablesorter hasStickyHeaders" style="margin-bottom:0px;margin-top:0px;">
                                <thead>
                                    <tr>
                                    <th>部署番号</th>
                                    <th>部署名</th>
                                    <th>上位部署</th>
                                    <th>状態</th>
                                    <th>責任者</th>
                                    <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($department_details as $department_detail)
                                    <tr>
                                    <td>{{$department_detail->department_id}}</td>
                                @if($department_detail->operation_start_date > \Carbon\Carbon::today()->format('Y-m-d') || (!(null == $department_detail->operation_end_date) && \Carbon\Carbon::today()->format('Y-m-d') > $department_detail->operation_end_date))
                                    <td><s><a href="{{ route('plbs01.show',[session('client_id'),$department_detail->department_id])}}">{{$department_detail->name}}</a></s></td>
                                @else
                                    <td><a href="{{ route('plbs01.show',[session('client_id'),$department_detail->department_id])}}">{{$department_detail->name}}</a></td>
                                @endif
                                    <td>@if(isset($department_detail->high_id))<a href="{{ route('plbs01.show',[session('client_id'),$department_detail->high_id])}}">{{$department_detail->high_name}}</a>@endif</td>
                                    <td>
                                @switch($department_detail->status)
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
                                    <td><a href="{{ route('plbs01.show',[session('client_id'),$department_detail->responsible_person_id])}}">{{$department_detail->management_name}}</a></td>
                                    <td>
                                    【<a href="{{ route('pa0001.clipboard',$department_detail->department_id)}}">複写</a>】
                                    【<p id="bs_list_delete{{$loop->index}}" name="bs_delete" style="pointer-events: none; display:inline-block; text-decoration:underline; margin:0px;" onclick="event.preventDefault(); document.getElementById('bs_delete{{$loop->index}}').submit();">削除</p>】
                                    <form id="bs_delete{{$loop->index}}" action="{{ route('psbs01.delete',[session('client_id'),$department_detail->department_id])}}" method="post" style="display: none;">
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
            <div class="personnel-area" style="padding-top:5px">
                <div class="row m-0">
                    <div class="col-4" style="display:inline-flex;">
                        {{-- ツリー操作機能　--}}
                        <p>所属人員</p>
                        <form action="{{ route('psji01.index') }}" method="get">
                        <input type="hidden" id="ji_high_new" name="high" value="{{$department_details[0]->department_id}}">

                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、詳細情報に属する下位情報を新規登録する詳細画面に遷移します">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.new')}}" alt="新規">
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
                    <div class="col-4 p-0" style="display:inline-flex;">
                        <p>氏名</p>
                        <form action="{{ route('psji01.search',[session('client_id'),$personnel_details[0]->personnel_id])}}" method="post">
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
                
                <div class="row m-0">
                    <div class="col">
                        <div class="border border-dark" style="max-height: 5em; overflow-y:scroll;">
                            <table id="ji-table" class="tablesorter hasStickyHeaders" style=" margin-bottom:0px;margin-top:0px;">
                                <thead>
                                    <tr>
                                    <th>人員番号</th>
                                    <th>氏名</th>
                                    <th>所属部署</th>
                                    <th>状態</th>
                                    <th>ID</th>
                                    <th>PW更新</th>
                                    <th>権限</th>
                                    <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($personnel_details as $personnel_detail)
                                    <tr>
                                    <td>{{ $personnel_detail->personnel_id}}</td>
                                    @if($personnel_detail->operation_start_date > \Carbon\Carbon::today()->format('Y-m-d') || (!(null == $personnel_detail->operation_end_date) && \Carbon\Carbon::today()->format('Y-m-d') > $personnel_detail->operation_end_date))
                                    <td><s><a href="{{ route('plbs01.show',[session('client_id'),$personnel_detail->personnel_id])}}">{{$personnel_detail->name}}</a> </s> </td>
                                    @else
                                    <td><a href="{{ route('plbs01.show',[session('client_id'),$personnel_detail->personnel_id])}}">{{$personnel_detail->name}}</a></td>
                                    @endif 
                                    <td><a href="{{ route('plbs01.show',[session('client_id'),$personnel_detail->high_id])}}">{{$personnel_detail->high_name}}</a>
                                    <td>
                                    @switch($personnel_detail->status)
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
                                    <td>aaa02</td>
                                    <td>{{$personnel_detail->password_update_day}}</td>
                                    <td>---------</td>
                                    <td>【<a href="{{ route('pa0001.clipboard',$personnel_detail->personnel_id)}}">複写</a>】
                                    【<p id="list_delete{{$loop->index}}" name="bs_delete" style="pointer-events: none; display:inline-block; text-decoration:underline; margin:0px;" onclick="event.preventDefault(); document.getElementById('delete{{$loop->index}}').submit();">削除</p>】
                                    <form id="delete{{$loop->index}}" action="{{ route('psji01.destroy',[session('client_id'),$personnel_detail->personnel_id])}}" method="post" style="display: none;">
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