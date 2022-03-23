{{-- ツリー操作機能　--}}
<div class="row" style="display:inline-flex">
    <div>所属人員</div>
    <div class="col" style="display:inline-flex;">
        <form action="{{ route('psji01.index') }}" method="get">
            <input type="hidden" id="ji_high_new" name="high" value="{{$click_data[0]->department_id}}">
        
            <button class="main_button_style" data-toggle="tooltip" title="クリックにより、詳細情報に属する下位情報を新規登録する詳細画面に遷移します">
                <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.new')}}" alt="新規">
            </button>
        </form>

        <form action="{{ route('psbs01.hierarchyUpdate',[session('client_id')]) }}" method="post">
            @csrf
            @method('patch')
            <input type="hidden" id="ji_high_move" name="high_id" value="{{$click_data[0]->department_id}}">
        
            <input type="hidden" id="ji_lower_move" name="lower_id" value="{{session('clipboard_id')}}"> 
            <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧に移動します 移動元からは抹消されます">
                <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.move')}}" alt="移動">
            </button>
        </form>

        <form action="{{ route('psji01.copy') }}" method="post">
            @csrf
            @method('post')
            <input type="hidden" name="client_id" value="{{ session('client_id') }}">
            <input type="hidden" name="high_id" value="{{$click_data[0]->department_id}}">

            <input type="hidden" id="ji_copy_id" name="copy_id" value="{{session('clipboard_id')}}">
            <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧に挿入します 移動元は消えません">
                <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.insert')}}" alt="挿入">
            </button>
        </form>

        <form action="{{ route('ptcm01.store') }}" method="post">
            @csrf
            @method('post')
            <input type="hidden" name="projection_source_id" value="{{session('clipboard_id')}}">
            <input type="hidden" name="client_id" value="{{ session('client_id') }}">
            <input type="hidden" id="ji_high_projection" name="high_id" value="{{$click_data[0]->department_id}}">
        
            <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧にショートカットして投影します 移動元は消えません">
                <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.ji')}}" alt="投影">
            </button>
        </form>
    </div>
</div>
{{-- ツリー操作機能ここまで　--}}

{{-- 検索機能　--}}
<div>
<div class="row" style="display:inline-flex">
    <div>氏名</div>
    <form action="{{ route('psji01.search',[session('client_id'),$select_id])}}" method="post">
    
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
</div>
{{-- 検索機能ここまで　--}}

{{-- ページネーション--}}
<div class="row">
@if(!empty($_POST['search']))
    <nav aria-label="Page navigation example">
        <ul class="pagination pagination-sm">
            <li class="page-item">
                <a class="page-link" href="{{ route('pa0001.count_search_department',['department_page'=>$count_department,'personnel_page'=>1,'id'=>session('client_id'),'id2'=>$select_id,'search'=>$_POST['search']]) }}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <li class="page-item">
@if($count_personnel == 1)
                <a class="page-link" href="{{ route('pa0001.count_search_department',['department_page'=>$count_department,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$select_id,'search'=>$_POST['search']]) }}" aria-label="Previous">
@else
                <a class="page-link" href="{{ route('pa0001.count_search_department',['department_page'=>$count_department,'personnel_page'=>$count_personnel-1,'id'=>session('client_id'),'id2'=>$select_id,'search'=>$_POST['search']]) }}" aria-label="Previous">
@endif
                    <span aria-hidden="true">&lt;</span>
                </a>
            </li>
        @if($personnel_max == 0)
            0/0
        @else
            {{$count_personnel}}/{{$personnel_max}}&nbsp;&nbsp;{{$total_personnel}}件
        @endif
            <li class="page-item">
@if($count_personnel<$personnel_max)
                <a class="page-link" href="{{ route('pa0001.count_search_department',['department_page'=>$count_department,'personnel_page'=>$count_personnel+1,'id'=>session('client_id'),'id2'=>$select_id,'search'=>$_POST['search']]) }}" aria-label="Next">
@else
                <a class="page-link" href="{{ route('pa0001.count_search_department',['department_page'=>$count_department,'personnel_page'=>$personnel_max,'id'=>session('client_id'),'id2'=>$select_id,'search'=>$_POST['search']]) }}" aria-label="Next">
@endif
                    <span aria-hidden="true">&gt;</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="{{ route('pa0001.count_search_department',['department_page'=>$count_department,'personnel_page'=>$personnel_max,'id'=>session('client_id'),'id2'=>$select_id,'search'=>$_POST['search']]) }}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
