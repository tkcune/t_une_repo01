<!-- 詳細画面用　新規作成・移動・挿入・投影・ページネーション・検索 -->
<div class="list-area" id="list">
    <div class="ue-area" style="padding-top:5px">
        <div class="row">
            {{-- 詳細画面 ツリー操作機能　--}}

            {{-- 新規登録ボタン --}}
            <div class="col-4" style="display:inline-flex">
                <p>名称</p>
                <form action="" method="get">
                    <input type="hidden" id="ji_high_new" name="high" value="">
                    <button class="main_button_style" data-toggle="tooltip" title="クリックにより、詳細情報に属する下位情報を新規登録する詳細画面に遷移します">
                        <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.new')}}" alt="新規">
                    </button>
                </form>

                {{-- 移動ボタン --}}
                <form action="#" method="post">
                    @csrf
                    @method('patch')
                    <input type="hidden" id="" name="" value="">
                    <input type="hidden" id="" name="lower_id" value="{{session('clipboard_id')}}">
                    <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧に移動します 移動元からは抹消されます">
                        <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.move')}}" alt="移動">
                    </button>
                </form>

                {{-- 挿入ボタン --}}
                <form action="#" method="post">
                    @csrf
                    @method('post')
                    <input type="hidden" name="client_id" value="">
                    <input type="hidden" name="high_id" value="">
                    <input type="hidden" id="" name="" value="">
                    <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧に挿入します 移動元は消えません">
                        <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.insert')}}" alt="挿入">
                    </button>
                </form>

                {{-- 投影ボタン --}}
                <form action="#" method="post">
                    @csrf
                    @method('post')
                    <input type="hidden" name="projection_source_id" value="">
                    <input type="hidden" name="client_id" value="">
                    <input type="hidden" id="" name="high_id" value="">
                    <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧にショートカットして投影します 移動元は消えません">
                        <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.ji')}}" alt="投影">
                    </button>
                </form>
            </div>
            {{-- ツリー操作機能ここまで　--}}

            {{-- ページネーション--}}
            <div class="col-3">
                <nav aria-label="Page navigation example">
                    <ul class="pagination pagination-sm">
                        <li class="page-item">
                            <a class="page-link" href="#" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

                        <li class="page-item">
                            <a class="page-link" href="#" aria-label="Previous">
                                <span aria-hidden="true">&lt;</span>
                            </a>
                        </li>

                        0/0&nbsp;&nbsp;0件

                        <li class="page-item">
                            <a class="page-link" href="#" aria-label="Next">
                                <span aria-hidden="true">&gt;</span>
                            </a>
                        </li>

                        <li class="page-item">
                            <a class="page-link" href="#" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            {{-- ページネーションここまで --}}

            {{-- 検索機能　--}}
            <div class="col-4" style="display:inline-flex">
                <p>検索</p>
                <form action="#" method="post">
                    @csrf
                    @method('post')
                    <input type="text" name="search" class="top" placeholder="検索" maxlength="32">
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
