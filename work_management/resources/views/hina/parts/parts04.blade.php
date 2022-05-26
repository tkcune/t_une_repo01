<!-- 詳細画面 枠内新規登録・削除・複写機能 -->
<form action="#" method="get">
    <!-- 新規登録 -->
    @csrf
    <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.new')}}" alt="新規" onclick="submit();" data-toggle="tooltip" title="本データの下位に新しいデータを追加します">
    <input type="hidden" id="high_new" name="high" value="">
</form>

<form action="#" method="post">
    <!-- 削除 -->
    @csrf
    @method('post')
    <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.delete')}}" alt="削除" onclick="submit();" id="delete" data-toggle="tooltip" title="削除有効化をチェックした状態でのクリックにより、詳細領域のデータを下位ツリーのデータを含めて削除します" disabled>
</form>

<form action="#" method="get">
    <!-- 複写 -->
    @csrf
    <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.copy')}}" alt="複写" onclick="submit();" id="copyTarget" data-toggle="tooltip" title="クリックにより、詳細領域のデータをクリップボードに複写します">
</form>
