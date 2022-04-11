<div class="row">
    <div class="col-4" id="page_name" style="margin-top:-5px; margin-right:-12px">
        <h2>人員詳細</2>
    </div>
    <div class="col-4" style="margin-right:-10px">
        <p id="palent">
            <span data-toggle="tooltip" id="id_number" title="番号:{{$click_personnel_data->personnel_id}}">名前</span>
            <input type="text" name="name" maxlength="32" style="width:140px;" value="{{$click_personnel_data->name}}" data-toggle="tooltip" title="人員の名称を入力します">
            <custom-tooltip title="人員の名称を入力します"></custom-tooltip>
        </p>
    </div>

    <div class="col">
        上位:
        <a href="{{ route('plbs01.show',[session('client_id'),$click_personnel_data->high_id])}}">{{$click_personnel_data->high_name}}</a>
    </div>
</div>