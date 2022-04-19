<form action="{{ route('pa0001.deleteclipboard')}}" method="get">
    @csrf
    <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.remove')}}" alt="取消" onclick="submit();" data-toggle="tooltip" title="クリップボードに複写した内容を抹消します" @if(null == session()->get('clipboard_id'))) disabled style="opacity:0.3;"@endif>
</form>

<form action="{{ route('pa0001.redirect')}}" method="get">
    <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.road')}}" alt="再表示" onclick="submit();" data-toggle="tooltip" title="ツリーを再表示します">
</form>

<form><custom-tooltip title="変更 : 登録更新を確定します
挿入 : 本データの下位に新しいデータを追加します
削除 : 詳細領域のデータを下位ツリーのデータを含めて削除します
複写 : 詳細領域のデータをクリップボードに複写します
取消 : クリップボードの内容を抹消します
再表示 : 再表示します"></custom-tooltip></form>

<input type="checkbox" id="check" onclick="deleteOn2()" data-toggle="tooltip" title="チェックを入れることで削除ボタンがクリックできるようになります（削除権限がある場合）">
<font size="-2" color="red">削除有効化</font>

<input type="checkbox" id="check2" onclick="updateOn()" data-toggle="tooltip" title="チェックを入れることで更新ボタンがクリックできるようになります（権限がある場合）">
<font size="-2" color="red">更新有効化</font>
<form><custom-tooltip title="削除有効化:チェックを入れることで削除ボタンがクリックできるようになります
更新有効化:チェックを入れることで更新ボタンがクリックできるようになります"></custom-tooltip></form>