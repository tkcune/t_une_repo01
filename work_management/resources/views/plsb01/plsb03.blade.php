<!-- 作業場所 抹消・隠蔽・再表示・更新、削除有効化 -->
<!-- 抹消 -->
<form action="{{ route('pa0001.deleteclipboard')}}" method="get">
    @csrf
    <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.remove')}}" alt="取消" onclick="submit();" data-toggle="tooltip" title="クリップボードに複写した内容を抹消します" @if(null==session()->get('clipboard_id'))) disabled style="opacity:0.3;"@endif>
</form>

<!-- 隠蔽 -->
<input type="hidden" id="tree_disabled" value="{{session('client_id')}}">
<button class="main_button_style" type="button" id="tree_change_display" data-toggle="tooltip" title="本機能を隠蔽、もしくは隠蔽状態を解除します 隠蔽した機能をツリー画面に表示するためには、ツリー画面で露出をクリックします">
    <img class="main_button_img" src="data:image/png;base64,{{Config::get('base64.ng')}}" alt="隠蔽/表示">
</button>

<!-- ツリーの再表示 -->
<form action="{{ route('pa0001.redirect')}}" method="get">
    <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.road')}}" alt="再表示" onclick="submit();" data-toggle="tooltip" title="ツリーを再表示します">
</form>

<!-- ツリーの表示 -->
<button class="main_button_style" type="button" data-toggle="tooltip" title="ツリーを表示します" onclick="displayOn()">
    <img class="main_button_img" src="data:image/png;base64,{{Config::get('base64.tree')}} alt=" 開く">
</button>

<!-- 削除有効化 -->
<input type="checkbox" id="check" onclick="deleteOn5()" data-toggle="tooltip" title="チェックを入れることで削除ボタンがクリックできるようになります（削除権限がある場合）">
<font size="-2" color="red">削除有効化</font>

<!-- 更新有効化 -->
<input type="checkbox" id="check2" onclick="updateOn()" data-toggle="tooltip" title="チェックを入れることで更新ボタンがクリックできるようになります（権限がある場合）">
<font size="-2" color="red">更新有効化</font>

