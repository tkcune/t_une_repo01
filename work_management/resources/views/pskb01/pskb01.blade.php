@extends('pc0001.pc0001')

@section('js')
    <script src="{{ asset('js/pskb01/pskb01.js') }}" defer></script>
@endsection

@section('css')
    <link href="{{ asset('css/pccp01.css') }}" rel="stylesheet">
@endsection


@section('script')
    {{-- テーブルソート --}}
    <script>
    $(document).ready(function() {
        $('#kb-table').tablesorter({
            headers: {
               5: { sorter: false }
            }
        });
    });
    </script>

    <script>
    $(document).ready(function() {
        $('#ft-table').tablesorter({
            headers: {
               5: { sorter: false }
            }
        });
    });
    </script>
@endsection
    
@section('content')
    <div class="col border border-primary" style="padding:10px;">
        <div class="row">
            <form action="{{ route('pskb01.update',$board_details[0]->board_id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('patch')
            @if(substr($click_id,0,2) == "ta")
            <div class="details-area border border-dark bg-info" style="padding:10px;" id="parent">
            @else
            <div class="details-area border border-dark bg-warning" style="padding:10px;" id="parent">
            @endif
                <div class="row">
                    <div class="col-4" style="margin-top:-5px; margin-right:-12px">
                        <h2>掲示板詳細</h2>
                    </div>
                    <div class="col-4" style="margin-right:-10px">
                        <p id="palent">
                            <span data-toggle="tooltip" id="id_number" title="番号:{{$board_details[0]->board_id}}">タイトル</span>
                            <input type="text" name="name" value="{{$board_details[0]->name}}">
                        </p>
                    </div>
                    
                    <div class="col-3">
                        @if(isset($board_details[0]->high_id))
                        <p>上位:<a href="{{ route('pskb01.show',[session('client_id'),$board_details[0]->high_id])}}" data-toggle="tooltip" title="クリックにより、上位部署に遷移します">{{$board_details[0]->high_name}}</a></p>
                        @endif
                    </div>
                <div>

                <div class="row">
                    <div class="col-4">
                        <p>管理者番号：<input type="text" id="management_number" name="management_number" maxlength="10" value="{{$board_details[0]->management_personnel_id}}" style="width:100px;"
                        data-toggle="tooltip" title="" readonly></p>
                    </div>
                    <div class="col-3" style="padding:0px">
                        <p>管理者名：{{$board_details[0]->management_name}}</p>
                    </div>
                    <div class="col" style="padding:0px">
                    <p>管理者検索：
                        <input type="search" id="search-list" list="keywords" style="width:150px;" autocomplete="on" maxlength="32"
                        data-toggle="tooltip" title="入力に該当した人員の候補を一覧に表示します。表示された人員を選択した場合、その番号が管理者人員番号に表示されます。">
                        <datalist id="keywords">
@foreach($system_management_lists as $system_management_list)
                            <option value="{{$system_management_list->name}}" label="{{$system_management_list->personnel_id}}"></option>
