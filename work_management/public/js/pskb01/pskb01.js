/**
 * 掲示板詳細の削除のON・OFF切替メソッド
 * @var int count 掲示板一覧テーブルの行数
 * @var int id 掲示板一覧対象のID
 * @var string check チェックボタンの状態
 */
 function deleteOn3() {
    var count = document.getElementById("kb-table").rows.length - 1;
  
    if (document.getElementById("delete").disabled === true) {
      // disabled属性を削除
      document.getElementById("delete").removeAttribute("disabled");
      document.getElementById("delete").style.opacity = 1;
      for (i = 0; i < count; i++) {
        var id = "kb_list_delete" + i;
        document.getElementById(id).setAttribute("style", "pointer-events: auto; display:inline-block; cursor: hand; cursor:pointer; text-decoration:underline; margin:0px; color:blue;");
      }
  
      //チェック状態の取得
      var check = document.getElementById("check").value;
  
      //checkの状態をストレージに保存
      localStorage.setItem('check', check);
  
    } else {
      // disabled属性を設定
      document.getElementById("delete").setAttribute("disabled", true);
      document.getElementById("delete").style.opacity = 0.3;
      for (i = 0; i < count; i++) {
        var id = "kb_list_delete" + i;
        document.getElementById(id).setAttribute("style", "pointer-events: none; display:inline-block; cursor: auto; text-decoration:underline; margin:0px;");
      }
  
      //checkの状態をストレージに保存
      localStorage.setItem('check', check);

    }    
}