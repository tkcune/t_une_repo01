/**
 * 画面の隠蔽表示メソッド
 * @param string div 対象のdiv
 * @param string state 対象のdivの状態
 */

var display=function() {
    //切り替える対象の状態を取得
    var div = document.getElementById('tree');
    //取得した情報からスタイルについての状態のみをstateに代入
    state=div.style.display;
    //非表示中のときの処理
    if(state=="none"){
         //スタイルを表示(inline)に切り替え
         div.setAttribute("style","display:inline");
    }else{
         //スタイルを非表示(none)に切り替え
        div.setAttribute("style","display:none");
    }
}

/**
 * 画面の隠蔽表示メソッド
 * @var string div 対象のdiv
 * @var string state 対象のdivの状態
 */

 var displayOn=function() {
  //切り替える対象の状態を取得
  var div = document.getElementById('tree');
  //取得した情報からスタイルについての状態のみをstateに代入
  state=div.style.display;
  //非表示中のときの処理
  if(state=="none"){
       //スタイルを表示(inline)に切り替え
       div.setAttribute("style","display:inline");
  }
}

/**
 * 一覧画面の隠蔽表示メソッド
 * @var string div 対象のdiv
 * @var string state 対象のdivの状態
 */

 var listOn=function() {
  //切り替える対象の状態を取得
  var div = document.getElementById('list');
  //取得した情報からスタイルについての状態のみをstateに代入
  state=div.style.display;
  //非表示中のときの処理
  if(state=="none"){
    //スタイルを表示(inline)に切り替え
    div.setAttribute("style","display:inline");
    document.getElementById('list-open-button').setAttribute("style","display:none");
  }else{
    //スタイルを非表示(none)に切り替え
    div.setAttribute("style","display:none");
    document.getElementById('list-open-button').setAttribute("style","display:inline");
  }
}

/**
 * 削除のON・OFF切替メソッド
 * @var int id 部署一覧対象のID
 * @var int id2 人員対象のID
 */
function deleteOn()
{
    if (document.getElementById("delete").disabled === true){
		// disabled属性を削除
		document.getElementById("delete").removeAttribute("disabled");
    for(i=0; i<5 ;i++){
      var id= "bs_list_delete"+ i;
      var id2= "list_delete"+ i;
      document.getElementById(id).setAttribute("style","pointer-events: auto; display:inline-block; cursor: hand; cursor:pointer; text-decoration:underline; margin:0px; color:blue;");
      document.getElementById(id2).setAttribute("style","pointer-events: auto; display:inline-block; cursor: hand; cursor:pointer; text-decoration:underline; margin:0px; color:blue;");
    }
	}else{
		// disabled属性を設定
		document.getElementById("delete").setAttribute("disabled", true);
    for(i=0; i<5 ;i++){
      var id= "bs_list_delete"+ i;
      var id2= "list_delete"+ i;
      document.getElementById(id).setAttribute("style","pointer-events: none; display:inline-block; cursor: auto; text-decoration:underline; margin:0px;");
      document.getElementById(id2).setAttribute("style","pointer-events: none; display:inline-block; cursor: auto; text-decoration:underline; margin:0px;");
    }
	}
}

/**
 * パスワード入力欄を露出・隠蔽するメソッド
 */
 function loginDisabled()
 {
     if (document.getElementById("login").style.visibility ="hidden"){
     // ログイン欄を出す
     document.getElementById("login").style.visibility ="visible";
   }else{
     // ログイン欄を消す
     document.getElementById("login").style.visibility ="hidden";
   }
 }

/**
 * パスワードを露出・隠蔽するメソッド
 */
function passwordOn()
{
    if (document.getElementById("password").type === 'password'){
		// パスワード属性をテキスト属性に変更
		document.getElementById("password").type="text";
	}else{
		// テキスト属性をパスワード属性に変更
		document.getElementById("password").type="password";
	}
}

/**
 * ツリーの隠蔽表示メソッド
 * @var string id 対象のid
 * @var string state 対象のdivの状態
 */

function treeDisabled()
{
    var id = document.getElementById("tree");

    state=id.style.display;

    if(state=="none"){
      //スタイルを表示(inline)に切り替え
      id.setAttribute("style","display:inline");
    }else{
      //スタイルを非表示(none)に切り替え
     id.setAttribute("style","display:none");
 }
}

/**
 * 複写した部署のIDを取得し各種フォームのvalueに当てはめるメソッド
 * @var string id 対象のID
 * 
 */
function clickSave()
{
  var id = document.getElementById("department_id").value ;

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

function clickDelete()
{
  var id = "";

  document.getElementById("lower_move").defaultValue = id;
  document.getElementById("copy").defaultValue = id;
  document.getElementById("projection_source").defaultValue = id;
  document.getElementById("ji_lower_move").defaultValue = id;

}

/**
 * 管理者検索メソッド
 * @var string id IDを格納する引数
 * 
 */

 function search()
 {
   document.getElementById("keywords").data();
  
   console.log( result );
   
   document.getElementById("management_number").defaultValue = id;

 }

