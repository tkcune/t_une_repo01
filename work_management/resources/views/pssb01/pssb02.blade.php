<!-- blade分割前 -->
@extends('pc0001.pc0001')

@section('js')
<script src="{{ asset('js/pssb01/pssb01.js') }}" defer></script>
@endsection

@section('script')
{{-- テーブルソート --}}
<script>
    $(document).ready(function() {
        $('#sb-table').tablesorter({
            headers: {
                5: {
                    sorter: false
                }
            }
        });
    });
</script>
@endsection

@section('content')
<div class="col border border-primary" style="padding:10px;">
    <div class="row">
        <form action="{{ route('pssb01.update',$space_data[0]->space_id) }}" method="post">
            @csrf
            @method('patch')
            <input type="hidden" id="space_id" name="space_id" value="{{$space_data[0]->space_id}}">
            <input type="hidden" name="client_id" value="{{ session('client_id') }}">
            @if(substr($click_id,0,2) == "ta")
            <div class="details-area border border-dark bg-info" style="padding:10px;" id="parent">
                @else
                <div class="details-area border border-dark bg-info" style="padding:10px;" id="parent">
                    @endif
                    <div class="row">
                        <div class="col-4" style="margin-top:-5px; margin-right:-12px">
                            <h2>作業場所詳細</h2>
                        </div>
                        <div class="col-4" style="margin-right:-10px">
                            <p id="palent">
                                <span data-toggle="tooltip" id="id_number" title="番号:{{$space_data[0]->space_id}}">
                                    名称：<input type="text" name="name" value="{{$space_data[0]->name}}" placeholder="作業場所名" data-toggle="tooltip" title="">
                            </p>
                        </div>

                        <div class="col-3">
                            @if(isset($space_data[0]->high_id))
                            <p>上位：<a href="{{ route('pssb01.show',[session('client_id'),$space_data[0]->high_id])}}" data-toggle="tooltip" title="クリックにより、上位部署に遷移します">{{$space_data[0]->high_name}}</a></p>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-4">
                                <p>管理者番号：<input type="text" id="management_number" name="management_number" maxlength="10" value="{{$space_data[0]->management_personnel_id}}" style="width:100px;" data-toggle="tooltip" title="" readonly></p>
                            </div>
                            <div class="col-3" style="padding:0px">
                                <p>管理者名：{{$space_data[0]->management_name}}</p>
                            </div>
                            <div class="col" style="padding:0px">
                                <p>管理者検索：
                                    <input type="search" id="search-list" list="keywords" style="width:150px;" autocomplete="on" maxlength="32" placeholder="管理者名を選択" data-toggle="tooltip" title="入力に該当した人員の候補を一覧に表示します。表示された人員を選択した場合、その番号が管理者人員番号に表示されます。">
                                    <datalist id="keywords">
                                        @foreach($system_management_lists as $system_management_list)
                                        <option value="{{$system_management_list->name}}" label="{{$system_management_list->personnel_id}}"></option>
                                        @endforeach
                                    </datalist>
                                </p>
                            </div>
                        </div>

                        <div class="row margin-reset">
                            <div class="col-4">
                                <p>郵便番号：<input type="text" name="postcode" size="10" value="{{$space_data[0]->post_code}}" maxlength="8" title="郵便番号はハイフン不要の7桁で入力してください。"  onKeyUp="AjaxZip3.zip2addr(this,'','prefectural','address');" placeholder="0123456">
                            </div>
                            <div class="col" style="padding:0px">
                                住所:
                                <input type="text" name="prefectural" value="{{$space_data[0]->prefectural_office_location}}" size="10" placeholder="都道府県" title="ここに都道府県名が入ります。">
                                <input type="text" name="address" value="{{$space_data[0]->address}}" size="30"  placeholder="市区町村以降" title="ここに市区町村名が入ります。">
                                </p>
                            </div>
                            <div class="row margin-reset">
                                <div class="col">
                                    <p>URL：<input type="url" name="URL" value="{{$space_data[0]->URL}}" size="81" placeholder="https://www.google.co.jp/maps/?hl=ja" title="ここに作業場所の地図のURLを入力します。">
                                        {{-- 作業場所のマップURL、値がNULLならグーグルマップが表示される --}}
                                        @if(($space_data[0]->URL) === NULL)
                                        <button class="main_button_style" type="button" id="remarks_change_display" onclick="window.open('https://www.google.com/maps/','_blank')" data-toggle="tooltip" title="クリックにより、地図を開きます">
                                            <img class="remarks_button" src="data:image/png;base64,{{Config::get('base64.tizu')}}" alt="地図">
                                        </button>
                                        @else
                                        <button class="main_button_style" type="button" id="remarks_change_display" onclick="window.open('{{$space_data[0]->URL}}','_blank')" data-toggle="tooltip" title="クリックにより、地図を開きます">
                                            <img class="remarks_button" src="data:image/png;base64,{{Config::get('base64.tizu')}}" alt="地図">
                                        </button>
                                        @endif

                                        <button class="main_button_style" type="button" id="remarks_change_display" onclick="remarksOn()" data-toggle="tooltip" title="クリックにより、備考及び登録日などの情報を開きます">
                                            <img class="remarks_button" src="data:image/png;base64,{{Config::get('base64.updown')}}" alt="開閉">
                                        </button>
                                    </p>
                                </div>
                            </div>

                            <input type="hidden" id="remarks" name="remarks" value="{{ $space_data[0]->remarks }}">

                        <div class="row margin-reset" id="remarks-field" style="display:none"">
                            <div>
                                備考
                            </div>
                            <div>
                                <textarea id="remarks_set" onchange = "remarks(this value)" maxlength="512" placeholder="備考欄は任意で入力します。" style="width:800px; height: 60px;">{{ $space_data[0]->remarks }}</textarea>
                            </div>
                        </div>

                        <div class="row" id="little-information-field" style="display:none">
                            <p>登録日:{{$space_data[0]->created_at}} 修正日:{{$space_data[0]->updated_at}}</p>
                        </div>

                        <div class="row margin-reset">
                            <div class="col">
                                <div style="display:inline-flex">
                                    <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.ok')}}" alt="確定" onclick="submit();" id="updateOn" data-toggle="tooltip" title="クリックにより、登録、更新を確定します" style="opacity: 0.3;" disabled>
        </form>

        <form action="{{ route('pssb01.create') }}" method="get">
            @csrf
            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.new')}}" alt="新規" onclick="submit();" data-toggle="tooltip" title="本データの下位に新しいデータを追加します">
            <input type="hidden" id="high_new" name="high" value="{{ $space_data[0]->space_id }}">
        </form>

        @if(substr($click_id,0,2) == "ta")
        <form action="{{ route('ptcm01.delete',[session('client_id'),$click_id])}}" method="post">
            @else
            <form action="{{ route('pssb01.destroy',[session('client_id'),$space_data[0]->space_id])}}" method="post">
                @endif
                @csrf
                @method('post')
                <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.delete')}}" alt="削除" onclick="submit();" id="delete" data-toggle="tooltip" title="削除有効化をチェックした状態でのクリックにより、詳細領域のデータを下位ツリーのデータを含めて削除します" disabled>
            </form>

            @if(substr($click_id,0,2) == "ta")
            <form action="{{ route('pa0001.clipboard',$click_id)}}" method="get">
                @else
                <form action="{{ route('pa0001.clipboard',$space_data[0]->space_id)}}" method="get">
                    @endif
                    @csrf
                    <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.copy')}}" alt="複写" onclick="submit();" id="copyTarget" data-toggle="tooltip" title="クリックにより、詳細領域のデータをクリップボードに複写します">
                </form>

                <form action="{{ route('pa0001.deleteclipboard')}}" method="get">
                    @csrf
                    <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.remove')}}" alt="取消" onclick="submit();" data-toggle="tooltip" title="クリップボードに複写した内容を抹消します" @if(null==session()->get('clipboard_id'))) disabled style="opacity:0.3" @endif>
                </form>

                <input type="hidden" id="tree_disabled" value="{{session('client_id')}}">
                <button class="main_button_style" type="button" id="tree_change_display" data-toggle="tooltip" title="本機能を隠蔽、もしくは隠蔽状態を解除します 隠蔽した機能をツリー画面に表示するためには、ツリー画面で露出をクリックします">
                    <img class="main_button_img" src="data:image/png;base64,{{Config::get('base64.ng')}}" alt="隠蔽/表示">
                </button>

                @if(substr($click_id,0,2) == "ta")
                <form action="{{ route('pssb01.show',[session('client_id'),$click_id])}}" method="get">
                    @else
                    <form action="{{ route('pssb01.show',[session('client_id'),$space_data[0]->space_id])}}" method="get">
                        @endif
                        <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.road')}}" alt="再表示" onclick="submit();" id="open_tree" data-toggle="tooltip" title="ツリーを再表示します">
                    </form>

                    <button class="main_button_style" type="button" data-toggle="tooltip" title="ツリーを表示します" onclick="displayOn()">
                        <img class="main_button_img" src="data:image/png;base64,{{Config::get('base64.tree')}} alt=" 開く">
                    </button>

                    <input type="checkbox" id="check" onclick="deleteOn5()" data-toggle="tooltip" title="チェックを入れることで削除ボタンがクリックできるようになります（削除権限がある場合）">
                    <font size="-2" color="red">削除有効化</font>

                    <input type="checkbox" id="check2" onclick="updateOn()" data-toggle="tooltip" title="チェックを入れることで更新ボタンがクリックできるようになります（権限がある場合）">
                    <font size="-2" color="red">更新有効化</font>
                </form>
            </form>
        </form>
    </div>
