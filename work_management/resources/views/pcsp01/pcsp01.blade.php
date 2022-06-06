<!-- スタンプ -->
<div id="stamp-area" data-board ="{{$board_details[0]->board_id}}" data-client ="{{session('client_id')}}" data-personnel ="ji00000001" class="stamp-area" style="display:none">
<p>

@foreach($stamp_lists as $stamp_list)
<button class="main_button_style" type="button" data-toggle="tooltip" title="スタンプでリアクションする" data-stamp="{{$loop->index+1}}" onclick="stampClick(event)">
  <img class="remarks_button" src="data:image/png;base64,{{Config::get($stamp_list)}}" alt="スタンプ">
</button>
@endforeach
</p>
</div>