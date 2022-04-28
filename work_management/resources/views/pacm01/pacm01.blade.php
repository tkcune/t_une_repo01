@extends('pc0001.pc0001')

@section('content')

    {{-- コメント　詳細画面ここから --}}
    {{-- 部署の詳細表示--}}
    <div class="col border border-primary" style="padding:10px;">

    @if( empty(session('click_code')) or session('click_code') == "bs")
    <form action="{{ route('psbs01.update') }}" method="post">
            @csrf
            @method('patch')
            <input type="hidden" id="department_id" name="department_id" value="{{$click_department_data[0]->department_id}}">
            <input type="hidden" name="client_id" value="{{ session('client_id') }}">

            @if(substr($click_id,0,2) == "ta")
            <div class="details-area border border-dark bg-info" style="padding:10px;" id="parent">
            @else
            <div class="details-area border border-dark bg-warning" style="padding:10px;" id="parent">
            @endif
                <div class="row">
                    <div class="col-4" id="page_name" style="margin-top:-5px; margin-right:-12px">
                        <h2>部署詳細</2>
                    </div>

                    <div class="col-4" style="margin-right:-10px">
                        <p>
                            <span data-toggle="tooltip" id="id_number" title="番号:{{$click_department_data[0]->department_id}}">部署名</span>
                            <input type="text" name="name" maxlength="32" style="width:140px;"
                            data-toggle="tooltip" title="部署の名称を入力します" @if(!empty(old('name'))) value="{{ old('name') }}" @else value= "{{$click_department_data[0]->name}}"@endif>
                        </p>
                    </div>
  
                    <div class="col" style="margin-top:5px;">
                        <p>上位:@if(isset($click_department_data[0]->high_id))<a href="{{ route('plbs01.show',[session('client_id'),$click_department_data[0]->high_id])}}" data-toggle="tooltip" title="クリックにより、上位部署に遷移します">{{$click_department_data[0]->high_name}}</a>@endif</p>
                    </div>
                </div>

                <div class="row margin-reset">
                    <div class="col-4">
                        <p>管理者番号：<input type="text" id="management_number" name="management_number" maxlength="10" 
                        @if(!empty(old('management_number'))) value="{{ old('management_number') }}" @else value="{{$click_department_data[0]->management_personnel_id}}" @endif 
                        style="width:100px;" data-toggle="tooltip" title="部署情報を修正、抹消できる管理者を変更する場合、ここを修正します 管理者自身とシステム管理者だけが修正できます"></p>
                    </div>
                    <div class="col-3" style="padding:0px">
                        <p>管理者名：<a href="{{ route('plbs01.show',[session('client_id'),$click_department_data[0]->management_personnel_id])}}">{{$click_department_data[0]->management_name}}</a></p>
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

                <div class="row margin-reset">
                    <div class="col">
                        <p>状態:
                        <select name="status" data-toggle="tooltip" title="部署の状態を選択します">
                        @if(!empty(old('status')))
                        <option value="10" @if(old('status') == "10") selected @endif>開設提案</option>
                        <option value="11" @if(old('status') == "11") selected @endif>審査</option>
                        <option value="12" @if(old('status') == "12") selected @endif>開設待</option>
                        <option value="13" @if(old('status') == "13") selected @endif>稼働中</option>
                        <option value="14" @if(old('status') == "14") selected @endif>休止</option>
                        <option value="18" @if(old('status') == "18") selected @endif>廃止</option>
                        @else
                        <option value="10" @if($click_department_data[0]->status == "10") selected @endif>開設提案</option>
                        <option value="11" @if($click_department_data[0]->status == "11") selected @endif>審査</option>
                        <option value="12" @if($click_department_data[0]->status == "12") selected @endif>開設待</option>
                        <option value="13" @if($click_department_data[0]->status == "13") selected @endif>稼働中</option>
                        <option value="14" @if($click_department_data[0]->status == "14") selected @endif>休止</option>
                        <option value="18" @if($click_department_data[0]->status == "18") selected @endif>廃止</option>
                        @endif
                        </select>
                        責任者:
                        <select name="responsible_person_id" style="width:90px; margin:0px;" data-toggle="tooltip" title="部署の責任者を選択します">
                        <option value="{{$click_department_data[0]->responsible_person_id}}">{{$click_department_data[0]->responsible_name}}</option>
                        @for($i = 0;$i < count($personnel_data);$i++)
                                <option value="{{$personnel_data[$i]->personnel_id}}">{{$personnel_data[$i]->name}}</option>
                        @endfor
                        </select>

                        運用開始日<input name="start_day" type="date" style="width:140px; margin:0px;" @if(!empty(old('start_day'))) value="{{ old('start_day')}}" @else value="{{$click_department_data[0]->operation_start_date}}" @endif >
                        運用終了日<input name="finish_day" type="date" style="width:140px; margin:0px;" @if(!empty(old('finish_day'))) value="{{ old('finish_day')}}" @else value="{{$click_department_data[0]->operation_end_date}}" @endif>
                    
                        &emsp;&emsp;

                        <button class="main_button_style" type="button" id="remarks_change_display" onclick="remarksOn()" data-toggle="tooltip" title="クリックにより、備考及び登録日などの情報を開きます">
                            <img class="remarks_button" src="data:image/png;base64,{{Config::get('base64.updown')}}" alt="開閉" >
                        </button>
                        </p>
                    </div>
                </div>

                <input type="hidden" id="remarks" name="remarks" value="{{$click_department_data[0]->remarks}}">

                <div class="row margin-reset" id="remarks-field" style="display:none">
                    <div>
                        備考
                    </div>
                    <div>
                        <textarea id="remarks_set" onchange = "remarks(this value)" maxlength="512" style="width:800px; height: 60px;">{{$click_department_data[0]->remarks}}</textarea>
                    </div>
                </div>

                <div class="row" id="little-information-field" style="display:none">
                    <p>
                    登録日:{{$click_department_data[0]->created_at}} 修正日:{{$click_department_data[0]->updated_at}}
                    登録者:<a href="{{ route('plbs01.show',[session('client_id'),$click_department_data[0]->responsible_person_id])}}">{{$click_department_data[0]->responsible_name}}</a>
                    </p>
                </div>

                <div class="row main_button_display">
                    <div class="col">
                    <p>
                    <div style="display:inline-flex">
                    <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.ok')}}" alt="確定" id="updateOn" onclick="submit();" data-toggle="tooltip" title="クリックにより、登録、更新を確定します" 
                    style="opacity: 0.3;" disabled>
    </form>
    
                    <form action="{{ route('psbs01.index') }}" method="get">
                    @csrf
                    <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.new')}}" alt="新規" onclick="submit();" data-toggle="tooltip" title="本データの下位に新しいデータを追加します">
                    <input type="hidden" id="high_new" name="high" value="{{$click_department_data[0]->department_id}}">
                    </form>

                    @if(substr($click_id,0,2) == "ta")
                    <form action="{{ route('ptcm01.delete',[session('client_id'),$click_id])}}" method="post">
                    @else
                    <form action="{{ route('psbs01.delete',[session('client_id'),$click_department_data[0]->department_id])}}" method="post">
                    @endif
                    @csrf
                    @method('post')
                    <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.delete')}}" alt="削除" onclick="submit();" id="delete" data-toggle="tooltip" title="削除有効化をチェックした状態でのクリックにより、詳細領域のデータを下位ツリーのデータを含めて削除します" disabled>
                    </form>

                    @if(substr($click_id,0,2) == "ta")
                    <form action="{{ route('pa0001.clipboard',$click_id)}}" method="get">
                    @else
                    <form action="{{ route('pa0001.clipboard',$click_department_data[0]->department_id)}}" method="get">
                    @endif
                        @csrf
                        <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.copy')}}" alt="複写" onclick="submit();" id="copyTarget" data-toggle="tooltip" title="クリックにより、詳細領域のデータをクリップボードに複写します">
                    </form>

                    <form action="{{ route('pa0001.deleteclipboard')}}" method="get">
                        @csrf
                        <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.remove')}}" alt="取消" onclick="submit();" data-toggle="tooltip" title="クリップボードに複写した内容を抹消します" @if(null == session()->get('clipboard_id'))) disabled style="opacity:0.3;"@endif>
                    </form>

                    <input type="hidden" id="tree_disabled" value="{{session('client_id')}}">
                    <button class="main_button_style" type="button" id="tree_change_display" data-toggle="tooltip" title="本機能を隠蔽、もしくは隠蔽状態を解除します 隠蔽した機能をツリー画面に表示するためには、ツリー画面で露出をクリックします">
                        <img class="main_button_img" src="data:image/png;base64,{{Config::get('base64.ng')}}" alt="隠蔽/表示" >
                    </button>

                    <form action="{{ route('pa0001.redirect')}}" method="get">
                        <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.road')}}" alt="再表示" onclick="submit();" id="open_tree" data-toggle="tooltip" title="ツリーを再表示します">
                    </form>

                    <button class="main_button_style" type="button" data-toggle="tooltip" title="ツリーを表示します" onclick="displayOn()">
                        <img class="main_button_img" src="data:image/png;base64,{{Config::get('base64.tree')}}" alt="開く" >
                    </button>
                    
                    <input type="checkbox" onclick="deleteOn()" id="check" data-toggle="tooltip" title="チェックを入れることで削除ボタンがクリックできるようになります（削除権限がある場合）">
                    <font size="-2" color="red">削除有効化</font>

                    <input type="checkbox" id="check2" onclick="updateOn()" data-toggle="tooltip" title="チェックを入れることで更新ボタンがクリックできるようになります（権限がある場合）">
                    <font size="-2" color="red">更新有効化</font>
                    </div>
                </div>
            </div>
    </div>
    {{-- 部署の詳細表示　ここまで--}}
    {{-- 人員の詳細表示　--}}
    @else
    <form action="{{ route('psji01.update',session('client_id')) }}" method="post">
            @csrf
            @method('patch')
            <input type="hidden" id="personnel_id" name="personnel_id" value="{{$click_personnel_data[0]->personnel_id}}">
            <input type="hidden" name="client_id" value="{{ session('client_id') }}">

            @if(substr($click_id,0,2) == "ta")
            <div class="details-area border border-dark bg-info" style="padding:10px;" id="parent">
            @else
            <div class="details-area border border-dark bg-warning" style="padding:10px;" id="parent">
            @endif
                <div class="row">
                    <div class="col-4" id="page_name" style="margin-top:-5px; margin-right:-12px">
                        <h2>人員詳細</2>
                    </div>
                    <div class="col-4" style="margin-right:-10px">
                        <p id="palent">
                            <span data-toggle="tooltip" id="id_number" title="番号:{{$click_personnel_data[0]->personnel_id}}">名前</span>
                            <input type="text" name="name" maxlength="32" style="width:140px;" value="{{$click_personnel_data[0]->name}}" data-toggle="tooltip" title="人員の名称を入力します">
                        </p>
                    </div>

                    <div class="col">
                        上位:
                        <a href="{{ route('plbs01.show',[session('client_id'),$click_personnel_data[0]->high_id])}}">{{$click_personnel_data[0]->high_name}}</a>
                    </div>

                    <div class="col" style="padding:0px">
            
                    </div>
                </div>

                <div class="row margin-reset">
                    <div class="col-4">
                        <p>管理者番号：<input type="text" id="management_number" name="management_number" maxlength="10" value="{{$click_personnel_data[0]->management_personnel_id}}" style="width:100px;"
                        data-toggle="tooltip" title="部署情報を修正、抹消できる管理者を変更する場合、ここを修正します 管理者自身とシステム管理者だけが修正できます"></p>
                    </div>
                    <div class="col-3" style="padding:0px">
                        <p>管理者名：<a href="{{ route('plbs01.show',[session('client_id'),$click_personnel_data[0]->management_personnel_id])}}">{{$click_personnel_data[0]->management_name}}</a></p>
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

                <div class="row margin-reset">
                    <div class="col-5">
                        <p>メールアドレス<input type="email" name="email" maxlength="64" value="{{$click_personnel_data[0]->email}}"></p>
                    </div>
                    <div class="col-4" style="padding:0px;margin-left:-68px">
                        <p id="login">パスワード<input id="password" type="password" maxlength="32" name="password">
                    @if($click_personnel_data[0]->login_authority == "1") 
                        <input type="checkbox" onclick="passwordOn()">
                    @endif
                        </p>
                    </div>
                    <div class="col">
                        <button type="submit" formaction="{{route('psji01.send')}}" formmethod="post">メール送信</button>
                    </div>
                </div>

                <div class="row margin-reset">
                    <div class="col">
                        <p>状態:
                        <select name="status" data-toggle="tooltip" title="人員の状態を選択します">
                        <option value="10" @if($click_personnel_data[0]->status == "10") selected @endif>応募</option>
                        <option value="11" @if($click_personnel_data[0]->status == "11") selected @endif>審査</option>
                        <option value="12" @if($click_personnel_data[0]->status == "12") selected @endif>入社待</option>
                        <option value="13" @if($click_personnel_data[0]->status == "13") selected @endif>在職</option>
                        <option value="14" @if($click_personnel_data[0]->status == "14") selected @endif>休職</option>
                        <option value="18" @if($click_personnel_data[0]->status == "18") selected @endif>退職</option>
                        </select>
                        システム管理者:
                        <input type="checkbox" name="system_management" value="1" data-toggle="tooltip" title="人員がシステム管理者かどうかを設定します"
                        @if($click_personnel_data[0]->system_management == "1") checked @endif>
                        ログイン:
                        <input name="login_authority" type="checkbox" value="1" onclick="loginDisabled()" @if($click_personnel_data[0]->login_authority == "1") checked @endif>
                        
                        運用開始日<input name="start_day" type="date" style="width:140px; margin:0px;" value="{{$click_personnel_data[0]->operation_start_date}}">
                        運用終了日<input name="finish_day" type="date" style="width:140px; margin:0px;" value="{{$click_personnel_data[0]->operation_end_date}}">

                        <button class="main_button_style" type="button" id="remarks_change_display" onclick="remarksOn()" data-toggle="tooltip" title="クリックにより、備考及び登録日などの情報を開きます">
                            <img class="remarks_button" src="data:image/png;base64,{{Config::get('base64.updown')}}" alt="開閉" >
                        </button>
                        </p>
                    </div>
                </div>

                <input type="hidden" id="remarks" name="remarks" value="{{$click_personnel_data[0]->remarks}}">

                <div class="row margin-reset" id="remarks-field" style="display:none"">
                    <div>
                        備考
                    </div>
                    <div>
                        <textarea id="remarks_set" onchange = "remarks(this value)" maxlength="512" style="width:800px; height: 60px;">{{$click_personnel_data[0]->remarks}}</textarea>
                    </div>
                </div>

                <div class="row" id="little-information-field" style="display:none">
                    <p>登録日:{{$click_personnel_data[0]->created_at}} 修正日:{{$click_personnel_data[0]->updated_at}}</p>
                </div>

                <div class="row margin-reset">
                    <div class="col">
                        <div style="display:inline-flex">
                        <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.ok')}}" alt="確定" onclick="submit();"
                        id="updateOn" data-toggle="tooltip" title="クリックにより、登録、更新を確定します" style="opacity: 0.3;" disabled>
    </form>
    
                        <form action="{{ route('psji01.index') }}" method="get">
                        @csrf
                        <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.new')}}" alt="新規" onclick="submit();"
                        data-toggle="tooltip" title="本データの下位に新しいデータを追加します">
                        <input type="hidden" id="high_new" name="high" value="{{$click_personnel_data[0]->high_id}}">
                        </form>

                        @if(substr($click_id,0,2) == "ta")
                        <form action="{{ route('ptcm01.delete',[session('client_id'),$click_id])}}" method="post">
                        @else
                        <form action="{{ route('psji01.destroy',[session('client_id'),$click_personnel_data[0]->personnel_id])}}" method="post">
                        @endif
                        @csrf
                        @method('post')
                        <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.delete')}}" alt="削除" onclick="submit();" id="delete" data-toggle="tooltip" 
                        title="削除有効化をチェックした状態でのクリックにより、詳細領域のデータを下位ツリーのデータを含めて削除します" 
                        disabled>
                        </form>

                        @if(substr($click_id,0,2) == "ta")
                        <form action="{{ route('pa0001.clipboard',$click_id)}}" method="get">
                        @else
                        <form action="{{ route('pa0001.clipboard',$click_personnel_data[0]->personnel_id)}}" method="get">
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

                        <form action="{{ route('pa0001.redirect')}}" method="get">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.road')}}" alt="再表示" onclick="submit();" id="open_tree" data-toggle="tooltip" title="ツリーを再表示します">
                        </form>

                        <button class="main_button_style" type="button" data-toggle="tooltip" title="ツリーを表示します" onclick="displayOn()">
                            <img class="main_button_img" src="data:image/png;base64,{{Config::get('base64.tree')}}" alt="開く" >
                        </button>

                        <input type="checkbox" id="check" onclick="deleteOn2()" data-toggle="tooltip" title="チェックを入れることで削除ボタンがクリックできるようになります（削除権限がある場合）">
                        <font size="-2" color="red">削除有効化</font>

                        <input type="checkbox" id="check2" onclick="updateOn()" data-toggle="tooltip" title="チェックを入れることで更新ボタンがクリックできるようになります（権限がある場合）">
                        <font size="-2" color="red">更新有効化</font>
                        </div>
                    </div>
                </div>
            </div>
    {{-- 人員の詳細表示　ここまで--}}
    @endif
        <div id="list-open-button" style="display:none;" onclick="listOn()">
            <p style="text-align:center; cursor: hand; cursor:pointer; background:#99CCFF; border:solid 1px;">↓</p>
        </div>
        <div class="list-area" id="list">
            <div class="department-area margin-reset">
                <div class="row">
                    {{-- ツリー操作機能　--}}
                    <div class="col-4" style="display:inline-flex; padding-top:15px;">
                        <p>@if(session('click_code') == "bs")配下@else所属@endif部署</p>
                        <form action="{{ route('psbs01.index') }}" method="get">
                        @if(session('click_code') == "bs")
                        <input type="hidden" id="high" name="high" value="{{$click_department_data[0]->department_id}}">
                        @else
                        <input type="hidden" id="high" name="high" value="{{$click_personnel_data[0]->high_id}}">
                        @endif
                        
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、詳細情報に属する下位情報を新規登録する詳細画面に遷移します">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.new')}}" alt="新規">
                        </button>
                        </form>

                        <form action="{{ route('psbs01.hierarchyUpdate',[session('client_id')]) }}" method="post">
                        @if(session('click_code') == "bs")
                        <input type="hidden" id="high_move" name="high_id" value="{{$click_department_data[0]->department_id}}">
                        @else
                        <input type="hidden" id="high_move" name="high_id" value="{{$click_personnel_data[0]->high_id}}">
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
                        @if(session('click_code') == "bs")
                        <input type="hidden" id="high_insert" name="high_id" value="{{$click_department_data[0]->department_id}}">
                        @else
                        <input type="hidden" id="high_insert" name="high_id" value="{{$click_personnel_data[0]->high_id}}">
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
                        @if(session('click_code') == "bs")
                        <input type="hidden" id="high_projection" name="high_id" value="{{$click_department_data[0]->department_id}}">
                        @else
                        <input type="hidden" id="high_projection" name="high_id" value="{{$click_personnel_data[0]->high_id}}">
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
                                    <a class="page-link" href="{{ route('psbs01.search_get',['department_page'=>1,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$click_id,'search'=>$_POST['search']])}}" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <li class="page-item">
    @if($count_department == 1)
                                <a class="page-link" href="{{ route('psbs01.search_get',['department_page'=>$count_department,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$click_id,'search'=>$_POST['search']])}}" aria-label="Previous">
    @else
                                <a class="page-link" href="{{ route('psbs01.search_get',['department_page'=>$count_department-1,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$click_id,'search'=>$_POST['search']])}}" aria-label="Previous">
    @endif
                                    <span aria-hidden="true">&lt;</span>
                                </a>
                                </li>
                                @if($departments['count'] == 0)
                                0/0&nbsp;&nbsp;0件
                                @else
                                {{$count_department}}/{{$departments['max']}}&nbsp;&nbsp;{{$departments['count']}}件
                                @endif
                                <li class="page-item">
    @if($count_department<$departments['max'])
                                    <a class="page-link" href="{{ route('psbs01.search_get',['department_page'=>$count_department+1,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$click_id,'search'=>$_POST['search']]) }}" aria-label="Next">
    @else
                                    <a class="page-link" href="{{ route('psbs01.search_get',['department_page'=>$departments['max'],'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$click_id,'search'=>$_POST['search']]) }}" aria-label="Next">
    @endif
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="{{ route('psbs01.search_get',['department_page'=>$departments['max'],'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$click_id,'search'=>$_POST['search']]) }}" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
@elseif(!empty($_POST['search2']))
                        <nav aria-label="Page navigation example">
                            <ul class="pagination pagination-sm">
                                <li class="page-item">
                                    <a class="page-link" href="{{ route('psji01.search_get',['department_page'=>1,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$click_id,'search2'=>$_POST['search2']])}}" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <li class="page-item">
    @if($count_department == 1)
                                <a class="page-link" href="{{ route('psji01.search_get',['department_page'=>$count_department,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$click_id,'search2'=>$_POST['search2']])}}" aria-label="Previous">
    @else
                                <a class="page-link" href="{{ route('psji01.search_get',['department_page'=>$count_department-1,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$click_id,'search2'=>$_POST['search2']])}}" aria-label="Previous">
    @endif
                                    <span aria-hidden="true">&lt;</span>
                                </a>
                                </li>
                                @if($departments['count'] == 0)
                                0/0&nbsp;&nbsp;0件
                                @else
                                {{$count_department}}/{{$departments['max']}}&nbsp;&nbsp;{{$departments['count']}}件
                                @endif
                                <li class="page-item">
    @if($count_department<$departments['max'])
                                    <a class="page-link" href="{{ route('psji01.search_get',['department_page'=>$count_department+1,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$click_id,'search2'=>$_POST['search2']]) }}" aria-label="Next">
    @else
                                    <a class="page-link" href="{{ route('psji01.search_get',['department_page'=>$departments['max'],'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$click_id,'search2'=>$_POST['search2']]) }}" aria-label="Next">
    @endif
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="{{ route('psji01.search_get',['department_page'=>$departments['max'],'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$click_id,'search2'=>$_POST['search2']]) }}" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
@else
                        <nav aria-label="Page navigation example">
                            <ul class="pagination pagination-sm">
                                <li class="page-item">
                                    <a class="page-link" href="{{ route('plbs01.show',['department_page'=>1,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$click_id]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <li class="page-item">
    @if($count_department == 1)
                                <a class="page-link" href="{{ route('plbs01.show',['department_page'=>$count_department,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$click_id]) }}" aria-label="Previous">
    @else
                                <a class="page-link" href="{{ route('plbs01.show',['department_page'=>$count_department-1,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$click_id]) }}" aria-label="Previous">
    @endif
                                    <span aria-hidden="true">&lt;</span>
                                </a>
                                </li>
                                @if($departments['count'] == 0)
                                0/0&nbsp;&nbsp;0件
                                @else
                                {{$count_department}}/{{$departments['max']}}&nbsp;&nbsp;{{$departments['count']}}件
                                @endif
                                <li class="page-item">
    @if($count_department<$departments['max'])
                                    <a class="page-link" href="{{ route('plbs01.show',['department_page'=>$count_department+1,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$click_id]) }}" aria-label="Next">
    @else
                                    <a class="page-link" href="{{ route('plbs01.show',['department_page'=>$departments['max'],'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$click_id]) }}" aria-label="Next">
    @endif
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="{{ route('index',['department_page'=>$departments['max'],'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$click_id]) }}" aria-label="Next">
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
                            <form action="{{ route('psbs01.search',[session('client_id'),$click_id])}}" method="post">
                        @else
                            <form action="{{ route('psbs01.search',[session('client_id'),$click_id])}}" method="post">
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
                </div>

                <div class="row margin-reset">
                    <div class="col">
                        <div class="border border-dark">    
                            <table id="bs-table" class="bs-table table_sticky table table-striped sort-table" style="margin-bottom:0px;margin-top:0px;">
                                <thead>
                                    <tr>
                                    <th >部署番号</th>
                                    <th width="160">部署名</th>
                                    <th width="160">上位部署</th>
                                    <th width="90">状態</th>
                                    <th width="120">責任者</th>
                                    <th width="190">操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($departments['data'] as $department['data'])
                                    <tr>
                                    <td width="100">{{$department['data']->department_id}}</td>
                                @if($department['data']->operation_start_date > \Carbon\Carbon::today()->format('Y-m-d') || (!(null == $department['data']->operation_end_date) && \Carbon\Carbon::today()->format('Y-m-d') > $department['data']->operation_end_date))
                                    <td width="160"><s><a href="{{ route('plbs01.show',[session('client_id'),$department['data']->department_id])}}" data-toggle="tooltip" title="運用中ではありません">{{$department['data']->name}}</a></s></td>
                                @else
                                    <td width="160"><a href="{{ route('plbs01.show',[session('client_id'),$department['data']->department_id])}}" data-toggle="tooltip" title="クリックにより、当該部署に遷移します">{{$department['data']->name}}</a></td>
                                @endif
                                    <td width="160">@if(!(empty($department['data']->high_id)))<a href="{{ route('plbs01.show',[session('client_id'),$department['data']->high_id])}}" data-toggle="tooltip" title="クリックにより、上位部署に遷移します">{{$department['data']->high_name}}</a>@endif</td>
                                    <td width="90">
                                @switch($department['data']->status)
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
                                    <td width="120"><a href="{{ route('plbs01.show',[session('client_id'),$department['data']->responsible_person_id])}}" data-toggle="tooltip" title="クリックにより、責任者の人員詳細に遷移します">{{$department['data']->responsible_name}}</a></td>
                                    <td width="190">
                                    【<a href="{{ route('pa0001.clipboard',$department['data']->department_id)}}">複写</a>】
                                    【<p id="bs_list_delete{{$loop->index}}" name="bs_delete" style="pointer-events: none; display:inline-block; text-decoration:underline; margin:0px;" onclick="event.preventDefault(); document.getElementById('bs_delete{{$loop->index}}').submit();">削除</p>】
                                    <form id="bs_delete{{$loop->index}}" action="{{ route('psbs01.delete',[session('client_id'),$department['data']->department_id])}}" method="post" style="display: none;">
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

