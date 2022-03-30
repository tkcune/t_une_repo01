<div class="margin-reset row" id="management_line">
    <div class="col-4">
        <p>管理者番号：<input type="text" id="management_number" name="management_number" maxlength="10" 
        @if(!empty(old('management_number'))) value="{{ old('management_number') }}" @else value="{{$click_department_data->management_personnel_id}}" @endif 
        style="width:100px;" data-toggle="tooltip" title="部署情報を修正、抹消できる管理者を変更する場合、ここを修正します 管理者自身とシステム管理者だけが修正できます"></p>
    </div>
    <div class="col-3" style="padding:0px">
        <p>管理者名：<a href="{{ route('plbs01.show',[session('client_id'),$click_department_data->management_personnel_id])}}">{{$click_management_lists[0]}}</a></p>
    </div>
    <div class="col" style="padding:0px">
        <p>管理者検索：
            <input type="search" id="search-list" list="keywords" style="width: auto;" autocomplete="on" maxlength="32"
            data-toggle="tooltip" title="入力に該当した人員の候補を一覧に表示します。表示された人員を選択した場合、その番号が管理者人員番号に表示されます。">
            <datalist id="keywords">
@for($j = 0; $j < count($all_personnel_data);$j++)
@if($all_personnel_data[$j]->system_management == 1)
                <option value="{{$all_personnel_data[$j]->name}}" label="{{$all_personnel_data[$j]->personnel_id}}"></option>
@endif
@endfor
            </datalist>
        </p>
    </div>
</div>