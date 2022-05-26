 <!-- 作業場所概要画面 人員ツリー操作機能　 -->
<div class="personnel-area" style="padding-top:5px">
    <div class="row">
        <div class="col-4" style="display:inline-flex;">
            <p>所属人員</p>
            {{-- 人員新規ボタン --}}
            <form action="{{ route('psji01.index') }}" method="get">
                <input type="hidden" id="ji_high_new" name="high" value="bs00000001">
                <button class="main_button_style" data-toggle="tooltip" title="クリックにより、詳細情報に属する下位情報を新規登録する詳細画面に遷移します">
                    <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.new')}}" alt="新規">
                </button>
            </form>

            {{-- 人員移動ボタン --}}
            <form action="{{ route('psbs01.hierarchyUpdate',[session('client_id')]) }}" method="post">
                @csrf
                @method('patch')
                <input type="hidden" id="ji_high_move" name="high_id" value="">
                <input type="hidden" id="ji_lower_move" name="lower_id" value="{{session('clipboard_id')}}">
                <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧に移動します 移動元からは抹消されます">
                    <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.move')}}" alt="移動" disabled style="opacity:0.3">
                </button>
            </form>

            {{-- 人員挿入ボタン --}}
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

            {{-- 人員投影ボタン --}}
            <form action="{{ route('ptcm01.store') }}" method="post">
                @csrf
                @method('post')
                <input type="hidden" name="client_id" value="{{ session('client_id') }}">
                <input type="hidden" id="projection_source" name="projection_source_id" value="{{session('clipboard_id')}}">
                <input type="hidden" id="high_projection" name="high_id" value="ji0000000">
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
                        @if(!empty($_POST['search']))
                        <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),'sb00000000','space_page'=>$count_space,'personnel_page'=>1,'search'=>$_POST['search']]) }}" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                        @elseif(!empty($_POST['search2']))
                        <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),'sb00000000','space_page'=>$count_space,'personnel_page'=>1,'search2'=>$_POST['search2']]) }}" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                        @else
                        <a class="page-link" href="{{ route('pssb01.index',['space_page'=>$count_space,'personnel_page'=>1]) }}" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                        @endif
                    </li>

                    <li class="page-item">
                        @if($count_personnel == 1)
                        @if(!empty($_POST['search']))
                        <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),'sb00000000','space_page'=>$count_space,'personnel_page'=>$count_personnel,'search'=>$_POST['search']]) }}" aria-label="Previous">
                            @elseif(!empty($_POST['search2']))
                            <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),'sb00000000','space_page'=>$count_space,'personnel_page'=>$count_personnel,'search2'=>$_POST['search2']]) }}" aria-label="Previous">
                                @else
                                <a class="page-link" href="{{ route('pssb01.index',['space_page'=>$count_space,'personnel_page'=>$count_personnel]) }}" aria-label="Previous">
                                    @endif
                                    @else
                                    @if(!empty($_POST['search']))
                                    <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),'sb00000000','space_page'=>$count_space,'personnel_page'=>$count_personnel-1,'search'=>$_POST['search']]) }}" aria-label="Previous">
                                        @elseif(!empty($_POST['search2']))
                                        <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),'sb00000000','space_page'=>$count_space,'personnel_page'=>$count_personnel-1,'search2'=>$_POST['search2']]) }}" aria-label="Previous">
                                            @else
                                            <a class="page-link" href="{{ route('pssb01.index',['space_page'=>$count_space,'personnel_page'=>$count_personnel-1]) }}" aria-label="Previous">
                                                @endif
                                                @endif
                                                <span aria-hidden="true">&lt;</span>
                                            </a>
                    </li>

                    {{$count_personnel}}/{{$personnel_data['max']}}&nbsp;&nbsp;{{$personnel_data['count']}}件

                    <li class="page-item">
                        @if($count_personnel<$personnel_data['max']) @if(!empty($_POST['search'])) <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),'sb00000000','space_page'=>$count_space,'personnel_page'=>$count_personnel+1,'search'=>$_POST['search']]) }}" aria-label="Next">
                            @elseif(!empty($_POST['search2']))
                            <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),'sb00000000','space_page'=>$count_space,'personnel_page'=>$count_personnel+1,'search2'=>$_POST['search2']]) }}" aria-label="Next">
                                @else
                                <a class="page-link" href="{{ route('pssb01.index',['space_page'=>$count_space,'personnel_page'=>$count_personnel+1]) }}" aria-label="Next">
                                    @endif
                                    @else
                                    @if(!empty($_POST['search']))
                                    <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),'sb00000000','space_page'=>$count_space,'personnel_page'=>$personnel_data['max'],'search'=>$_POST['search']]) }}" aria-label="Next">
                                        @elseif(!empty($_POST['search2']))
                                        <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),'sb00000000','space_page'=>$count_space,'personnel_page'=>$personnel_data['max'],'search2'=>$_POST['search2']]) }}" aria-label="Next">
                                            @else
                                            <a class="page-link" href="{{ route('pssb01.index',['space_page'=>$count_space,'personnel_page'=>$personnel_data['max']]) }}" aria-label="Next">
                                                @endif
                                                @endif
                                                <span aria-hidden="true">&gt;</span>
                                            </a>
                    </li>

                    <li class="page-item">
                        @if(!empty($_POST['search']))
                        <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),'sb00000000','space_page'=>$count_space,'personnel_page'=>$personnel_data['max'],'search'=>$_POST['search']]) }}" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                        @elseif(!empty($_POST['search2']))
                        <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),'sb00000000','space_page'=>$count_space,'personnel_page'=>$personnel_data['max'],'search2'=>$_POST['search2']]) }}" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                        @else
                        <a class="page-link" href="{{ route('pssb01.index',['space_page'=>$count_space,'personnel_page'=>$personnel_data['max']]) }}" aria-label="Next">
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
                <input type="text" name="search2" class="top" maxlength="32" placeholder="人員検索" value="{{ $_POST['search2'] }}">
                @else
                <input type="text" name="search2" class="top" maxlength="32" placeholder="人員検索">
                @endif
                <button class="main_button_style" data-toggle="tooltip" title="クリックにより、検索文字に従い検索し、一覧に表示するレコードを限定します。文字が入力されていない場合は、全件を表示します" type="submit">
                    <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.search')}}" alt="検索">
                </button>
            </form>
        </div>
    </div>