@if( empty(session('click_code')) or session('click_code') == "bs")
            <div class="personnel-area" style="padding-top:5px">
                <div class="row">
                    {{-- ツリー操作機能　--}}
                    <div class="col-4" style="display:inline-flex">
                        <p>所属人員</p>
                        <form action="{{ route('psji01.index') }}" method="get">
                        <input type="hidden" id="ji_high_new" name="high" value="{{$click_department_data[0]->department_id}}">
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、詳細情報に属する下位情報を新規登録する詳細画面に遷移します">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.new')}}" alt="新規">
                        </button>
                        </form>

                        <form action="{{ route('psbs01.hierarchyUpdate',[session('client_id')]) }}" method="post">
                        @csrf
                        @method('patch')
                        <input type="hidden" id="ji_high_move" name="high_id" value="{{$click_department_data[0]->department_id}}">
                        <input type="hidden" id="ji_lower_move" name="lower_id" value="{{session('clipboard_id')}}"> 
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧に移動します 移動元からは抹消されます">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.move')}}" alt="移動">
                        </button>
                        </form>

                        <form action="{{ route('psji01.copy') }}" method="post">
                        @csrf
                        @method('post')
                        <input type="hidden" name="client_id" value="{{ session('client_id') }}">
                        <input type="hidden" name="high_id" value="{{$click_department_data[0]->department_id}}">
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
                        <input type="hidden" id="ji_high_projection" name="high_id" value="{{$click_department_data[0]->department_id}}">
                        <button class="main_button_style" data-toggle="tooltip" title="クリックにより、クリップボードにコピーした情報を、一覧にショートカットして投影します 移動元は消えません">
                            <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.ji')}}" alt="投影">
                        </button>
                        </form>
                    </div>
                    {{-- ツリー操作機能ここまで　--}}

                    {{-- ページネーション--}}
                    <div class="col-3">
