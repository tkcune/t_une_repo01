<form action="{{ route('pa0001.deleteclipboard')}}" method="get">
    @csrf
    <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.remove')}}" alt="取消" onclick="submit();" data-toggle="tooltip" title="クリップボードに複写した内容を抹消します" @if(null == session()->get('clipboard_id'))) disabled style="opacity:0.3;"@endif>
</form>

<input type="checkbox" id="check" onclick="deleteOn2()" data-toggle="tooltip" title="チェックを入れることで削除ボタンがクリックできるようになります（削除権限がある場合）">
<font size="-2" color="red">削除有効化</font>

<input type="checkbox" id="check2" onclick="updateOn()" data-toggle="tooltip" title="チェックを入れることで更新ボタンがクリックできるようになります（権限がある場合）">
<font size="-2" color="red">更新有効化</font>