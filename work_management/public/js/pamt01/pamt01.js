var display=function() {
    //切り替える対象の状態を取得
    var div = document.getElementById('tree');
    //取得した情報からスタイルについての状態のみをstateに代入
    state=div.style.display;
    //デバッグ用にlogに出力
    console.log(state);
    //非表示中のときの処理
    if(state=="none"){
         //スタイルを表示(inline)に切り替え
         div.setAttribute("style","display:inline");
        //デバッグ用にinlineをlogに出力
        console.log("inline");
    }else{
         //スタイルを非表示(none)に切り替え
        div.setAttribute("style","display:none");
        //デバッグ用にnoneをlogに出力
        console.log("none");
    }
}

/**
 * 削除のON・OFF切替メソッド
 */
function deleteOn()
{
    if (document.getElementById("delete").disabled === true){
		// disabled属性を削除
		document.getElementById("delete").removeAttribute("disabled");
	}else{
		// disabled属性を設定
		document.getElementById("delete").setAttribute("disabled", true);
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
 */
function clickSave()
{
  var id = document.getElementById("department_id").value ;

  document.getElementById("lower_move").defaultValue = id;
  document.getElementById("copy").defaultValue = id;
  document.getElementById("projection_source").defaultValue = id;
  document.getElementById("ji_lower_move").defaultValue = id;

}

function clickDelete()
{
  var id = "";

  document.getElementById("lower_move").defaultValue = id;
  document.getElementById("copy").defaultValue = id;
  document.getElementById("projection_source").defaultValue = id;
  document.getElementById("ji_lower_move").defaultValue = id;

}

