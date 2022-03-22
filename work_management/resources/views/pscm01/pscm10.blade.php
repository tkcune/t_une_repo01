<div class="row margin-reset">
    <div style="padding:0px">
        <p>管理者名：<a href="{{ route('plbs01.show',[session('client_id'),$click_personnel_data->management_personnel_id])}}">{{$click_management_lists[0]}}</a></p>
    </div>
    <div class="row">
        <div class="col">
            <div>管理者番号：</div>
            <div>
                <input type="text" id="management_number" name="management_number" maxlength="10" value="{{$click_personnel_data->management_personnel_id}}" style="width:100px;"
                data-toggle="tooltip" title="部署情報を修正、抹消できる管理者を変更する場合、ここを修正します 管理者自身とシステム管理者だけが修正できます"></p>
            </div>
        </div>
        <div class="col" style="padding:0px">
            <div>管理者検索：</div>
            <div>
                <input type="search" id="search-list" list="keywords" style="width:150px;" autocomplete="on" maxlength="32"
                        
                data-toggle="tooltip" title="入力に該当した人員の候補を一覧に表示します。表示された人員を選択した場合、その番号が管理者人員番号に表示されます。">
                <datalist id="keywords">
                        
@for($j = 0; $j < count($all_personnel_data);$j++)
@if($all_personnel_data[$j]->system_management == 1)
                    <option value="{{$all_personnel_data[$j]->name}}" label="{{$all_personnel_data[$j]->personnel_id}}"></option>
@endif
@endfor
                </datalist>
            </div>
        </div>
    </div>
</div>