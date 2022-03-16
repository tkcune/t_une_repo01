{{-- ツリー操作機能　--}}
<div class="col-4" style="display:inline-flex; padding-top:15px;">
    <p>@if(session('click_code') == "bs")配下@else所属@endif部署</p>
        <form action="{{ route('psbs01.index') }}" method="get">
            @if(isset($top_department))
                <input type="hidden" id="high" name="high" value="{{$top_department[0]->department_id}}">
            @else
                <input type="hidden" id="high" name="high" value="{{$click_department_data[0]->department_id}}">
            @endif
                        
            <button class="main_button_style" data-toggle="tooltip" title="クリックにより、詳細情報に属する下位情報を新規登録する詳細画面に遷移します">
                <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.new')}}" alt="新規">
            </button>
        </form>

        <form action="{{ route('psbs01.hierarchyUpdate',[session('client_id')]) }}" method="post">
            @if(isset($top_department))
                <input type="hidden" id="high_move" name="high_id" value="{{$top_department[0]->department_id}}">
            @else
                <input type="hidden" id="high_move" name="high_id" value="{{$click_department_data[0]->department_id}}">
            @endif
                <input type="hidden" id="lower_move" name="lower_id" value="{{session('clipboard_id')}}">
                @csrf
                @method('patch')
                <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧に移動します 移動元からは抹消されます">
                    <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.move')}}" alt="移動">
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
                <input type="hidden" id="high_insert" name="high_id" value="{{$click_department_data[0]->department_id}}">
            @endif
                <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧に挿入します 移動元は消えません">
                    <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.insert')}}" alt="挿入">
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
            <input type="hidden" id="high_projection" name="high_id" value="{{$click_department_data[0]->department_id}}">
            @endif
            <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧にショートカットして投影します 移動元は消えません">
                <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.ji')}}" alt="投影">
            </button>
        </form>
</div>
{{-- ツリー操作機能ここまで　--}}
{{-- ページネーション--}}
<div class="col-3" style="padding-top:15px;">
@if(!empty($_POST['search']))
    <nav aria-label="Page navigation example">
        <ul class="pagination pagination-sm">
            <li class="page-item">
                <a class="page-link" href="{{ route('pa0001.count_search_department',['department_page'=>1,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$select_id,'search'=>$_POST['search']])}}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <li class="page-item">
    @if($count_department == 1)
                <a class="page-link" href="{{ route('pa0001.count_search_department',['department_page'=>$count_department,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$select_id,'search'=>$_POST['search']])}}" aria-label="Previous">
    @else
                <a class="page-link" href="{{ route('pa0001.count_search_department',['department_page'=>$count_department-1,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$select_id,'search'=>$_POST['search']])}}" aria-label="Previous">
    @endif
                    <span aria-hidden="true">&lt;</span>
                </a>
            </li>
            {{$count_department}}/{{$department_max}}&nbsp;&nbsp;{{count($department_data)}}件
            <li class="page-item">
    @if($count_department<$department_max)
                <a class="page-link" href="{{ route('pa0001.count_search_department',['department_page'=>$count_department+1,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$select_id,'search'=>$_POST['search']]) }}" aria-label="Next">
    @else
                <a class="page-link" href="{{ route('pa0001.count_search_department',['department_page'=>$department_max,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$select_id,'search'=>$_POST['search']]) }}" aria-label="Next">
    @endif
                    <span aria-hidden="true">&gt;</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="{{ route('pa0001.count_search_department',['department_page'=>$department_max,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$select_id,'search'=>$_POST['search']]) }}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
