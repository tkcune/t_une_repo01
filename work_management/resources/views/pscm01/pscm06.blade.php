<div class="row">
    <div class="col-4" id="page_name">
        <h2>部署詳細</2>
    </div>
    <div id="name_value" class="col-4">
        <p>
            <span data-toggle="tooltip" id="id_number" title="番号:{{$click_department_data->department_id}}">部署名</span>
            <input type="text" name="name" maxlength="32" style="width:140px;"
            data-toggle="tooltip" title="部署の名称を入力します" @if(!empty(old('name'))) value="{{ old('name') }}" @else value= "{{$click_department_data->name}}"@endif>
        </p>
    </div>
    @if(isset($click_department_high_name))
        <div class="col" style="margin-top:5px;">
            <p>上位:<a href="{{ route('plbs01.show',[session('client_id'),$click_department_data->high_id])}}" data-toggle="tooltip" title="クリックにより、上位部署に遷移します">{{$click_department_high_name}}</a></p>
        </div>
    @endif
</div>