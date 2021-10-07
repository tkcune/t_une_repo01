@extends('pc0001.pc0001')

<?php
    $b = "bs00000003";
?>

@section('content')

    
    {{-- コメント　詳細画面ここから --}}
    {{-- 部署の詳細表示--}}
    <div class="col border border-primary" style="padding:10px;">
    @if( empty(session('click_code')) or session('click_code') == "bs")
    <form action="{{ route('psbs01.update') }}" method="post">
            @csrf
            @method('patch')
            <input type="hidden" id="department_id" name="department_id" value="bs00000001">
            <input type="hidden" name="client_id" value="{{ session('client_id') }}">

            <div class="details-area border border-dark bg-warning" style="padding:10px;" id="parent">
                <div class="row">
                    <div class="col-4">
                        <p id="palent">部署名<input type="text" name="name"></p>
                    </div>
                    <div class="col">
                        <p>番号:bs00000001</p>
                    </div>
                    <div class="col">
                        <p>上位:<a href="{{ route('plbs01.show',[session('client_id'),$b])}}">部署</a></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <p>状態:
                        <select name="status">
                        <option value="10">開設提案</option>
                        <option value="11">審査</option>
                        <option value="12">開設待</option>
                        <option value="13">稼働中</option>
                        <option value="14">休止</option>
                        <option value="18">廃止</option>
                        </select>
                        責任者:
                        <select name="management_personnel_id">
                        <option>社員01</option>
                        </select>
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                    <p>
                    <div style="display:inline-flex">
                    <input type="submit" value="確定">
    </form>
    
                    <form action="{{ route('psbs01.index') }}" method="get">
                    @csrf
                    <input type="submit" value="新規">
                    <input type="hidden" id="high_new" name="high" value="">
                    </form>

                    <form action="{{ route('psbs01.delete',[session('client_id'),$b])}}" method="post">
                    @csrf
                    @method('post')
                    <input type="submit" id="delete" value="削除" disabled>
                    </form>

                    <input type="button" value="複写" id="copyTarget" onclick="clickSave()">

                    <input type="button" value="取消" onclick="clickDelete()">

                    <input type="hidden" id="tree_disabled" value="{{session('client_id')}}">
                    <input type="button" value="隠蔽/表示" onclick="treeDisabled()">

                    <form action="{{ route('index') }}" method="get">
                    <input type="submit" value="再表示">
                    </form>

                    <input type="checkbox" onclick="deleteOn()">
                    </div>
                    登録日:140809 登録者:<a href="#">社員08</a></p>
                    </div>
                </div>
            </div>
    {{-- 人員の詳細表示　ここまで--}}
    @else
    <form action="{{ route('psji01.update',session('client_id')) }}" method="post">
            @csrf
            @method('patch')
            <input type="hidden" id="personnel_id" name="personnel_id" value="">
            <input type="hidden" name="client_id" value="{{ session('client_id') }}">

            <div class="details-area border border-dark bg-warning" style="padding:10px;" id="parent">
                <div class="row">
                    <div class="col-4">
                        <p id="palent">名前<input type="text" name="name"></p>
                    </div>
                    <div class="col">
                        <p>番号:ji00000001</p>
                    </div>
                    <div class="col">
                        <p>上位:<a href="{{ route('plbs01.show',[session('client_id'),$b])}}">田中</a></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-5">
                        <p>管理者番号：<input type="text" name="number" style="width:100px;"></p>
                    </div>
                    <div class="col-3" style="padding:0px">
                        <p>管理者名：山田太郎</p>
                    </div>
                    <div class="col" style="padding:0px">
                        <p>管理者検索：
                        <input type="search" list="keywords" style="width:100px;">
                        <datalist id="keywords">
                        <option value="山田太郎">
                        </datalist>
                    </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-5">
                        <p>メールアドレス<input type="email" name="mail"></p>
                    </div>
                    <div class="col-4" style="padding:0px">
                        <p>パスワード<input id="password" type="password" name="mail"><input type="checkbox" onclick="passwordOn()"></p>
                    </div>
                    <div class="col">
                        <button>メール送信</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <p>状態:
                        <select name="status">
                        <option value="10">応募</option>
                        <option value="11">審査</option>
                        <option value="12">入社待</option>
                        <option value="13">在職</option>
                        <option value="14">休職</option>
                        <option value="18">退職</option>
                        </select>
                        責任者:
                        <select name="management_personnel_id">
                        <option>社員01</option>
                        </select>
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                    <div style="display:inline-flex">
                    <input type="submit" value="確定">
    </form>
    
                    <form action="{{ route('psji01.index') }}" method="get">
                    @csrf
                    <input type="submit" value="新規">
                    <input type="hidden" id="high_new" name="high" value="">
                    </form>

                    <form action="{{ route('psbs01.delete',[session('client_id'),$b])}}" method="post">
                    @csrf
                    @method('post')
                    <input type="submit" id="delete" value="削除" disabled>
                    </form>

                    <input type="button" value="複写" id="copyTarget" onclick="clickSave()">

                    <input type="button" value="取消" onclick="clickDelete()">

                    <input type="hidden" id="tree_disabled" value="{{ session('client_id') }}">
                    <input type="button" value="隠蔽/表示" onclick="treeDisabled()">

                    <form action="{{ route('index') }}" method="get">
                    <input type="submit" value="再表示">
                    </form>

                    <input type="checkbox" onclick="deleteOn()">
                    </div>
                    <p>登録日:140809 修正日:140809 運用開始日:140809 運用終了日:140809</p>
                    </div>
                </div>
            </div>
    {{-- 人員の詳細表示　ここまで--}}
    @endif
            <div class="department-area">
                <div class="row">
                    <div class="col" style="padding-top:15px">
                        <div style="display:inline-flex">
                        {{-- ツリー操作機能　--}}
                        <form action="{{ route('psbs01.index') }}" method="get">
                        <input type="hidden" id="high" name="high" value="">
                        <p>配下部署<button>新規</button>
                        </form>

                        <form action="{{ route('psbs01.hierarchyUpdate',[session('client_id')]) }}" method="post">
                        <input type="hidden" id="high_move" name="high_id" value="">
                        <input type="hidden" id="lower_move" name="lower_id" value=""> 
                        @csrf
                        @method('patch')
                        <button>移動</button>
                        </form>

                        <form action="{{ route('psbs01.copy') }}" method="post">
                        @csrf
                        @method('post')
                        <input type="hidden" name="client_id" value="{{ session('client_id') }}">
                        <input type="hidden" id="copy" name="copy_id" value="">
                        <input type="hidden" id="high_insert" name="high_id" value="">
                        <button>挿入</button>
                        </form>

                        <form action="{{ route('ptcm01.store') }}" method="post">
                        @csrf
                        @method('post')
                        <input type="hidden" id="projection_source" name="projection_source_id" value="">
                        <input type="hidden" id="high_projection" name="high_id" value="">
                        <input type="hidden" name="client_id" value="{{ session('client_id') }}">
                        <button>投影</button>
                        </form>
                        {{-- ツリー操作機能ここまで　--}}

                        {{-- ページネーション--}}
                        <nav aria-label="Page navigation example">
                            <ul class="pagination pagination-sm">
                                <li class="page-item">
                                    <a class="page-link" href="{{ route('count',['department_page'=>1,'personnel_page'=>$count_personnel]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <li class="page-item">
