<!-- スタンプ -->
<div id="stamp-area" data-board ="{{$board_details[0]->board_id}}" data-client ="{{session('client_id')}}" data-personnel ="ji00000001" class="stamp-area" style="display:none">
<p>
<button class="main_button_style" type="button" data-toggle="tooltip" title="スタンプでリアクションする" data-stamp="1" onclick="stampClick(event)">
  <img class="remarks_button" src="data:image/png;base64,{{Config::get('base64.yes')}}" alt="スタンプ">
</button>

<button class="main_button_style" type="button" data-toggle="tooltip" title="スタンプでリアクションする" data-stamp="2" onclick="stampClick(event)">
  <img class="remarks_button" src="data:image/png;base64,{{Config::get('base64.no')}}" alt="スタンプ">
</button>

<button class="main_button_style" type="button" data-toggle="tooltip" title="スタンプでリアクションする" data-stamp="3" onclick="stampClick(event)">
  <img class="remarks_button" src="data:image/png;base64,{{Config::get('base64.circle')}}" alt="スタンプ">
</button>

<button class="main_button_style" type="button" data-toggle="tooltip" title="スタンプでリアクションする" data-stamp="4" onclick="stampClick(event)">
  <img class="remarks_button" src="data:image/png;base64,{{Config::get('base64.cross')}}" alt="スタンプ">
</button>

<button class="main_button_style" type="button" data-toggle="tooltip" title="スタンプでリアクションする"  data-stamp="5" onclick="stampClick(event)">
  <img class="remarks_button" src="data:image/png;base64,{{Config::get('base64.question')}}" alt="スタンプ">
  </button>
  
<button class="main_button_style" type="button" data-toggle="tooltip" title="スタンプでリアクションする"  data-stamp="6" onclick="stampClick(event)">
  <img class="remarks_button" src="data:image/png;base64,{{Config::get('base64.reference')}}" alt="スタンプ">
</button>
</p>
</div>