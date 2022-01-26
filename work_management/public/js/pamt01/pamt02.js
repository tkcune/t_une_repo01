/**
 * 備考欄挿入
 * @var value 備考欄の中身
 * 
 */

 //今後ページレイアウトの修正後に削除する可能性大

 function remarks() {
    var value = document.getElementById("remarks_set").value;
    document.getElementById("remarks").defaultValue = value;
  }
  document.getElementById("remarks_set").onchange = remarks;

  /**
 * 備考欄及び微細情報の隠蔽表示メソッド
 * @var string div 備考欄のdiv
 * @var string div2 微細情報のdiv
 * @var string state 対象のdivの状態
 */

 var remarksOn = function () {
  //切り替える対象の状態を取得
  var div = document.getElementById('remarks-field');
  var div2 = document.getElementById('little-information-field');
  //取得した情報からスタイルについての状態のみをstateに代入
  state = div.style.display;
  state2 = div2.style.display;
  //非表示中のときの処理
  if (state == "none") {
    //スタイルを表示(inline)に切り替え
    div.setAttribute("style","display:block");
    div2.setAttribute("style", "display:block");
    //document.getElementById('list-open-button').setAttribute("style", "display:none");
  } else {
    //スタイルを非表示(none)に切り替え
    div.setAttribute("style", "display:none");
    div2.setAttribute("style", "display:none");
    //document.getElementById('list-open-button').setAttribute("style", "display:inline");
  }

  
  
}