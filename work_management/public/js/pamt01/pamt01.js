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

//クリップボードに保存するメソッド
function copyToClipboard() {
    // コピー対象を取得
    var text = document.getElementById('palent').textContent;

    // 選択しているテキストをクリップボードにコピーする
    navigator.clipboard.writeText(text).then(e => {
      });

}

//クリップボード内を削除するメソッド
function deleteToClipboard() {
    // コピー対象をJavaScript上で変数として定義する
    var text = document.getElementById('deletearea').value;

    // 選択しているテキストをクリップボードにコピーする
    navigator.clipboard.writeText(text).then(e => {
      });

}

function treeDisabled()
{
    var id = document.getElementById("tree");

    if (document.getElementById(id).disabled === true){
		// disabled属性を削除
		document.getElementById(id).removeAttribute("disabled");
	}else{
		// disabled属性を設定
		document.getElementById(id).setAttribute("disabled", true);
	}
}