@endforeach
                        </datalist>
                    </p>
                    </div>
                </div>


                    <div class="row">
                        <div class="col">
                            <p>状態:
                            <select name="status" data-toggle="tooltip" title="人員の状態を選択します">
                            <option value="10" @if($board_details[0]->status == "10") selected @endif>議題提出</option>
                            <option value="11" @if($board_details[0]->status == "11") selected @endif>議題化検討</option>
                            <option value="12" @if($board_details[0]->status == "12") selected @endif>検討開始待</option>
                            <option value="13" @if($board_details[0]->status == "13") selected @endif>検討中</option>
                            <option value="14" @if($board_details[0]->status == "14") selected @endif>検討保留</option>
                            <option value="18" @if($board_details[0]->status == "18") selected @endif>検討終了</option>
                            </select>

                            参照ファイル
                            <input type="hidden" id="file_id" name="file_id" value="">
                            <input type="file" name="file_name[]" id="file_name" multiple value="" style="width:254px;" data-toggle="tooltip" title="ファイルをアップロード" >
                            <button formaction="{{ route('pskb01.fileUpload',[session('client_id'),$board_details[0]->board_id])}}" formmethod="post">追加</button>

                            <button class="main_button_style" type="button" id="remarks_change_display" onclick="remarksOn()" data-toggle="tooltip" title="クリックにより、備考及び登録日などの情報を開きます">
                                <img class="remarks_button" src="data:image/png;base64,{{Config::get('base64.updown')}}" alt="開閉" >
                            </button>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <input type="hidden" id="url_id" name="url_id" value="">
                            <p>URL
                            <input type="text" id="url" name="url" value="" style="width:700px;"
                            data-toggle="tooltip" title="URLを追加">
                            <button formaction="{{ route('pskb01.urlup',[session('client_id'),$board_details[0]->board_id])}}" formmethod="post">更新</button>
                            </p>
                        </div>
                    </div>

                    <input type="hidden" id="remarks" name="remarks" value="{{$board_details[0]->remarks}}">

                    <div class="row margin-reset" id="remarks-field" style="display:none">
                        <div>
                            備考
                        </div>
                        <div>
                            <textarea id="remarks_set" onchange = "remarks(this value)" maxlength="512" style="width:800px; height: 60px;">{{$board_details[0]->remarks}}</textarea>
                        </div>
                    </div>

                    <div class="row little-information-field" id="little-information-field" style="display:none">
                        <p>登録日:{{$board_details[0]->created_at}} 修正日:{{$board_details[0]->updated_at}}
                            <button class="main_button_style" type="button" data-toggle="tooltip" title="スタンプでリアクションする">
                                <img class="remarks_button" src="data:image/png;base64,{{Config::get('base64.stamp')}}" alt="スタンプ" onclick="stamp()">
                            </button>
                        </p>

                        @include('pcsp01.pcsp01')
                    </div>

                    <div class="row margin-reset">
                        <div class="col">
                            <div style="display:inline-flex">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.ok')}}" alt="確定" onclick="submit();" id="updateOn" data-toggle="tooltip" title="クリックにより、登録、更新を確定します"
                            style="opacity: 0.3;" disabled>
        </form>
                            <form action="{{ route('pskb01.create') }}" method="get">
                            @csrf
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.new')}}" alt="新規" onclick="submit();"
                            data-toggle="tooltip" title="本データの下位に新しいデータを追加します">
                            <input type="hidden" id="high_new" name="high" value="{{$board_details[0]->board_id}}">
                            </form>

                            @if(substr($click_id,0,2) == "ta")
                            <form action="{{ route('pskb01.destroy',$click_id)}}" method="post">
                            @else
                            <form action="{{ route('pskb01.destroy',$board_details[0]->board_id) }}" method="post">
                            @endif
                            @csrf
                            @method('delete')
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.delete')}}" alt="削除" onclick="submit();" id="delete" data-toggle="tooltip" 
                            title="削除有効化をチェックした状態でのクリックにより、詳細領域のデータを下位ツリーのデータを含めて削除します"  disabled>
                            </form>

                            @if(substr($click_id,0,2) == "ta")
                            <form action="{{ route('pa0001.clipboard',$click_id)}}" method="get">
                            @else
                            <form action="{{ route('pa0001.clipboard',$board_details[0]->board_id)}}" method="get">
                            @endif
                            @csrf
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.copy')}}" alt="複写" onclick="submit();" id="copyTarget"
                            data-toggle="tooltip" title="クリックにより、詳細領域のデータをクリップボードに複写します">
                            </form>

                            <form action="{{ route('pa0001.deleteclipboard')}}" method="get">
                            @csrf
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.remove')}}" alt="取消" onclick="submit();"
                            data-toggle="tooltip" title="クリップボードに複写した内容を抹消します" @if(null == session()->get('clipboard_id'))) disabled style="opacity:0.3;" @endif>
                            </form>

                            <input type="hidden" id="tree_disabled" value="{{session('client_id')}}">
                            <button class="main_button_style" type="button" id="tree_change_display" data-toggle="tooltip" title="本機能を隠蔽、もしくは隠蔽状態を解除します 隠蔽した機能をツリー画面に表示するためには、ツリー画面で露出をクリックします">
                                <img class="main_button_img" src="data:image/png;base64,{{Config::get('base64.ng')}}" alt="隠蔽/表示" >
                            </button>

                            @if(substr($click_id,0,2) == "ta")
                            <form action="{{ route('pskb01.show',[session('client_id'),$click_id])}}" method="get">
                            @else
                            <form action="{{ route('pskb01.show',[session('client_id'),$board_details[0]->board_id])}}" method="get">
                            @endif
                                <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.road')}}" alt="再表示" onclick="submit();" id="open_tree" data-toggle="tooltip" title="ツリーを再表示します">
                            </form>

                            <button class="main_button_style" type="button" data-toggle="tooltip" title="ツリーを表示します" onclick="displayOn()">
                                <img class="main_button_img" src="data:image/png;base64,{{Config::get('base64.tree')}}" alt="開く" >
                            </button>

                            <input type="checkbox" id="check" onclick="deleteOn3()" data-toggle="tooltip" title="チェックを入れることで削除ボタンがクリックできるようになります（削除権限がある場合）">
                            <font size="-2" color="red">削除有効化</font>

                            <input type="checkbox" id="check2" onclick="updateOn()" data-toggle="tooltip" title="チェックを入れることで更新ボタンがクリックできるようになります（権限がある場合）">
                            <font size="-2" color="red">更新有効化</font>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>

    <div id="list-open-button" style="display:none;" onclick="listOn()">
        <p style="text-align:center; cursor: hand; cursor:pointer; background:#99CCFF; border:solid 1px;">↓</p>
    </div>
    
    <div class="list-area" id= "list">
                <div class="row" style="padding-top:5px">
                    {{-- ツリー操作機能　--}}
                    <div class="col-4" style="display:inline-flex">
                        <p>一覧画面</p>
                        <form action="{{ route('pskb01.create') }}" method="get">
                        <input type="hidden" id="kb_high_new" name="high" value="{{$board_details[0]->board_id}}">
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、詳細情報に属する下位情報を新規登録する詳細画面に遷移します">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.new')}}" alt="新規">
                        </button>
                        </form>

                        <form action="{{ route('psbs01.hierarchyUpdate',[session('client_id')]) }}" method="post">
                        @csrf
                        @method('patch')
                        <input type="hidden" id="high_move" name="high_id" value="{{$board_details[0]->board_id}}">
                        <input type="hidden" id="lower_move" name="lower_id" value="{{session('clipboard_id')}}"> 
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧に移動します 移動元からは抹消されます">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.move')}}" alt="移動">
                        </button>
                        </form>

                        <form action="{{ route('pskb01.copy') }}" method="post">
                        @csrf
                        @method('post')
                        <input type="hidden" name="client_id" value="{{ session('client_id') }}">
                        <input type="hidden" id="copy" name="copy_id" value="{{session('clipboard_id')}}">
                        <input type="hidden" id="high_insert" name="high_id" value="{{$board_details[0]->board_id}}">
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧に挿入します 移動元は消えません">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.insert')}}" alt="挿入">
                        </button>
                        </form>

                        <form action="{{ route('ptcm01.store') }}" method="post">
                        @csrf
                        @method('post')
                        <input type="hidden" name="client_id" value="{{ session('client_id') }}">
                        <input type="hidden" id="projection_source" name="projection_source_id" value="{{session('clipboard_id')}}">
                        <input type="hidden" id="high_projection" name="high_id" value="{{$board_details[0]->board_id}}">
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
                                @if(!empty($_POST['search']))
                                    <a class="page-link" href="{{ route('pskb01.search',[session('client_id'),$click_id,'count'=>1,'search'=>$_POST['search']]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                @else
                                    <a class="page-link" href="{{ route('pskb01.show',[session('client_id'),$click_id,'count'=>1]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                @endif
                                </li>
                                <li class="page-item">
                                @if(!empty($_POST['search']))
                                    @if($count_board <= 1)
                                    <a class="page-link" href="{{ route('pskb01.search',[session('client_id'),$click_id,'count'=>1,'search'=>$_POST['search']]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&lt;</span>
                                    </a>
                                    @else
                                    <a class="page-link" href="{{ route('pskb01.search',[session('client_id'),$click_id,'count'=>$count_board-1,'search'=>$_POST['search']]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&lt;</span>
                                    </a>
                                    @endif
                                @else
                                    @if($count_board <= 1)
                                    <a class="page-link" href="{{ route('pskb01.show',[session('client_id'),$click_id,'count'=>1]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&lt;</span>
                                    </a>
                                    @else
                                    <a class="page-link" href="{{ route('pskb01.show',[session('client_id'),$click_id,'count'=>$count_board-1]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&lt;</span>
                                    </a>
                                    @endif
                                @endif
                                </li>
                                {{$count_board}}/{{$board_lists['max']}}&nbsp;&nbsp;{{$board_lists['count']}}件
                                <li class="page-item">
                                @if(!empty($_POST['search']))
                                    @if($count_board < $board_lists['max'])
                                    <a class="page-link" href="{{ route('pskb01.search',[session('client_id'),$click_id,'count'=>$count_board+1,'search'=>$_POST['search']]) }}" aria-label="Next">
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                    @else
                                    <a class="page-link" href="{{ route('pskb01.search',[session('client_id'),$click_id,'count'=>$board_lists['max'],'search'=>$_POST['search']]) }}" aria-label="Next">
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                    @endif
                                @else
                                    @if($count_board < $board_lists['max'])
                                    <a class="page-link" href="{{ route('pskb01.show',[session('client_id'),$click_id,'count'=>$count_board+1]) }}" aria-label="Next">
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                    @else
                                    <a class="page-link" href="{{ route('pskb01.show',[session('client_id'),$click_id,'count'=>$board_lists['max']]) }}" aria-label="Next">
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                    @endif
                                @endif
                                </li>
                                <li class="page-item">
                                @if(!empty($_POST['search']))
                                    <a class="page-link" href="{{ route('pskb01.search',[session('client_id'),$click_id,'count'=>$board_lists['max'],'search'=>$_POST['search']]) }}" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                @else
                                    <a class="page-link" href="{{ route('pskb01.show',[session('client_id'),$click_id,'count'=>$board_lists['max']]) }}" aria-label="Next">
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
                        <form action="{{ route('pskb01.search',[session('client_id'),$board_details[0]->board_id]) }}" method="post">
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

                    <div class="col" style = '' onclick="listOn()">
                        <p style="cursor: hand; cursor:pointer;">✕</p>
                    </div>
                    {{-- 検索機能ここまで　--}}
                </div>
                
                <div class="row margin-reset">
                    <div class="col">
                        <div class="border border-dark">
                            <table id="kb-table" class="kb-table table_sticky table table-striped" style="margin-bottom:0px;margin-top:0px;">
                                <thead>
                                <tr>
                                    <th >番号</th>
                                    <th width="140">名称</th>
                                    <th width="140">上位</th>
                                    <th width="150">登録日</th>
                                    <th width="120">管理者</th>
                                    <th width="190">操作</th>
                                </thead>
                                <tbody>
                                @foreach($board_lists['data'] as $board_list['data'])
                                    <tr>
                                    <td width="100">{{$board_list['data']->board_id}}</td>
                                    <td width="140"><a href="{{ route('pskb01.show',[session('client_id'),$board_list['data']->board_id])}}" data-toggle="tooltip" title="">{{$board_list['data']->name}}</a></td>
                                    
                                    <td width="140"><a @if(isset($board_list['data']->high_id))<a href="{{ route('pskb01.show',[session('client_id'),$board_list['data']->high_id])}}" data-toggle="tooltip" title="">{{$board_list['data']->high_name}}</a>@endif</td>
                        
                                    <td width="150">{{$board_list['data']->created_at}}
                                    <td width="120"><a href="{{ route('plbs01.show',[session('client_id'),$board_list['data']->management_personnel_id])}}" data-toggle="tooltip" title="">{{$board_list['data']->management_name}}</a></td>
                                    <td width="170">【<a href="#">複写</a>】
                                    【<p id="kb_list_delete{{$loop->index}}" name="kb_delete" style="pointer-events: none; display:inline-block; text-decoration:underline; margin:0px;" onclick="event.preventDefault(); document.getElementById('delete{{$loop->index}}').submit();">削除</p>】
                                    <form id="delete{{$loop->index}}" action="#" method="post" style="display: none;">
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

                <div class="row" style="padding-top:5px">
                    {{-- ツリー操作機能　--}}
                    <div class="col-4" style="display:inline-flex">
                        <p>付帯定義</p>
                        <form action="{{ route('pskb01.create') }}" method="get">
                        <input type="hidden" id="kb_high_new" name="high" value="{{$board_details[0]->board_id}}">
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、詳細情報に属する下位情報を新規登録する詳細画面に遷移します">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.new')}}" alt="新規">
                        </button>
                        </form>

                        <form action="{{ route('psbs01.hierarchyUpdate',[session('client_id')]) }}" method="post">
                        @csrf
                        @method('patch')
                        <input type="hidden" id="high_move" name="high_id" value="{{$board_details[0]->board_id}}">
                        <input type="hidden" id="lower_move" name="lower_id" value="{{session('clipboard_id')}}"> 
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧に移動します 移動元からは抹消されます">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.move')}}" alt="移動">
                        </button>
                        </form>

                        <form action="{{ route('pskb01.copy') }}" method="post">
                        @csrf
                        @method('post')
                        <input type="hidden" name="client_id" value="{{ session('client_id') }}">
                        <input type="hidden" id="copy" name="copy_id" value="{{session('clipboard_id')}}">
                        <input type="hidden" id="high_insert" name="high_id" value="{{$board_details[0]->board_id}}">
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧に挿入します 移動元は消えません">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.insert')}}" alt="挿入">
                        </button>
                        </form>

                        <form action="{{ route('ptcm01.store') }}" method="post">
                        @csrf
                        @method('post')
                        <input type="hidden" name="client_id" value="{{ session('client_id') }}">
                        <input type="hidden" id="projection_source" name="projection_source_id" value="{{session('clipboard_id')}}">
                        <input type="hidden" id="high_projection" name="high_id" value="{{$board_details[0]->board_id}}">
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
                                @if(!empty($_POST['search']))
                                    <a class="page-link" href="{{ route('pskb01.search',[session('client_id'),$click_id,'count2'=>1,'search'=>$_POST['search']]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                @else
                                    <a class="page-link" href="{{ route('pskb01.show',[session('client_id'),$click_id,'count2'=>1]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                @endif
                                </li>
                                <li class="page-item">
                                @if(!empty($_POST['search']))
                                    @if($count_board <= 1)
                                    <a class="page-link" href="{{ route('pskb01.search',[session('client_id'),$click_id,'count2'=>1,'search'=>$_POST['search']]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&lt;</span>
                                    </a>
                                    @else
                                    <a class="page-link" href="{{ route('pskb01.search',[session('client_id'),$click_id,'count2'=>$count_incidental-1,'search'=>$_POST['search']]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&lt;</span>
                                    </a>
                                    @endif
                                @else
                                    @if($count_board <= 1)
                                    <a class="page-link" href="{{ route('pskb01.show',[session('client_id'),$click_id,'count2'=>1]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&lt;</span>
                                    </a>
                                    @else
                                    <a class="page-link" href="{{ route('pskb01.show',[session('client_id'),$click_id,'count2'=>$count_incidental-1]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&lt;</span>
                                    </a>
                                    @endif
                                @endif
                                </li>
                                @if($incidental_lists['max'] == 0)
                                0/0&nbsp;&nbsp;0件
                                @else
                                {{$count_incidental}}/{{$incidental_lists['max']}}&nbsp;&nbsp;{{$incidental_lists['count']}}件
                                @endif
                                <li class="page-item">
                                @if(!empty($_POST['search']))
                                    @if($count_incidental < $incidental_lists['max'])
                                    <a class="page-link" href="{{ route('pskb01.search',[session('client_id'),$click_id,'count2'=>$count_incidental+1,'search'=>$_POST['search']]) }}" aria-label="Next">
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                    @else
                                    <a class="page-link" href="{{ route('pskb01.search',[session('client_id'),$click_id,'count2'=>$incidental_lists['max'],'search'=>$_POST['search']]) }}" aria-label="Next">
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                    @endif
                                @else
                                    @if($count_incidental < $incidental_lists['max'])
                                    <a class="page-link" href="{{ route('pskb01.show',[session('client_id'),$click_id,'count2'=>$count_incidental+1]) }}" aria-label="Next">
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                    @else
                                    <a class="page-link" href="{{ route('pskb01.show',[session('client_id'),$click_id,'count2'=>$incidental_lists['max']]) }}" aria-label="Next">
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                    @endif
                                @endif
                                </li>
                                <li class="page-item">
                                @if(!empty($_POST['search']))
                                    <a class="page-link" href="{{ route('pskb01.search',[session('client_id'),$click_id,'count'=>$incidental_lists['max'],'search'=>$_POST['search']]) }}" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                @else
                                    <a class="page-link" href="{{ route('pskb01.show',[session('client_id'),$click_id,'count'=>$incidental_lists['max']]) }}" aria-label="Next">
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
                        <form action="{{ route('pskb01.search',[session('client_id'),$board_details[0]->board_id]) }}" method="post">
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

                    {{-- 検索機能ここまで　--}}
                </div>
                
                <div class="row margin-reset">
                    <div class="col">
                        <div class="border border-dark">
                            <table id="ft-table" class="ft-table table_sticky table table-striped" style="margin-bottom:0px;margin-top:0px;">
                                <thead>
                                <tr>
                                    <th width="100">番号</th>
                                    <th width="360">付帯定義</th>
                                    <th width="360">操作</th>
                                </thead>
                                <tbody>
                                @foreach($incidental_lists['data'] as $incidental_list['data'])
                                    <tr>
                                    <td id="incidental_id{{$loop->index}}" width="100">{{$incidental_list['data']->incidental_id}}</td>
                                    @if(substr($incidental_list['data']->data_id,0,2) == "fi")
                                    <td width="360"><a href="{{route('pskb01.download',[session('client_id'),$incidental_list['data']->data_id])}}" data-toggle="tooltip" title="" id="incidental_name{{$loop->index}}">{{$incidental_list['data']->name}}</a></td> 
                                    @else
                                    <td id="incidental_name{{$loop->index}}" width="360"><a href="{{$incidental_list['data']->url_path}}" data-toggle="tooltip" title="">{{$incidental_list['data']->url_path}}</a></td>
                                    @endif
                                    <td width="360">@if(substr($incidental_list['data']->data_id,0,2) == "ur")【<a href="#" onclick="(function(){var val = document.getElementById('incidental_id{{$loop->index}}').innerHTML;var val2 = document.getElementById('incidental_name{{$loop->index}}').textContent;reflection(val,val2);})();">反映</a>】@endif【<a href="#">複写</a>】
                                    【<p id="ft_list_delete{{$loop->index}}" name="kb_delete" style="pointer-events: none; display:inline-block; text-decoration:underline; margin:0px;" onclick="event.preventDefault(); document.getElementById('delete{{$loop->index}}').submit();">削除</p>】
                                    @if(substr($incidental_list['data']->data_id,0,2) == "ur")
                                    <form id="delete{{$loop->index}}" action="{{route('pskb01.urlDelete',[session('client_id'),$incidental_list['data']->data_id,$incidental_list['data']->incidental_id])}}" method="post" style="display: none;">
                                    @else
                                    <form id="delete{{$loop->index}}" action="{{route('pskb01.fileDelete',[session('client_id'),$incidental_list['data']->data_id,$incidental_list['data']->incidental_id])}}" method="post" style="display: none;">
                                    @endif
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

@endsection