</div>
</div>
</div>
</div>
</div>
</div>


<div class="personnel-area" style="padding-top:5px">
    <div class="row">
        {{-- ツリー操作機能　--}}
        <div class="col-4" style="display:inline-flex">
            <p>配下場所</p>
            <form action="{{ route('pssb01.create') }}" method="get">
                <input type="hidden" id="high" name="high" value="{{$space_data[0]->space_id}}">
                <button class="main_button_style" data-toggle="tooltip" title="クリックにより、詳細情報に属する下位情報を新規登録する詳細画面に遷移します">
                    <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.new')}}" alt="新規">
                </button>
            </form>

            <form action="{{ route('pssb01.hierarchyUpdate',[session('client_id')]) }}" method="post">
                @csrf
                @method('patch')
                <input type="hidden" id="high_move" name="high_id" value="{{$space_data[0]->space_id}}">
                <input type="hidden" id="lower_move" name="lower_id" value="{{session('clipboard_id')}}">
                <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧に移動します 移動元からは抹消されます">
                    <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.move')}}" alt="移動">
                </button>
            </form>

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
        </div>
        {{-- ツリー操作機能ここまで　--}}

        {{-- ページネーション--}}
        <div class="col-3">
            <nav aria-label="Page navigation example">
                <ul class="pagination pagination-sm">
                    <li class="page-item">
                        @if(!empty($_POST['search']))
                        <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),$space_data[0]->space_id,'count'=>1,'search'=>$_POST['search']]) }}" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                        @else
                        <a class="page-link" href="{{ route('pssb01.show',[session('client_id'),$space_data[0]->space_id,'count'=>1]) }}" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                        @endif
                    </li>
                    <li class="page-item">
                        @if(!empty($_POST['search']))
                        <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),$space_data[0]->space_id,'count'=>$count_space-1,'search'=>$_POST['search']]) }}" aria-label="Previous">
                            <span aria-hidden="true">&lt;</span>
                        </a>
                        @else
                        <a class="page-link" href="{{ route('pssb01.show',[session('client_id'),$space_data[0]->space_id,'count'=>$count_space-1]) }}" aria-label="Previous">
                            <span aria-hidden="true">&lt;</span>
                        </a>
                        @endif
                    </li>

                    {{$count_space}}/{{$space_details['max']}}&nbsp;&nbsp;{{$space_details['count']}}件

                    <li class="page-item">
                        @if(!empty($_POST['search']))
                        @if($count_space < $space_details['max'])
                            <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),$space_data[0]->space_id,'count'=>$count_space+1,'search'=>$_POST['search']]) }}" aria-label="Next">
                                <span aria-hidden="true">&gt;</span>
                            </a>
                        @else
                            <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),$space_data[0]->space_id,'count'=>$space_details['max'],'search'=>$_POST['search']]) }}" aria-label="Next">
                                <span aria-hidden="true">&gt;</span>
                            </a>
                        @endif
                        @else
                        @if($count_space < $space_details['max'])
                            <a class="page-link" href="{{ route('pssb01.show',[session('client_id'),$space_data[0]->space_id,'count'=>$count_space+1]) }}" aria-label="Next">
                                <span aria-hidden="true">&gt;</span>
                            </a>
                        @else
                            <a class="page-link" href="{{ route('pssb01.show',[session('client_id'),$space_data[0]->space_id,'count'=>$space_details['max']]) }}" aria-label="Next">
                                <span aria-hidden="true">&gt;</span>
                            </a>
                        @endif
                        @endif
                    </li>

                    <li class="page-item">
                        @if(!empty($_POST['search']))
                            <a class="page-link" href="{{ route('pssb01.search',[session('client_id'),$space_data[0]->space_id,'count'=>$space_details['max'],'search'=>$_POST['search']]) }}" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        @else
                            <a class="page-link" href="{{ route('pssb01.show',[session('client_id'),$space_data[0]->space_id,'count'=>$space_details['max']]) }}" aria-label="Next">
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
        {{-- 検索機能ここまで　--}}
    </div>

    <div class="row margin-reset">
        <div class="col">
            <div class="border border-dark">
                <table id="sb-table" class="sb-table table_sticky-info table table-striped" style="margin-bottom:0px;margin-top:0px;">
                    <thead>
                        <tr>
                            <th width="100">番号</th>
                            <th width="300">名称</th>
                            <th width="300">上位</>
                            <th width="300">操作</th>
                    </thead>
                    <tbody>
                        @foreach($space_details['data'] as $space['data'])
                        <tr>
                            <td>{{$space['data']->space_id}}</td>
                            <td><a href="{{ route('pssb01.show',[session('client_id'),$space['data']->space_id])}}" data-toggle="tooltip" title="">{{$space['data']->name}}</a></td>
                            <td><a @if(isset($space['data']->high_id))<a href="{{ route('pssb01.show',[session('client_id'),$space['data']->high_id])}}" data-toggle="tooltip" title="">{{$space['data']->high_name}}</a>@endif</td>
                            <td>【<a href="{{ route('pa0001.clipboard',$space['data']->space_id)}}">複写</a>】
                                【<p id="sb_list_delete{{$loop->index}}" name="sb_delete" style="pointer-events: none; display:inline-block; text-decoration:underline; margin:0px;" onclick="event.preventDefault(); document.getElementById('delete{{$loop->index}}').submit();">削除</p>】
                                <form id="delete{{$loop->index}}" action="{{ route('pssb01.destroy',[session('client_id'),$space['data']->space_id])}}" method="post" style="display: none;">
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
<!-- 郵便番号検索は→を一旦利用 https://www.webdesign-fan.com/ajaxzip3  -->
<script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8"></script>
@endsection
