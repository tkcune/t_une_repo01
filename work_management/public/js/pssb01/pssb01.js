/**
 * 作業場所詳細の削除のON・OFF切替メソッド
 * @var int count 作業場所一覧テーブルの行数
 * @var int id 作業場所一覧対象のID
 * @var string check チェックボタンの状態
 */
 function deleteOn4() {
    var count = document.getElementById("sb-table").rows.length - 1;
    var count2 = document.getElementById("ji-table").rows.length - 1;

    if (document.getElementById("delete").disabled === true) {
      // disabled属性を削除
      document.getElementById("delete").removeAttribute("disabled");
      document.getElementById("delete").style.opacity = 1;
      for (i = 0; i < count; i++) {
        var id = "sb_list_delete" + i;
        document.getElementById(id).setAttribute("style", "pointer-events: auto; display:inline-block; cursor: hand; cursor:pointer; text-decoration:underline; margin:0px; color:blue;");
      }
      for (j = 0; j < count2; j++) {
        var id2 = "list_delete" + j;
        document.getElementById(id2).setAttribute("style", "pointer-events: auto; display:inline-block; cursor: hand; cursor:pointer; text-decoration:underline; margin:0px; color:blue;");
      }

      //チェック状態の取得
      var check = document.getElementById("check").value;

      //checkの状態をストレージに保存
      localStorage.setItem('check', check);

    } else {
      //disabled属性を設定
      document.getElementById("delete").setAttribute("disabled", true);
      document.getElementById("delete").style.opacity = 0.3;
      for (i = 0; i < count; i++) {
        var id = "sb_list_delete" + i;
        document.getElementById(id).setAttribute("style", "pointer-events: none; display:inline-block; cursor: auto; text-decoration:underline; margin:0px;");
      }
      for (j = 0; j < count2; j++) {
        var id2 = "list_delete" + j;
        document.getElementById(id2).setAttribute("style", "pointer-events: none; display:inline-block; cursor: auto; text-decoration:underline; margin:0px;");
      }

      //checkの状態をストレージに保存
      localStorage.setItem('check', check);
    }
  }

  /**
 * 作業場所詳細の削除のON・OFF切替メソッド
 * @var int count 部署一覧テーブルの行数
 * @var int count2 人員一覧テーブルの行数
 * @var int id 部署一覧対象のID
 * @var int id2 人員対象のID
 * @var string check チェックボタンの状態
 */
 function deleteOn5() {
    var count = document.getElementById("sb-table").rows.length - 1;

    if (document.getElementById("delete").disabled === true) {
      // disabled属性を削除
      document.getElementById("delete").removeAttribute("disabled");
      document.getElementById("delete").style.opacity = 1;
      for (i = 0; i < count; i++) {
        var id = "sb_list_delete" + i;
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
        var id = "sb_list_delete" + i;
        document.getElementById(id).setAttribute("style", "pointer-events: none; display:inline-block; cursor: auto; text-decoration:underline; margin:0px;");
      }

      //checkの状態をストレージに保存
      localStorage.setItem('check', check);
    }

    
  }
