/**
 * 画面の隠蔽表示メソッド
 * @param string div 対象のdiv
 * @param string state 対象のdivの状態
 */

var display = function () {
  //切り替える対象の状態を取得
  var div = document.getElementById('tree');
  //取得した情報からスタイルについての状態のみをstateに代入
  state = div.style.display;
  //非表示中のときの処理
  if (state == "none") {
    //スタイルを表示(inline)に切り替え
    div.setAttribute("style", "");
  } else {
    //スタイルを非表示(none)に切り替え
    div.setAttribute("style", "display:none");
  }
}

/**
 * 画面の隠蔽表示メソッド
 * @var string div 対象のdiv
 * @var string state 対象のdivの状態
 */

var displayOn = function () {
  //切り替える対象の状態を取得
  var div = document.getElementById('tree');
  //取得した情報からスタイルについての状態のみをstateに代入
  state = div.style.display;
  //非表示中のときの処理
  if (state == "none") {
    //スタイルを切り替え
    div.setAttribute("style", "");
  }
}

/**
 * 一覧画面の隠蔽表示メソッド
 * @var string div 対象のdiv
 * @var string state 対象のdivの状態
 */

var listOn = function () {
  //切り替える対象の状態を取得
  var div = document.getElementById('list');
  //取得した情報からスタイルについての状態のみをstateに代入
  state = div.style.display;
  //非表示中のときの処理
  if (state == "none") {
    //スタイルを表示(inline)に切り替え
    div.setAttribute("style", "display:inline");
    document.getElementById('list-open-button').setAttribute("style", "display:none");
  } else {
    //スタイルを非表示(none)に切り替え
    div.setAttribute("style", "display:none");
    document.getElementById('list-open-button').setAttribute("style", "display:inline");
  }
}

/**
 * 削除のON・OFF切替メソッド
 * @var int count 部署一覧テーブルの行数
 * @var int count2 人員一覧テーブルの行数
 * @var int id 部署一覧対象のID
 * @var int id2 人員対象のID
 */
function deleteOn() {
  var count = document.getElementById("bs-table").rows.length - 1;
  var count2 = document.getElementById("ji-table").rows.length - 1;

  if (document.getElementById("delete").disabled === true) {
    // disabled属性を削除
    document.getElementById("delete").removeAttribute("disabled");
    for (i = 0; i < count; i++) {
      var id = "bs_list_delete" + i;
      document.getElementById(id).setAttribute("style", "pointer-events: auto; display:inline-block; cursor: hand; cursor:pointer; text-decoration:underline; margin:0px; color:blue;");
    }
    for (j = 0; j < count2; j++) {
      var id2 = "list_delete" + j;
      document.getElementById(id2).setAttribute("style", "pointer-events: auto; display:inline-block; cursor: hand; cursor:pointer; text-decoration:underline; margin:0px; color:blue;");
    }
  } else {
    // disabled属性を設定
    document.getElementById("delete").setAttribute("disabled", true);
    for (i = 0; i < count; i++) {
      var id = "bs_list_delete" + i;
      document.getElementById(id).setAttribute("style", "pointer-events: none; display:inline-block; cursor: auto; text-decoration:underline; margin:0px;");
    }
    for (j = 0; j < count2; j++) {
      var id2 = "list_delete" + j;
      document.getElementById(id2).setAttribute("style", "pointer-events: none; display:inline-block; cursor: auto; text-decoration:underline; margin:0px;");
    }
  }
}

/**
 * パスワード入力欄を露出・隠蔽するメソッド
 */
function loginDisabled() {
  if (document.getElementById("login").style.visibility = "hidden") {
    // ログイン欄を出す
    document.getElementById("login").style.visibility = "visible";
  } else {
    // ログイン欄を消す
    document.getElementById("login").style.visibility = "hidden";
  }
}

/**
 * パスワードを露出・隠蔽するメソッド
 */
function passwordOn() {
  if (document.getElementById("password").type === 'password') {
    // パスワード属性をテキスト属性に変更
    document.getElementById("password").type = "text";
  } else {
    // テキスト属性をパスワード属性に変更
    document.getElementById("password").type = "password";
  }
}

/**
 * ツリーの隠蔽表示メソッド
 * @var string id 対象のid
 * @var string state 対象のdivの状態
 */

function treeDisabled() {
  var id = document.getElementById("tree");

  state = id.style.display;

  if (state == "none") {
    //スタイルを表示(inline)に切り替え
    id.setAttribute("style", "display:inline");
  } else {
    //スタイルを非表示(none)に切り替え
    id.setAttribute("style", "display:none");
  }
}

/**
 * 複写した部署のIDを取得し各種フォームのvalueに当てはめるメソッド
 * @var string id 対象のID
 * 
 */
function clickSave() {
  var id = document.getElementById("department_id").value;

  document.getElementById("lower_move").defaultValue = id;
  document.getElementById("copy").defaultValue = id;
  document.getElementById("projection_source").defaultValue = id;
  document.getElementById("ji_lower_move").defaultValue = id;

}

/**
 * 複写した部署を取り消すメソッド
 * @var string id IDを空にするための引数
 * 
 */

function clickDelete() {
  var id = "";

  document.getElementById("lower_move").defaultValue = id;
  document.getElementById("copy").defaultValue = id;
  document.getElementById("projection_source").defaultValue = id;
  document.getElementById("ji_lower_move").defaultValue = id;

}

/**
 * 管理者検索メソッド
 * @var string name  入力した人員名
 * @var int    count optionの個数
 * @var string label 入力した人員の人員番号
 *    
 * @var string okSystem システム情報メッセージログを表示させるID
 * @var string errSystem システム異常メッセージログを表示させるID   
 * 
 */

function search() {
  //
  var name = document.getElementById("search-list").value;

  console.log(name);
  //optionの個数を判断する
  const count = document.getElementById('search-list').list.options;

  for (i = 0; i < count.length; i++) {
    if (name == document.getElementById('search-list').list.options[i].value) {
      label = document.getElementById('search-list').list.options[i].getAttribute('label');
      mangement = document.getElementById('search-list').list.options[i].getAttribute('managment');
      console.log(mangement);
    }
  }

  document.getElementById("management_number").defaultValue = label;
  document.getElementById("management_name").innerText = name;

  //  名前をクリックすることで利用者の詳細ページに移動する
  document.getElementById("management_name").onclick = function () {
    location.href = `show/aa00000001/${label}`;
  }


  //  ログ管理システム　管理者の場合のみチェックボックスを表示させる 
  const okSystem = document.getElementById("okSystem");
  const errSystem = document.getElementById("errSystem");

  if (typeof okSystem != 'undefined'  &&  mangement == "1") {
    okSystem.innerHTML = ` <input type="checkbox" name="check[]" value="si" title="システム情報メッセージログを表示するかどうかを指定します" form="create"> システム情報メッセージ `;
  }else{
    okSystem.innerHTML = "";
  }

  if (typeof errSystem != 'undefined' && mangement  == "1") {
    errSystem.innerHTML = ` <input type="checkbox" name="check[]" value="sy" title="システム異常メッセージログを表示するかどうかを指定します" form="create"> システム異常メッセージ`;
  }else{
    errSystem.innerHTML ="";
  }

}


document.getElementById("search-list").onchange = search;
