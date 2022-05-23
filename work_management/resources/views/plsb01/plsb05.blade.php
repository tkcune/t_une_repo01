 <!-- 作業場所 取消・隠蔽・再表示・更新、削除有効化 -->
<form action="{{ route('pa0001.clipboard',"sb00000000")}}" method="get">
    @csrf
    <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.copy')}}" alt="複写" onclick="submit();" id="copyTarget" data-toggle="tooltip" title="クリックにより、詳細領域のデータをクリップボードに複写します">
</form>

<button class="main_button_style" type="button" id="tree_change_display" data-toggle="tooltip" title="ツリーを表示します" onclick="displayOn()">
    <img class="main_button_img" src="data:image/png;base64,{{Config::get('base64.tree')}}" alt="開く">
</button>

{{--動作の為に非表示で設置--}}
<form action="#" method="post">
    @csrf
    @method('post')
    <input type="submit" id="delete" value="削除" style="display:none;" data-toggle="tooltip" title="削除有効化をチェックした状態でのクリックにより、詳細領域のデータを下位ツリーのデータを含めて削除します" disabled>
</form>
{{--ここまで--}}

<form>
    <custom-tooltip title="変確定します
複写 : 詳細領域のデータをクリップボードに複写します
再表示 : 再表示します">
    </custom-tooltip>
</form>

<input type="checkbox" id="check" onclick="deleteOn4()" data-toggle="tooltip" title="チェックを入れることで削除ボタンがクリックできるようになります（削除権限がある場合）">
<font size="-2" color="red">削除有効化</font>

<form>
    <custom-tooltip title="削除有効化:チェックを入れることで削除ボタンがクリックできるようになります"></custom-tooltip>
</form>
