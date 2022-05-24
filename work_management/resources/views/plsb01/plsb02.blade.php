<!-- 作業場所詳細画面の操作blade -->
<div class="list-area" id="list">
    <div class="space-area" style="padding-top:5px">
        <div class="row">
            {{-- 作業場所 ツリー操作機能　--}}
            <div class="col-4" style="display:inline-flex">
                <p>配下場所</p>
                <form action="{{ route('pssb01.create') }}" method="get">
                    <input type="hidden" id="high" name="high" value="{{$space_data[0]->space_id}}">
                    <button class="main_button_style" data-toggle="tooltip" title="クリックにより、詳細情報に属する下位情報を新規登録する詳細画面に遷移します">
                        <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.new')}}" alt="新規">
                    </button>
                </form>

                {{-- 作業場所移動ボタン --}}
                <form action="{{ route('pssb01.hierarchyUpdate',[session('client_id')]) }}" method="post">
                    @csrf
                    @method('patch')
                    <input type="hidden" id="high_move" name="high_id" value="{{$space_data[0]->space_id}}">
                    <input type="hidden" id="lower_move" name="lower_id" value="{{session('clipboard_id')}}">
                    <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧に移動します 移動元からは抹消されます">
                        <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.move')}}" alt="移動">
                    </button>
                </form>

                {{-- 作業場所挿入ボタン --}}
                <form action="{{ route('pssb01.copy') }}" method="post">
                    @csrf
                    @method('post')
                    <input type="hidden" name="client_id" value="{{ session('client_id') }}">
                    <input type="hidden" id="copy" name="copy_id" value="{{session('clipboard_id')}}">
                    <input type="hidden" id="high_insert" name="high_id" value="{{$space_data[0]->space_id}}">
                    <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧に挿入します 移動元は消えません">
                        <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.insert')}}" alt="挿入">
                    </button>
                </form>

                {{-- 作業場所投影ボタン --}}
                <form action="{{ route('pssb01.projection') }}" method="post">
                    @csrf
                    @method('post')
                    <input type="hidden" name="client_id" value="{{ session('client_id') }}">
                    <input type="hidden" id="projection_source" name="projection_source_id" value="{{session('clipboard_id')}}">
                    <input type="hidden" id="high_projection" name="high_id" value="{{$space_data[0]->space_id}}">
                    <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧にショートカットして投影します 移動元は消えません">
                        <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.ji')}}" alt="投影">
                    </button>
                </form>

                <form>
                    <custom-tooltip title="新規: 新規登録する詳細画面に遷移します
                        移動: クリップボードにコピーした情報を一覧に移動します。移動元は抹消されます
                        挿入: クリップボードにコピーした情報を一覧に挿入します。移動元は消えません
                        投影: クリップボードにコピーした情報を、一覧にショートカットして投影します。移動元は消えません"></custom-tooltip>
                </form>
            </div>
            {{-- ツリー操作機能ここまで　--}}

            {{-- ページネーション--}}
            <div class="col-3">
                <nav aria-label="Page navigation example">
                    <ul class="pagination pagination-sm">
                        <li class="page-item">
                            @if(!empty($_POST['search']))
                            <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),$click_id,'space_page'=>1,'search'=>$_POST['search']]) }}" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                            @else
                            <a class="page-link" href="{{ route('pssb01.show',[session('client_id'),$click_id,'space_page'=>1]) }}" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                            @endif
                        </li>

                        <li class="page-item">
                            @if(!empty($_POST['search']))
                            @if($count_space <= 1) <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),$click_id,'space_page'=>1,'search'=>$_POST['search']]) }}" aria-label="Previous">
                                <span aria-hidden="true">&lt;</span>
                                </a>
                                @else
                                <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),$click_id,'count'=>$count_space-1,'search'=>$_POST['search']]) }}" aria-label="Previous">
                                    <span aria-hidden="true">&lt;</span>
                                </a>
                                @endif
                                @else
                                @if($count_space <= 1) <a class="page-link" href="{{ route('pssb01.show',[session('client_id'),$click_id,'space_page'=>1]) }}" aria-label="Previous">
                                    <span aria-hidden="true">&lt;</span>
                                    </a>
                                    @else
                                    <a class="page-link" href="{{ route('pssb01.show',[session('client_id'),$click_id,'space_page'=>$count_space-1]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&lt;</span>
                                    </a>
                                    @endif
                                    @endif
                        </li>

                        {{$count_space}}/{{$space_details['max']}}&nbsp;&nbsp;{{$space_details['count']}}件

                        <li class="page-item">
                            @if(!empty($_POST['search']))
                            @if($count_space < $space_details['max']) <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),$click_id,'space_page'=>$count_space+1,'search'=>$_POST['search']]) }}" aria-label="Next">
                                <span aria-hidden="true">&gt;</span>
                                </a>
                                @else
                                <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),$click_id,'space_page'=>$space_details['max'],'search'=>$_POST['search']]) }}" aria-label="Next">
                                    <span aria-hidden="true">&gt;</span>
                                </a>
                                @endif
                                @else
                                @if($count_space < $space_details['max'])
                                <a class="page-link" href="{{ route('pssb01.show',[session('client_id'),$click_id,'space_page'=>$count_space+1]) }}" aria-label="Next">
                                    <span aria-hidden="true">&gt;</span>
                                    </a>
                                    @else
                                    <a class="page-link" href="{{ route('pssb01.show',[session('client_id'),$click_id,'space_page'=>$space_details['max']]) }}" aria-label="Next">
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                    @endif
                                    @endif
                        </li>

                        <li class="page-item">
                            @if(!empty($_POST['search']))
                            <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),$click_id,'space_page'=>$space_details['max'],'search'=>$_POST['search']]) }}" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                            @else
                            <a class="page-link" href="{{ route('pssb01.show',[session('client_id'),$click_id,'space_page'=>$space_details['max']]) }}" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                            @endif
                        </li>
                    </ul>
                </nav>
            </div>
            {{-- ページネーションここまで --}}

            {{-- 検索機能　--}}
            <div class="col-4" style="display:inline-flex">
                <p>検索</p>
                <form action="{{ route('pssb01.search',[session('client_id'),$space_data[0]->space_id]) }}" method="post">
                    @csrf
                    @method('post')
                    @if(!empty($_POST['search']))
                    <input type="text" name="search" class="top" maxlength="32" placeholder="作業場所検索" value="{{ $_POST['search'] }}">
                    @else
                    <input type="text" name="search" class="top" maxlength="32" placeholder="作業場所検索">
                    @endif
                    <button class="main_button_style" data-toggle="tooltip" title="クリックにより、検索文字に従い検索し、一覧に表示するレコードを限定します。文字が入力されていない場合は、全件を表示します" type="submit">
                        <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.search')}}" alt="検索">
                    </button>
                </form>
            </div>
            <div class="col" onclick="listOn()">
                <p style="cursor: hand; cursor:pointer;">✕</p>
            </div>
            {{-- 検索機能ここまで　--}}
        </div>
