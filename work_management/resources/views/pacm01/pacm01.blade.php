@extends('pc0001.pc0001')

<?php
    $a = "aa00000001";
    $b = "bs00000003";
    $high = "bs00000004";
?>

@section('content')

    {{-- コメント　詳細画面ここから --}}
    <div class="col border border-primary" style="padding:10px;">
    <form action="{{ route('psbs01.update') }}" method="post">
            @csrf
            @method('patch')
            <input type="hidden" name="department_id" value="bs00000004">
            <input type="hidden" name="client_id" value="aa00000001">

            <div class="details-area border border-dark bg-warning" style="padding:10px;">
                <div class="row">
                    <div class="col-4">
                        <p id="palent">部署名<input type="text" name="name"></p>
                    </div>
                    <div class="col">
                        <p>番号:210000002</p>
                    </div>
                    <div class="col">
                        <p>上位:<a href="#">部署8</a></p>
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
                    <form action="{{ route('psbs01.delete',[$a,$b])}}" method="post">
                    @csrf
                    @method('post')
                    <input type="submit" value="新規">
                    </form>
                    <form action="{{ route('psbs01.delete',[$a,$b])}}" method="post">
                    @csrf
                    @method('post')
                    <input type="submit" id="delete" value="削除" disabled>
                    </form>
                    <form action="{{ route('psbs01.delete',[$a,$b])}}" method="post">
                    @csrf
                    @method('post')
                    <input type="submit" value="複写">
                    </form>
                    <form action="{{ route('psbs01.delete',[$a,$b])}}" method="post">
                    @csrf
                    @method('post')
                    <input type="submit" value="取消">
                    </form>
                    <form action="{{ route('psbs01.delete',[$a,$b])}}" method="post">
                    @csrf
                    @method('post')
                    <input type="submit" value="隠蔽/表示">
                    </form>
                    <form action="{{ route('psbs01.delete',[$a,$b])}}" method="post">
                    @csrf
                    @method('post')
                    <input type="submit" value="再表示">
                    </form>
                    <input type="button" value="削除有効化" onclick="deleteOn()">
                    </div>
                    登録日:140809 登録者:<a href="#">社員08</a></p>
                    </div>
                </div>
            </div>
            <div class="department-area">
                <div class="row">
                    <div class="col" style="padding-top:15px">
                        <div style="display:inline-flex">
                        {{-- ツリー操作機能　--}}
                        <form action="{{ route('psbs01.index') }}" method="get">
                        <input type="hidden" name="high" value="{{ $high }}">
                        <p>配下部署<button>新規</button>
                        </form>

                        <form action="{{ route('psbs01.hierarchyUpdate',[$a]) }}" method="post">
                        <input type="hidden" name="high_id" value="{{ $b }}">
                        <input type="hidden" name="lower_id" value="bs00000006"> 
                        @csrf
                        @method('patch')
                        <button>移動</button>
                        </form>

                        <form action="#" method="get">
                        <button>挿入</button>
                        </form>

                        <form action="{{ route('ptcm01.store') }}" method="post">
                        @csrf
                        @method('post')
                        <input type="hidden" name="projection_source_id" value="{{ $high }}">
                        <input type="hidden" name="client_id" value="aa00000001">
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
                                1/1(14)
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
                        <form action="#" method="get">
                        <button type="button" >検索</button>
                        部署
                        <input type="text">
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
                            <td><a href="#">{{$department->name}}</a></td>
                            <td><a href="#">部署00</a></td>
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
                            <td><a href="#">社員01</a></td>
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
                        <input type="hidden" name="high" value="{{ $high }}">
                        <p>所属人員 <button>新規</button>
                        </form>

                        <form action="{{ route('psbs01.hierarchyUpdate',[$a]) }}" method="post">
                        <input type="hidden" name="high_id" value="{{ $b }}">
                        <input type="hidden" name="lower_id" value="bs00000006"> 
                        @csrf
                        @method('patch')
                        <button>移動</button>
                        </form>

                        <form action="#" method="get">
                        <button>挿入</button>
                        </form>

                        <form action="{{ route('ptcm01.store') }}" method="post">
                        @csrf
                        @method('post')
                        <input type="hidden" name="projection_source_id" value="{{ $high }}">
                        <input type="hidden" name="client_id" value="aa00000001">
                        <button>投影</button>
                        </form>
                        {{-- ツリー操作機能ここまで　--}}

                        {{-- ページネーション--}}
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
                                1/1(14)
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
                        {{-- ページネーションここまで --}}

                        {{-- 検索機能　--}}
                        <form action="#" method="get">
                        <button type="button" >検索</button>
                        氏名<input type="text">
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
                            <td><a href="#">部署09</a></td>
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