@elseif(!empty($_POST['search2']))
    <nav aria-label="Page navigation example">
        <ul class="pagination pagination-sm">
            <li class="page-item">
                <a class="page-link" href="{{ route('pa0001.count_search_personnel',['department_page'=>1,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$select_id,'search'=>$_POST['search2']])}}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <li class="page-item">
    @if($count_department == 1)
                <a class="page-link" href="{{ route('pa0001.count_search_personnel',['department_page'=>$count_department,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$select_id,'search'=>$_POST['search2']])}}" aria-label="Previous">
    @else
                <a class="page-link" href="{{ route('pa0001.count_search_personnel',['department_page'=>$count_department-1,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$select_id,'search'=>$_POST['search2']])}}" aria-label="Previous">
    @endif
                    <span aria-hidden="true">&lt;</span>
                </a>
            </li>
        {{$count_department}}/{{$department_max}}&nbsp;&nbsp;{{count($department_data)}}件
            <li class="page-item">
    @if($count_department<$department_max)
                <a class="page-link" href="{{ route('pa0001.count_search_personnel',['department_page'=>$count_department+1,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$select_id,'search'=>$_POST['search2']]) }}" aria-label="Next">
    @else
                <a class="page-link" href="{{ route('pa0001.count_search_personnel',['department_page'=>$department_max,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$select_id,'search'=>$_POST['search2']]) }}" aria-label="Next">
    @endif
                    <span aria-hidden="true">&gt;</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="{{ route('pa0001.count_search_personnel',['department_page'=>$department_max,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$select_id,'search'=>$_POST['search2']]) }}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
@elseif(isset($select_id))
    <nav aria-label="Page navigation example">
        <ul class="pagination pagination-sm">
            <li class="page-item">
                <a class="page-link" href="{{ route('pa0001.count_narrowdown',['department_page'=>1,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$select_id]) }}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <li class="page-item">
    @if($count_department == 1)
                <a class="page-link" href="{{ route('pa0001.count_narrowdown',['department_page'=>$count_department,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$select_id]) }}" aria-label="Previous">
    @else
                <a class="page-link" href="{{ route('pa0001.count_narrowdown',['department_page'=>$count_department-1,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$select_id]) }}" aria-label="Previous">
    @endif
                    <span aria-hidden="true">&lt;</span>
                </a>
            </li>
                                
            {{$count_department}}/{{$department_max}}&nbsp;&nbsp;{{count($department_data)}}件
            <li class="page-item">
    @if($count_department<$department_max)
                <a class="page-link" href="{{ route('pa0001.count_narrowdown',['department_page'=>$count_department+1,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$select_id]) }}" aria-label="Next">
    @else
                <a class="page-link" href="{{ route('pa0001.count_narrowdown',['department_page'=>$department_max,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$select_id]) }}" aria-label="Next">
    @endif
                    <span aria-hidden="true">&gt;</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="{{ route('pa0001.count_narrowdown',['department_page'=>$department_max,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$select_id]) }}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
@else
    <nav aria-label="Page navigation example">
        <ul class="pagination pagination-sm">
            <li class="page-item">
                <a class="page-link" href="{{ route('pa0001.count',['department_page'=>1,'personnel_page'=>$count_personnel]) }}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <li class="page-item">
    @if($count_department == 1)
                <a class="page-link" href="{{ route('pa0001.count',['department_page'=>$count_department,'personnel_page'=>$count_personnel]) }}" aria-label="Previous">
    @else
                <a class="page-link" href="{{ route('pa0001.count',['department_page'=>$count_department-1,'personnel_page'=>$count_personnel]) }}" aria-label="Previous">
    @endif
                    <span aria-hidden="true">&lt;</span>
                </a>
            </li>
            {{$count_department}}/{{$department_max}}&nbsp;&nbsp;{{count($department_data)}}件
            <li class="page-item">
    @if($count_department<$department_max)
                <a class="page-link" href="{{ route('pa0001.count',['department_page'=>$count_department+1,'personnel_page'=>$count_personnel]) }}" aria-label="Next">
    @else
                <a class="page-link" href="{{ route('pa0001.count',['department_page'=>$department_max,'personnel_page'=>$count_personnel]) }}" aria-label="Next">
    @endif
                    <span aria-hidden="true">&gt;</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="{{ route('pa0001.count',['department_page'=>$department_max,'personnel_page'=>$count_personnel]) }}" aria-label="Next">
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
        @if(session('click_code') == "bs")
            @if(isset($top_department))
                <form action="{{ route('psbs01.search',[session('client_id'),$top_department[0]->department_id])}}" method="post">
            @else
                <form action="{{ route('psbs01.search',[session('client_id'),$click_department_data[0]->department_id])}}" method="post">
            @endif
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
                <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.search')}}" alt="検索">
            </button>
        </form>
</div> 

<div class="col" style="padding-top:15px" onclick="listOn()">
    <p style="cursor: hand; cursor:pointer;">✕</p>
</div>