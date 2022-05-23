<!-- 取消・隠蔽・再表示・更新、削除有効化 -->
<form action="#" method="get">
    @csrf
    <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.remove')}}" alt="取消" onclick="submit();" data-toggle="tooltip" title="クリップボードに複写した内容を抹消します" @if(null==session()->get('clipboard_id'))) disabled style="opacity:0.3;" @endif>
</form>

<input type="hidden" id="tree_disabled" value="{{session('client_id')}}">
<button class="main_button_style" type="button" id="tree_change_display" data-toggle="tooltip" title="本機能を隠蔽、もしくは隠蔽状態を解除します 隠蔽した機能をツリー画面に表示するためには、ツリー画面で露出をクリックします">
    <img class="main_button_img" src="data:image/png;base64,{{Config::get('base64.ng')}}" alt="隠蔽/表示">
</button>

<form action="#" method="get">
    <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.road')}}" alt="再表示" onclick="submit();" id="open_tree" data-toggle="tooltip" title="ツリーを再表示します">
</form>

<form>
    <custom-tooltip title="変確定します
挿入 : 本データの下位に新しいデータを追加しま更 : 登録更新をす
削除 : 詳細領域のデータを下位ツリーのデータを含めて削除します
複写 : 詳細領域のデータをクリップボードに複写します
取消 : クリップボードの内容を抹消します
隠蔽/表示 : 本機能を隠蔽、もしくは隠蔽状態を解除します 隠蔽した機能をツリー画面に表示するためには、ツリー画面で露出をクリックします
再表示 : 再表示します">
    </custom-tooltip>
</form>

<input type="checkbox" id="check" onclick="deleteOn()" data-toggle="tooltip" title="チェックを入れることで削除ボタンがクリックできるようになります（削除権限がある場合）">
<font size="-2" color="red">削除有効化</font>

<input type="checkbox" id="check2" onclick="updateOn()" data-toggle="tooltip" title="チェックを入れることで更新ボタンがクリックできるようになります（権限がある場合）">
<font size="-2" color="red">更新有効化</font>
<form>
    <custom-tooltip title="削除有効化:チェックを入れることで削除ボタンがクリックできるようになります
更新有効化:チェックを入れることで更新ボタンがクリックできるようになります"></custom-tooltip>
</form>
