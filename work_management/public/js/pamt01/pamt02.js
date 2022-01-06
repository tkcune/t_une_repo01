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