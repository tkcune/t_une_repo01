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