@if($count_department == 1)
                                <a class="page-link" href="{{ route('count',['department_page'=>$count_department,'personnel_page'=>$count_personnel]) }}" aria-label="Previous">
@else
                                <a class="page-link" href="{{ route('count',['department_page'=>$count_department-1,'personnel_page'=>$count_personnel]) }}" aria-label="Previous">
@endif
                                    <span aria-hidden="true">&lt;</span>
                                </a>
                                </li>
                                {{$count_department}}/{{$department_max}}
                                <li class="page-item">
@if($count_department<$department_max)
                                    <a class="page-link" href="{{ route('count',['department_page'=>$count_department+1,'personnel_page'=>$count_personnel]) }}" aria-label="Next">
@else
                                    <a class="page-link" href="{{ route('count',['department_page'=>$department_max,'personnel_page'=>$count_personnel]) }}" aria-label="Next">
@endif
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="{{ route('count',['department_page'=>$department_max,'personnel_page'=>$count_personnel]) }}" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                        {{-- ページネーションここまで--}}

                        

                        {{-- 検索機能　--}}
                        <form action="{{ route('psbs01.search',[session('client_id')])}}" method="post">
                        @csrf
                        @method('post')
                        <button type="submit">検索</button>
                        @if(!empty($_POST['search']))
                        部署<input type="text" name="search" value="{{ $_POST['search'] }}">
                        @else
                        部署<input type="text" name="search">
                        @endif
                        </form>
                        </div>
                    </div>
                </div>

                {{--　8/19　今後　配下の部署のみ表示させるように修正 --}}
                <div class="row">
                    <div class="col">
                    <table class="table table-bordered border-dark">
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
                            @foreach($departments as $department)
                            <tr>
                            <td>{{$department->department_id}}</td>
                            <td><a href="{{ route('plbs01.show',[session('client_id'),$department->department_id])}}">{{$department->name}}</a></td>
                            <td><a href="{{ route('plbs01.show',[session('client_id'),$department_high[$loop->index]->department_id])}}">{{$department_high[$loop->index]->name}}</a></td>
                            <td>
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
                            <td><a href="#">{{ $responsible_lists[$loop->index] }}</a></td>
                            <td>【<a href="#">コピー</a>】</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                   
                    </div>
                </div>
                {{--　8/19　今後　配下の部署のみ表示させるように修正 ここまで--}}
            </div>
                <div class="row">
                    <div class="col">
                        <div style="display:inline-flex">
                        {{-- ツリー操作機能　--}}
                        <form action="{{ route('psji01.index') }}" method="get">
                        <input type="hidden" id="ji_high_new" name="high" value="">
                        <p>所属人員 <button>新規</button>
                        </form>

                        <form action="{{ route('psbs01.hierarchyUpdate',[session('client_id')]) }}" method="post">
                        <input type="hidden" id="ji_high_move" name="high_id" value="">
                        <input type="hidden" id="ji_lower_move" name="lower_id" value="ji00000001"> 
                        @csrf
                        @method('patch')
                        <button>移動</button>
                        </form>

                        <form action="{{ route('psji01.copy') }}" method="post">
                        @csrf
                        @method('post')
                        <input type="hidden" name="client_id" value="{{ session('client_id') }}">
                        <input type="hidden" name="high_id" value="{{ $b }}">
                        <input type="hidden" id="ji_copy_id" name="copy_id" value="">
                        <button>挿入</button>
                        </form>

                        <form action="{{ route('ptcm01.store') }}" method="post">
                        @csrf
                        @method('post')
                        <input type="hidden" name="projection_source_id" value="ji00000001">
                        <input type="hidden" name="client_id" value="{{ session('client_id') }}">
                        <input type="hidden" id="ji_high_projection" name="high_id" value="">
                        <button>投影</button>
                        </form>
                        {{-- ツリー操作機能ここまで　--}}

                        {{-- ページネーション--}}
