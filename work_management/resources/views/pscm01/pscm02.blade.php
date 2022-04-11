<div class="row margin-reset">
    <div class="col">
        <p>状態:
            <select name="status" data-toggle="tooltip" title="人員の状態を選択します">
                <option value="10" @if($click_personnel_data->status == "10") selected @endif>応募</option>
                <option value="11" @if($click_personnel_data->status == "11") selected @endif>審査</option>
                <option value="12" @if($click_personnel_data->status == "12") selected @endif>入社待</option>
                <option value="13" @if($click_personnel_data->status == "13") selected @endif>在職</option>
                <option value="14" @if($click_personnel_data->status == "14") selected @endif>休職</option>
                <option value="18" @if($click_personnel_data->status == "18") selected @endif>退職</option>
            </select>
            <custom-tooltip title="人員の状態を選択します"></custom-tooltip>
            システム管理者:
            <input type="checkbox" name="system_management" value="1" data-toggle="tooltip" title="人員がシステム管理者かどうかを設定します"
            @if($click_personnel_data->system_management == "1") checked @endif>
            <custom-tooltip title="人員がシステム管理者かどうかを設定します"></custom-tooltip>
            ログイン:
            <input name="login_authority" type="checkbox" value="1" onclick="loginDisabled()" @if($click_personnel_data->login_authority == "1") checked @endif>
                        
            運用開始日<input name="start_day" type="date" style="margin:0px;" value="{{$click_personnel_data->operation_start_date}}">
            運用終了日<input name="finish_day" type="date" style="margin:0px;" value="{{$click_personnel_data->operation_end_date}}">

            <button class="main_button_style" type="button" id="remarks_change_display" onclick="remarksOn()" data-toggle="tooltip" title="クリックにより、備考及び登録日などの情報を開きます">
                <img class="remarks_button" src="data:image/png;base64,{{Config::get('base64.updown')}}" alt="開閉" >
            </button>
            <custom-tooltip title="備考及び登録日などの情報を開きます"></custom-tooltip>
        </p>
    </div>
</div>

<input type="hidden" id="remarks" name="remarks" value="{{$click_personnel_data->remarks}}">