@if(!empty($_POST['search']))
                        <nav aria-label="Page navigation example">
                            <ul class="pagination pagination-sm">
                                <li class="page-item">
                                    <a class="page-link" href="{{ route('psbs01.search_get',['department_page'=>$count_department,'personnel_page'=>1,'id'=>session('client_id'),'id2'=>$click_id,'search'=>$_POST['search']]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <li class="page-item">
    @if($count_personnel == 1)
                                <a class="page-link" href="{{ route('psbs01.search_get',['department_page'=>$count_department,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$click_id,'search'=>$_POST['search']]) }}" aria-label="Previous">
    @else
                                <a class="page-link" href="{{ route('psbs01.search_get',['department_page'=>$count_department,'personnel_page'=>$count_personnel-1,'id'=>session('client_id'),'id2'=>$click_id,'search'=>$_POST['search']]) }}" aria-label="Previous">
    @endif
                                    <span aria-hidden="true">&lt;</span>
                                </a>
                                </li>
                                @if($names['count'] == 0)
                                0/0&nbsp;&nbsp;0件
                                @else
                                {{$count_personnel}}/{{$names['max']}}&nbsp;&nbsp;{{$names['count']}}件
                                @endif
                                <li class="page-item">
    @if($count_personnel<$names['max'])
                                    <a class="page-link" href="{{ route('psbs01.search_get',['department_page'=>$count_department,'personnel_page'=>$count_personnel+1,'id'=>session('client_id'),'id2'=>$click_id,'search'=>$_POST['search']]) }}" aria-label="Next">
    @else
                                    <a class="page-link" href="{{ route('psbs01.search_get',['department_page'=>$count_department,'personnel_page'=>$names['max'],'id'=>session('client_id'),'id2'=>$click_id,'search'=>$_POST['search']]) }}" aria-label="Next">
    @endif
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="{{ route('psbs01.search_get',['department_page'=>$count_department,'personnel_page'=>$names['max'],'id'=>session('client_id'),'id2'=>$click_id,'search'=>$_POST['search']]) }}" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
@elseif(!empty($_POST['search2']))
                        <nav aria-label="Page navigation example">
                            <ul class="pagination pagination-sm">
                                <li class="page-item">
                                    <a class="page-link" href="{{ route('psji01.search_get',['department_page'=>$count_department,'personnel_page'=>1,'id'=>session('client_id'),'id2'=>$click_id,'search2'=>$_POST['search2']]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <li class="page-item">
    @if($count_personnel == 1)
                                <a class="page-link" href="{{ route('psji01.search_get',['department_page'=>$count_department,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$click_id,'search2'=>$_POST['search2']]) }}" aria-label="Previous">
    @else
                                <a class="page-link" href="{{ route('psji01.search_get',['department_page'=>$count_department,'personnel_page'=>$count_personnel-1,'id'=>session('client_id'),'id2'=>$click_id,'search2'=>$_POST['search2']]) }}" aria-label="Previous">
    @endif
                                    <span aria-hidden="true">&lt;</span>
                                </a>
                                </li>
                                @if($names['count'] == 0)
                                0/0&nbsp;&nbsp;0件
                                @else
                                {{$count_personnel}}/{{$names['max']}}&nbsp;&nbsp;{{$names['count']}}件
                                @endif
                                <li class="page-item">
    @if($count_personnel<$names['max'])
                                    <a class="page-link" href="{{ route('psji01.search_get',['department_page'=>$count_department,'personnel_page'=>$count_personnel+1,'id'=>session('client_id'),'id2'=>$click_id,'search2'=>$_POST['search2']]) }}" aria-label="Next">
    @else
                                    <a class="page-link" href="{{ route('psji01.search_get',['department_page'=>$count_department,'personnel_page'=>$names['max'],'id'=>session('client_id'),'id2'=>$click_id,'search2'=>$_POST['search2']]) }}" aria-label="Next">
    @endif
                                        <span aria-hidden="true">&gt;</span>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="{{ route('psji01.search_get',['department_page'=>$count_department,'personnel_page'=>$names['max'],'id'=>session('client_id'),'id2'=>$click_id,'search2'=>$_POST['search2']]) }}" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
