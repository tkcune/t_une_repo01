<input type="hidden" id="remarks" name="remarks" value="{{$click_department_data->remarks}}">

<div class="row margin-reset" id="remarks-field" style="display:none">
    <div>
        備考
    </div>
    <div>
        <textarea id="remarks_set" onchange = "remarks(this value)" maxlength="512" style="width: 100%; height: 60px;">{{$click_department_data->remarks}}</textarea>
    </div>
</div>

<div class="row" id="little-information-field" style="display:none">
    <p>
        登録日:{{$click_department_data->created_at}} 修正日:{{$click_department_data->updated_at}}
        登録者:<a href="{{ route('plbs01.show',[session('client_id'),$click_department_data->responsible_person_id])}}">{{$click_department_data->responsible}}</a>
    </p>
</div>