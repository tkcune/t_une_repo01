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