@elseif(isset($select_id))
                        <nav aria-label="Page navigation example">
                            <ul class="pagination pagination-sm">
                                <li class="page-item">
                                    <a class="page-link" href="{{ route('plbs01.show',['department_page'=>$count_department,'personnel_page'=>1,'id'=>session('client_id'),'id2'=>$click_id]) }}" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <li class="page-item">
    @if($count_personnel == 1)
                                <a class="page-link" href="{{ route('plbs01.show',['department_page'=>$count_department,'personnel_page'=>$count_personnel,'id'=>session('client_id'),'id2'=>$click_id]) }}" aria-label="Previous">
    @else
                                <a class="page-link" href="{{ route('plbs01.show',['department_page'=>$count_department,'personnel_page'=>$count_personnel-1,'id'=>session('client_id'),'id2'=>$click_id]) }}" aria-label="Previous">
    @endif
                                    <span aria-hidden="true">&lt;</span>
                                </a>
                                </li>
                                @if($names['max'] == 0)
                                0/0&nbsp;&nbsp;0件
                                @else
                                {{$count_personnel}}/{{$names['max']}}&nbsp;&nbsp;{{$names['count']}}件
                                @endif
                                <li class="page-item">
    @if($count_personnel<$names['max'])
                                <a class="page-link" href="{{ route('plbs01.show',['department_page'=>$count_department,'personnel_page'=>$count_personnel+1,'id'=>session('client_id'),'id2'=>$click_id]) }}" aria-label="Next">
    @else
                                <a class="page-link" href="{{ route('plbs01.show',['department_page'=>$count_department,'personnel_page'=>$names['max'],'id'=>session('client_id'),'id2'=>$click_id]) }}" aria-label="Next">
    @endif
                                    <span aria-hidden="true">&gt;</span>
                                </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="{{ route('plbs01.show',['department_page'=>$count_department,'personnel_page'=>$names['max'],'id'=>session('client_id'),'id2'=>$click_id]) }}" aria-label="Next">
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
                                {{$count_personnel}}/{{$names['max']}}&nbsp;&nbsp;{{$names['count']}}件
                                <li class="page-item">
    @if($count_personnel<$names['max'])
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

                    {{-- 検索機能　--}}
                    <div class="col-4" style="display:inline-flex">
                        <p>氏名</p>
                        @if(session('click_code') == "bs")
                            <form action="{{ route('psji01.search',[session('client_id'),$click_id])}}" method="post">
                        @else
                            <form action="{{ route('psji01.search',[session('client_id'),$click_id])}}" method="post">
                        @endif
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
                    {{-- 検索機能ここまで　--}}
                </div>
                
                <div class="row margin-reset">
                    <div class="col">
                        <div class="border border-dark">
                            <table id="ji-table" class="ji-table table_sticky table table-striped" style="margin-bottom:0px;margin-top:0px;">
                                <thead>
                                    <tr>
                                    <th width="102">人員番号</th>
                                    <th width="100">氏名</th>
                                    <th width="130">所属部署</th>
                                    <th width="80">状態</th>
                                    <th width="60">ID</th>
                                    <th width="100">PW更新</th>
                                    <th width="80">権限</th>
                                    <th width="160">操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($names['data'] as $name['data'])
                                    <tr>
                                    <td width="100">{{ $name['data']->personnel_id}}</td>
                                    @if($name['data']->operation_start_date > \Carbon\Carbon::today()->format('Y-m-d') || (!(null == $name['data']->operation_end_date) && \Carbon\Carbon::today()->format('Y-m-d') > $name['data']->operation_end_date))
                                    <td width="100"><s><a href="{{ route('plbs01.show',[session('client_id'),$name['data']->personnel_id])}}" data-toggle="tooltip" title="運用中ではありません">{{$name['data']->name}}</a> </s> </td>
                                    @else
                                    <td width="100"><a href="{{ route('plbs01.show',[session('client_id'),$name['data']->personnel_id])}}" data-toggle="tooltip" title="クリックにより、当該人員に遷移します">{{$name['data']->name}}</a></td>
                                    @endif 
                                    <td width="130"><a href="{{ route('plbs01.show',[session('client_id'),$name['data']->high_id])}}" data-toggle="tooltip" title="クリックにより、所属部署に遷移します">{{$name['data']->high_name}}</a>
                                    <td width="80">
                                    @switch($name['data']->status)
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
                                    <td width="60">aaa02</td>
                                    <td width="100">{{$name['data']->password_update_day}}</td>
                                    <td width="80">---------</td>
                                    <td width="160">【<a href="{{ route('pa0001.clipboard',$name['data']->personnel_id)}}">複写</a>】
                                    【<p id="list_delete{{$loop->index}}" name="bs_delete" style="pointer-events: none; display:inline-block; text-decoration:underline; margin:0px;" onclick="event.preventDefault(); document.getElementById('delete{{$loop->index}}').submit();">削除</p>】
                                    <form id="delete{{$loop->index}}" action="{{ route('psji01.destroy',[session('client_id'),$name['data']->personnel_id])}}" method="post" style="display: none;">
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
    </div>
@endif
    {{-- コメント　詳細画面ここまで --}}

@endsection