@elseif(!empty($_POST['search2']))
    <nav aria-label="Page navigation example">
        <ul class="pagination pagination-sm">
            <li class="page-item">
                <a class="page-link" href="{{ route('pa0001.count_search_personnel',['department_page'=>$count_department,'personnel_page'=>1,'id'=>session('client_id'),'id2'=>$select_id,'search'=>$_POST['search2']]) }}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <li class="page-item">
    @if($count_personnel == 1)
                <a class="page-link" href="{{ route('pa0001.count_search_personnel',['department_page'=>$count_department,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$select_id,'search'=>$_POST['search2']]) }}" aria-label="Previous">
    @else
                <a class="page-link" href="{{ route('pa0001.count_search_personnel',['department_page'=>$count_department,'personnel_page'=>$count_personnel-1,'id'=>session('client_id'),'id2'=>$select_id,'search'=>$_POST['search2']]) }}" aria-label="Previous">
    @endif
                    <span aria-hidden="true">&lt;</span>
                </a>
            </li>
            @if($personnel_max == 0)
                0/0
            @else
                {{$count_personnel}}/{{$personnel_max}}&nbsp;&nbsp;{{$total_personnel}}件
            @endif
            <li class="page-item">
    @if($count_personnel<$personnel_max)
                <a class="page-link" href="{{ route('pa0001.count_search_personnel',['department_page'=>$count_department,'personnel_page'=>$count_personnel+1,'id'=>session('client_id'),'id2'=>$select_id,'search'=>$_POST['search2']]) }}" aria-label="Next">
    @else
                <a class="page-link" href="{{ route('pa0001.count_search_personnel',['department_page'=>$count_department,'personnel_page'=>$personnel_max,'id'=>session('client_id'),'id2'=>$select_id,'search'=>$_POST['search2']]) }}" aria-label="Next">
    @endif
                    <span aria-hidden="true">&gt;</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="{{ route('pa0001.count_search_personnel',['department_page'=>$count_department,'personnel_page'=>$personnel_max,'id'=>session('client_id'),'id2'=>$select_id,'search'=>$_POST['search2']]) }}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
@elseif(isset($select_id))
    <nav aria-label="Page navigation example">
        <ul class="pagination pagination-sm">
            <li class="page-item">
                <a class="page-link" href="{{ route('pa0001.count_narrowdown',['department_page'=>$count_department,'personnel_page'=>1,'id'=>session('client_id'),'id2'=>$select_id]) }}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <li class="page-item">
    @if($count_personnel == 1)
                <a class="page-link" href="{{ route('pa0001.count_narrowdown',['department_page'=>$count_department,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$select_id]) }}" aria-label="Previous">
    @else
                <a class="page-link" href="{{ route('pa0001.count_narrowdown',['department_page'=>$count_department,'personnel_page'=>$count_personnel-1,'id'=>session('client_id'),'id2'=>$select_id]) }}" aria-label="Previous">
    @endif
                    <span aria-hidden="true">&lt;</span>
                </a>
            </li>
                @if($personnel_max == 0)
                0/0
                @else
                {{$count_personnel}}/{{$personnel_max}}&nbsp;&nbsp;{{$total_personnel}}件
                @endif
            <li class="page-item">
    @if($count_personnel<$personnel_max)
                <a class="page-link" href="{{ route('pa0001.count_narrowdown',['department_page'=>$count_department,'personnel_page'=>$count_personnel+1,'id'=>session('client_id'),'id2'=>$select_id]) }}" aria-label="Next">
    @else
                <a class="page-link" href="{{ route('pa0001.count_narrowdown',['department_page'=>$count_department,'personnel_page'=>$personnel_max,'id'=>session('client_id'),'id2'=>$select_id]) }}" aria-label="Next">
    @endif
                    <span aria-hidden="true">&gt;</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="{{ route('pa0001.count_narrowdown',['department_page'=>$count_department,'personnel_page'=>$personnel_max,'id'=>session('client_id'),'id2'=>$select_id]) }}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
@else
    <nav aria-label="Page navigation example">
        <ul class="pagination pagination-sm">
            <li class="page-item">
                <a class="page-link" href="{{ route('pa0001.count',['department_page'=>$count_department,'personnel_page'=>1]) }}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <li class="page-item">
    @if($count_personnel == 1)
                <a class="page-link" href="{{ route('pa0001.count',['department_page'=>$count_department,'personnel_page'=>$count_personnel]) }}" aria-label="Previous">
    @else
                <a class="page-link" href="{{ route('pa0001.count',['department_page'=>$count_department,'personnel_page'=>$count_personnel-1]) }}" aria-label="Previous">
    @endif
                    <span aria-hidden="true">&lt;</span>
                </a>
            </li>
            {{$count_personnel}}/{{$personnel_max}}&nbsp;&nbsp;{{$total_personnel}}件
            <li class="page-item">
    @if($count_personnel<$personnel_max)
                <a class="page-link" href="{{ route('pa0001.count',['department_page'=>$count_department,'personnel_page'=>$count_personnel+1]) }}" aria-label="Next">
    @else
                <a class="page-link" href="{{ route('pa0001.count',['department_page'=>$count_department,'personnel_page'=>$personnel_max]) }}" aria-label="Next">
    @endif
                    <span aria-hidden="true">&gt;</span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="{{ route('pa0001.count',['department_page'=>$count_department,'personnel_page'=>$personnel_max]) }}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
@endif
</div>
{{-- ページネーションここまで --}}