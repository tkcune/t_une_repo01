<div class="row margin-reset">
    <div class="col-4">
        <p>管理者番号：<input type="text" id="management_number" name="management_number" maxlength="10" value="{{$click_personnel_data->management_personnel_id}}" style="width:100px;"
        data-toggle="tooltip" title="部署情報を修正、抹消できる管理者を変更する場合、ここを修正します 管理者自身とシステム管理者だけが修正できます">
        <custom-tooltip title="部署情報を修正、抹消できる管理者を変更する場合ここを修正します。
        管理者自身とシステム管理者だけが修正できます"></custom-tooltip></p>
    </div>
    <div class="col-3" style="padding:0px">
        <p>管理者名：<a href="{{ route('plbs01.show',[session('client_id'),$click_personnel_data->management_personnel_id])}}">{{$click_personnel_data->management_name}}</a></p>
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
            <custom-tooltip title="入力に該当した人員の候補を一覧に表示します。
            表示された人員を選択した場合その番号が管理者人員番号に表示されます"></custom-tooltip>
        </p>
    </div>
</div>