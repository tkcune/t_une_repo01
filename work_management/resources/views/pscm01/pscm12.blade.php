<div class="row margin-reset" id="remarks-field" style="display:none"">
    <div>
        備考
    </div>
    <div>
        <textarea id="remarks_set" onchange = "remarks(this value)" maxlength="512" style="width:800px; height: 60px;">{{$click_personnel_data->remarks}}</textarea>
    </div>
    </div>

<div class="row" id="little-information-field" style="display:none">
    <p>登録日:{{$click_personnel_data->created_at}} 修正日:{{$click_personnel_data->updated_at}}</p>
</div>