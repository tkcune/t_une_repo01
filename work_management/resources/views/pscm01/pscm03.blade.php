<div style="display: flex;">
    <div>
        状態:
    </div>
    <div>
        <select name="status" data-toggle="tooltip" title="部署の状態を選択します">
            @if(!empty(old('status')))
            <option value="10" @if(old('status') == "10") selected @endif>開設提案</option>
            <option value="11" @if(old('status') == "11") selected @endif>審査</option>
            <option value="12" @if(old('status') == "12") selected @endif>開設待</option>
            <option value="13" @if(old('status') == "13") selected @endif>稼働中</option>
            <option value="14" @if(old('status') == "14") selected @endif>休止</option>
            <option value="18" @if(old('status') == "18") selected @endif>廃止</option>
            @else
            <option value="10" @if($department_details_object->status == "10") selected @endif>開設提案</option>
            <option value="11" @if($department_details_object->status == "11") selected @endif>審査</option>
            <option value="12" @if($department_details_object->status == "12") selected @endif>開設待</option>
            <option value="13" @if($department_details_object->status == "13") selected @endif>稼働中</option>
            <option value="14" @if($department_details_object->status == "14") selected @endif>休止</option>
            <option value="18" @if($department_details_object->status == "18") selected @endif>廃止</option>
            @endif
        </select>
    </div>
    <div>
        責任者:
    </div>
    <div>
        <select name="responsible_person_id" style="width:90px; margin:0px;" data-toggle="tooltip" title="部署の責任者を選択します">
            <option value="{{$department_details_object->responsible_person_id}}">{{$top_responsible[0]}}</option>
                       
            @for($i = 0;$i < count($personnel_data);$i++)
                @if($personnel_data[$i]->high_id == $department_details_object->department_id)
                    <option value="{{$personnel_data[$i]->personnel_id}}" >{{$personnel_data[$i]->name}}</option>
                @endif
            @endfor
        </select>
    </div>
    <div>
        運用開始日<input name="start_day" type="date" style="width:140px; margin:0px;" @if(!empty(old('start_day'))) value="{{ old('start_day')}}" @else value="{{$department_details_object->operation_start_date}}" @endif>
    </div>
    <div>
        運用終了日<input name="finish_day" type="date" style="width:140px; margin:0px;" @if(!empty(old('finish_day'))) value="{{ old('finish_day')}}" @else value="{{$department_details_object->operation_end_date}}" @endif>
    </div>

    &emsp;&emsp;

    <div>        
        <button class="main_button_style" type="button" id="remarks_change_display" onclick="remarksOn()" data-toggle="tooltip" title="クリックにより、備考及び登録日などの情報を開きます">
            <img class="remarks_button" src="data:image/png;base64,{{Config::get('base64.updown')}}" alt="開閉" >
        </button>
    </div>
</div>