@if(isset($select_id))
                        <nav aria-label="Page navigation example">
                            <ul class="pagination pagination-sm">
                                <li class="page-item">
                                    <a class="page-link" href="{{ route('count2',['department_page'=>$count_department,'personnel_page'=>1,'id'=>$client,'id2'=>$select_id]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <li class="page-item">
    @if($count_personnel == 1)
                                <a class="page-link" href="{{ route('count2',['department_page'=>$count_department,'personnel_page'=>$count_personnel,'id'=>$client,'id2'=>$select_id]) }}" aria-label="Previous">
    @else
                                <a class="page-link" href="{{ route('count2',['department_page'=>$count_department,'personnel_page'=>$count_personnel-1,'id'=>$client,'id2'=>$select_id]) }}" aria-label="Previous">
    @endif
                                    <span aria-hidden="true">&lt;</span>
                                </a>
                                </li>
                                {{$count_personnel}}/{{$personnel_max}}
                                <li class="page-item">
    @if($count_personnel<$personnel_max)
                                    <a class="page-link" href="{{ route('count2',['department_page'=>$count_department,'personnel_page'=>$count_personnel+1,'id'=>$client,'id2'=>$select_id]) }}" aria-label="Next">
    @else
                                    <a class="page-link" href="{{ route('count2',['department_page'=>$count_department,'personnel_page'=>$personnel_max,'id'=>$client,'id2'=>$select_id]) }}" aria-label="Next">
    @endif
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="{{ route('count2',['department_page'=>$count_department,'personnel_page'=>$personnel_max,'id'=>$client,'id2'=>$select_id]) }}" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
@else
                        <nav aria-label="Page navigation example">
                            <ul class="pagination pagination-sm">
                                <li class="page-item">
                                    <a class="page-link" href="{{ route('count',['department_page'=>$count_department,'personnel_page'=>1]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <li class="page-item">
    @if($count_personnel == 1)
                                <a class="page-link" href="{{ route('count',['department_page'=>$count_department,'personnel_page'=>$count_personnel]) }}" aria-label="Previous">
    @else
                                <a class="page-link" href="{{ route('count',['department_page'=>$count_department,'personnel_page'=>$count_personnel-1]) }}" aria-label="Previous">
    @endif
                                    <span aria-hidden="true">&lt;</span>
                                </a>
                                </li>
                                {{$count_personnel}}/{{$personnel_max}}
                                <li class="page-item">
    @if($count_personnel<$personnel_max)
                                    <a class="page-link" href="{{ route('count',['department_page'=>$count_department,'personnel_page'=>$count_personnel+1]) }}" aria-label="Next">
    @else
                                    <a class="page-link" href="{{ route('count',['department_page'=>$count_department,'personnel_page'=>$personnel_max]) }}" aria-label="Next">
    @endif
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="{{ route('count',['department_page'=>$count_department,'personnel_page'=>$personnel_max]) }}" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
@endif
                        {{-- ページネーションここまで --}}

                        {{-- 検索機能　--}}
                        <form action="{{ route('psji01.search',[session('client_id')])}}" method="post">
                        @csrf
                        @method('post')
                        <button type="submit" >検索</button>
                        @if(!empty($_POST['search']))
                        氏名<input type="text" name="search" value="{{ $_POST['search'] }}">
                        @else
                        氏名<input type="text" name="search">
                        @endif
                        </form>
                        {{-- 検索機能ここまで　--}}
                        </div>
                    </div>
                </div>
                
                {{--　8/19　今後　部署の人員のみ表示させるように修正 --}}
                <div class="row">
                    <div class="col">
                    <table class="table table-bordered border-dark">
                        <thead>
                            <tr>
                            <th>人員番号</th>
                            <th>氏名</th>
                            <th>所属部署</th>
                            <th>状態</th>
                            <th>ログインID</th>
                            <th>PW更新</th>
                            <th>権限</th>
                            <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($names as $name)
                            <tr>
                            <td>{{ $name->personnel_id}}</td>
                            <td><a href="#">{{$name->name}}</a></td>
                            <td><a href="{{ route('plbs01.show',[session('client_id'),$personnel_high[$loop->index]->department_id])}}">{{$personnel_high[$loop->index]->name}}</a></td>
                            <td>
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
                            <td>aaa02</td>
                            <td>{{$name->password_update_day}}</td>
                            <td>---------</td>
                            <td>【<a href="#">コピー</a>】</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                 
                    </div>
                </div>
            </div>
            {{--　8/19　今後　部署の人員のみ表示させるように修正 ここまで--}}
    </div>
    {{-- コメント　詳細画面ここまで --}}

@endsection
