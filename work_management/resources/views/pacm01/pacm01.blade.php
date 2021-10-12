@extends('pc0001.pc0001')

<?php
    $b = "bs00000003";
?>

@section('content')

    
    {{-- コメント　詳細画面ここから --}}
    {{-- 部署の詳細表示--}}
    <div class="col border border-primary" style="padding:10px;">
    @if( empty(session('click_code')) or session('click_code') == "bs")
    @if(isset($top_department))
    <form action="{{ route('psbs01.update') }}" method="post">
            @csrf
            @method('patch')
            <input type="hidden" id="department_id" name="department_id" value="{{$top_department[0]->department_id}}">
            <input type="hidden" name="client_id" value="{{ session('client_id') }}">

            <div class="details-area border border-dark bg-warning" style="padding:10px;" id="parent">
                <div class="row">
                    <div class="col-4">
                        <p id="palent">部署名<input type="text" name="name" value="{{$top_department[0]->name}}"></p>
                    </div>
                    <div class="col">
                        <p>番号:{{$top_department[0]->department_id}}</p>
                    </div>
                    <div class="col">
        
                    </div>
                </div>

                <div class="row">
                    <div class="col-4">
                        <p>管理者番号：<input type="text" name="management_number" value="{{$top_department[0]->management_personnel_id}}" style="width:100px;"></p>
                    </div>
                    <div class="col-3" style="padding:0px">
                        <p>管理者名：{{$top_management[0]}}</p>
                    </div>
                    <div class="col" style="padding:0px">
                    <p>管理者検索：
                        <input type="search" list="keywords" style="width:150px;">
                        <datalist id="keywords">
                        <option value="山田一郎">
                        </datalist>
                    </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <p>状態:
                        <select name="status">
                        <option value="10" @if($top_department[0]->status == "10") selected @endif>開設提案</option>
                        <option value="11" @if($top_department[0]->status == "11") selected @endif>審査</option>
                        <option value="12" @if($top_department[0]->status == "12") selected @endif>開設待</option>
                        <option value="13" @if($top_department[0]->status == "13") selected @endif>稼働中</option>
                        <option value="14" @if($top_department[0]->status == "14") selected @endif>休止</option>
                        <option value="18" @if($top_department[0]->status == "18") selected @endif>廃止</option>
                        </select>
                        責任者:
                        <select name="management_personnel_id">
                        <option>{{$top_responsible[0]}}</option>
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
                    <input type="hidden" id="high_new" name="high" value="{{$top_department[0]->department_id}}">
                    </form>

                    <form action="{{ route('psbs01.delete',[session('client_id'),$top_department[0]->department_id])}}" method="post">
                    @csrf
                    @method('post')
                    <input type="submit" id="delete" value="削除" disabled>
                    </form>

                    <form action="{{ route('clipboard',$top_department[0]->department_id)}}" method="get">
                    @csrf
                    <input type="submit" value="複写" id="copyTarget">
                    </form>

                    <form action="{{ route('deleteclipboard')}}" method="get">
                    @csrf
                    <input type="submit" value="取消">
                    </form>

                    <input type="hidden" id="tree_disabled" value="{{session('client_id')}}">
                    <input type="button" value="隠蔽/表示" onclick="treeDisabled()">

                    <form action="{{ route('index') }}" method="get">
                    <input type="submit" value="再表示">
                    </form>

                    <input type="checkbox" onclick="deleteOn()">
                    </div>
                    登録日:{{$top_department[0]->created_at}} 登録者:<a href="#">{{$top_responsible[0]}}</a></p>
                    </div>
                </div>
            </div>
    @else
    <form action="{{ route('psbs01.update') }}" method="post">
            @csrf
            @method('patch')
            <input type="hidden" id="department_id" name="department_id" value="{{$departments[0]->department_id}}">
            <input type="hidden" name="client_id" value="{{ session('client_id') }}">

            <div class="details-area border border-dark bg-warning" style="padding:10px;" id="parent">
                <div class="row">
                    <div class="col-4">
                        <p id="palent">部署名<input type="text" name="name" value="{{$departments[0]->name}}"></p>
                    </div>
                    <div class="col">
                        <p>番号:{{$departments[0]->department_id}}</p>
                    </div>
                    <div class="col">
                        <p>上位:<a href="{{ route('plbs01.show',[session('client_id'),$departments[0]->high_id])}}">{{$department_high[0]->name}}</a></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-4">
                        <p>管理者番号：<input type="text" name="management_number" value="{{$departments[0]->management_personnel_id}}" style="width:100px;"></p>
                    </div>
                    <div class="col-3" style="padding:0px">
                        <p>管理者名：{{$management_lists[0]}}</p>
                    </div>
                    <div class="col" style="padding:0px">
                    <p>管理者検索：
                        <input type="search" list="keywords" style="width:150px;">
                        <datalist id="keywords">
                        <option value="山田一郎">
                        </datalist>
                    </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <p>状態:
                        <select name="status">
                        <option value="10" @if($departments[0]->status == "10") selected @endif>開設提案</option>
                        <option value="11" @if($departments[0]->status == "11") selected @endif>審査</option>
                        <option value="12" @if($departments[0]->status == "12") selected @endif>開設待</option>
                        <option value="13" @if($departments[0]->status == "13") selected @endif>稼働中</option>
                        <option value="14" @if($departments[0]->status == "14") selected @endif>休止</option>
                        <option value="18" @if($departments[0]->status == "18") selected @endif>廃止</option>
                        </select>
                        責任者:
                        <select name="management_personnel_id">
                        <option>{{$responsible_lists[0]}}</option>
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
                    <input type="hidden" id="high_new" name="high" value="{{$departments[0]->department_id}}">
                    </form>

                    <form action="{{ route('psbs01.delete',[session('client_id'),$departments[0]->department_id])}}" method="post">
                    @csrf
                    @method('post')
                    <input type="submit" id="delete" value="削除" disabled>
                    </form>


                    <form action="{{ route('clipboard',$departments[0]->department_id)}}" method="get">
                    @csrf
                    <input type="submit" value="複写" id="copyTarget">
                    </form>

                    <form action="{{ route('deleteclipboard')}}" method="get">
                    @csrf
                    <input type="submit" value="取消">
                    </form>

                    <input type="hidden" id="tree_disabled" value="{{session('client_id')}}">
                    <input type="button" value="隠蔽/表示">

                    <form action="{{ route('index') }}" method="get">
                    <input type="submit" value="再表示">
                    </form>

                    <input type="checkbox" onclick="deleteOn()">
                    </div>
                    登録日:{{$departments[0]->created_at}} 登録者:<a href="#">{{$responsible_lists[0]}}</a></p>
                    </div>
                </div>
            </div>
    @endif
    {{-- 部署の詳細表示　ここまで--}}
    {{-- 人員の詳細表示　--}}
    @else
    <form action="{{ route('psji01.update',session('client_id')) }}" method="post">
            @csrf
            @method('patch')
            <input type="hidden" id="personnel_id" name="personnel_id" value="{{$names[0]->personnel_id}}">
            <input type="hidden" name="client_id" value="{{ session('client_id') }}">

            <div class="details-area border border-dark bg-warning" style="padding:10px;" id="parent">
                <div class="row">
                    <div class="col-4">
                        <p id="palent">名前<input type="text" name="name" value="{{$names[0]->name}}"></p>
                    </div>
                    <div class="col">
                        <p>番号:{{$names[0]->personnel_id}}</p>
                    </div>
                    <div class="col">
                        <p>上位:山田一郎</p>
                    </div>

                    <div class="col" style="padding:0px">
                    <p>ログイン：
                        <input type="checkbox" name="login" value="1" onclick="loginDisabled()" @if($names[0]->login_authority == "1") checked @endif>
                    </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-4">
                        <p>管理者番号：<input type="text" name="management_number" value="{{$names[0]->management_personnel_id}}" style="width:100px;"></p>
                    </div>
                    <div class="col-3" style="padding:0px">
                        <p>管理者名：{{$personnel_management_lists[0]}}</p>
                    </div>
                    <div class="col" style="padding:0px">
                    <p>管理者検索：
                        <input type="search" list="keywords" style="width:150px;">
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
                        <p id="login" @if($names[0]->login_authority == "0") visibility hidden @endif >パスワード<input id="password" type="password" name="mail"><input type="checkbox" onclick="passwordOn()"></p>
                    </div>
                    <div class="col">
                        <button>メール送信</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <p>状態:
                        <select name="status">
                        <option value="10" @if($names[0]->status == "10") selected @endif>応募</option>
                        <option value="11" @if($names[0]->status == "11") selected @endif>審査</option>
                        <option value="12" @if($names[0]->status == "12") selected @endif>入社待</option>
                        <option value="13" @if($names[0]->status == "13") selected @endif>在職</option>
                        <option value="14" @if($names[0]->status == "14") selected @endif>休職</option>
                        <option value="18" @if($names[0]->status == "18") selected @endif>退職</option>
                        </select>
                        システム管理者:
                        <input type="checkbox" value="1" @if($names[0]->system_management == "1") checked @endif>
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

                    <form action="{{ route('psji01.destroy',[session('client_id'),$b])}}" method="post">
                    @csrf
                    @method('post')
                    <input type="submit" id="delete" value="削除" disabled>
                    </form>

                    <input type="button" value="複写" id="copyTarget" onclick="clickSave()">

                    <input type="button" value="取消" onclick="clickDelete()">

                    <input type="hidden" id="tree_disabled" value="{{ session('client_id') }}">
                    <input type="button" value="隠蔽/表示">

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
                        @if(isset($top_department))
                            <input type="hidden" id="high" name="high" value="{{$top_department[0]->department_id}}">
                        @else
                            <input type="hidden" id="high" name="high" value="{{$departments[0]->department_id}}">
                        @endif
                        <p>配下部署<button>新規</button>
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
                        <button>移動</button>
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
                        <button>挿入</button>
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
            </div>
                <div class="row">
                    <div class="col">
                        <div style="display:inline-flex">
                        {{-- ツリー操作機能　--}}
                        <form action="{{ route('psji01.index') }}" method="get">
                        @if(isset($top_department))
                        <input type="hidden" id="ji_high_new" name="high" value="{{$top_department[0]->department_id}}">
                        @else
                        <input type="hidden" id="ji_high_new" name="high" value="{{$departments[0]->department_id}}">
                        @endif
                        <p>所属人員 <button>新規</button>
                        </form>

                        <form action="{{ route('psbs01.hierarchyUpdate',[session('client_id')]) }}" method="post">
                        @csrf
                        @method('patch')
                        @if(isset($top_department))
                        <input type="hidden" id="ji_high_move" name="high_id" value="{{$top_department[0]->department_id}}">
                        @else
                        <input type="hidden" id="ji_high_move" name="high_id" value="{{$departments[0]->department_id}}">
                        @endif
                        <input type="hidden" id="ji_lower_move" name="lower_id" value="{{session('clipboard_ji_id')}}"> 
                        <button>移動</button>
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
                        <input type="hidden" id="ji_copy_id" name="copy_id" value="{{session('clipboard_ji_id')}}">
                        <button>挿入</button>
                        </form>

                        <form action="{{ route('ptcm01.store') }}" method="post">
                        @csrf
                        @method('post')
                        <input type="hidden" name="projection_source_id" value="{{session('clipboard_ji_id')}}">
                        <input type="hidden" name="client_id" value="{{ session('client_id') }}">
                        @if(isset($top_department))
                        <input type="hidden" id="ji_high_projection" name="high_id" value="{{$top_department[0]->department_id}}">
                        @else
                        <input type="hidden" id="ji_high_projection" name="high_id" value="{{$departments[0]->department_id}}">
                        @endif
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
                            <td><a href="{{ route('plbs01.show',[session('client_id'),$name->personnel_id])}}">{{$name->name}}</a></td>
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
    </div>
    {{-- コメント　詳細画面ここまで --}}

@endsection
