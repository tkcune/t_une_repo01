/** 
 * ページネーションのページ遷移時の状態を引き継ぐメソッド
 * 
 * @var string element 名前の要素
 * @var string title 名前箇所のtitle属性の内容
 * @var string session ページ遷移前のtitle属性の内容
 */
 window.addEventListener('pageshow',()=>{

  // 開いたページの番号が記載された要素を取得
  var element = document.getElementById("id_number");

  //  開いたページの番号が記載された要素のtitle属性を取得
  var title = element.title;

  var session = window.sessionStorage.getItem(['page_id']);

  //ページ遷移前とページ遷移後の番号が同じかどうかの判断
  if(title == session){

    //チェックボックスの状態の判断
    if(localStorage.getItem('check') == "undefined") {
      document.getElementById("check").checked = false;
      console.log(localStorage.getItem('check'));
    }else if(localStorage.getItem('check') == "on"){
      deleteOn3();
      document.getElementById("check").checked = true;
    }else{

    }
  }
  //チェック状態のリセット
  localStorage.removeItem('check');

  //セッションの削除
  window.sessionStorage.removeItem(['page_id']);

});

/**
 * 掲示板詳細の削除のON・OFF切替メソッド
 * @var int count 掲示板一覧テーブルの行数
 * @var int id 掲示板一覧対象のID
 * @var string check チェックボタンの状態
 */
 function deleteOn3() {
    var count = document.getElementById("kb-table").rows.length - 1;
    var count2 = document.getElementById("ft-table").rows.length - 1;
  
    if (document.getElementById("delete").disabled === true) {
      // disabled属性を削除
      document.getElementById("delete").removeAttribute("disabled");
      document.getElementById("delete").style.opacity = 1;
      for (i = 0; i < count; i++) {
        var id = "kb_list_delete" + i;
        document.getElementById(id).setAttribute("style", "pointer-events: auto; display:inline-block; cursor: hand; cursor:pointer; text-decoration:underline; margin:0px; color:blue;");
      }
      for (j = 0; j < count2; j++) {
        var id2 = "ft_list_delete" + j;
        document.getElementById(id2).setAttribute("style", "pointer-events: auto; display:inline-block; cursor: hand; cursor:pointer; text-decoration:underline; margin:0px; color:blue;");
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
      for (j = 0; j < count2; j++) {
        var id2 = "ft_list_delete" + j;
        document.getElementById(id2).setAttribute("style", "pointer-events: none; display:inline-block; cursor: auto; text-decoration:underline; margin:0px;");
      }
  
  
      //checkの状態をストレージに保存
      localStorage.setItem('check', check);
    } 
  }

  /**
 * 掲示板詳細の付帯定義反映
 * @var int count 掲示板一覧テーブルの行数
 * @var int id 掲示板一覧対象のID
 * @var string check チェックボタンの状態
 */
 function reflection(id,id2) {
  
  if(id2.substring(0,4) == "http"){
    document.getElementById("url_id").defaultValue = id;
    document.getElementById("url").value = id2;
  }else{
    document.getElementById("file_id").defaultValue = id;
    document.getElementById("file_name").value = id2;
  }
}

/**
 * 掲示板備考欄及び微細情報の隠蔽表示メソッド
 * @var string div 備考欄のdiv
 * @var string div2 微細情報のdiv
 * @var string stamp_div スタンプ情報のdiv
 * @var string state 対象のdivの状態
 */
var boardRemarks = function () {
  //切り替える対象の状態を取得
  var div = document.getElementById('remarks-field');
  var div2 = document.getElementById('little-information-field');
  var stamp_div = document.getElementById('stamp-field');
  //取得した情報からスタイルについての状態のみをstateに代入
  state = div.style.display;
  state2 = div2.style.display;
  //非表示中のときの処理
  if (state == "none") {
    //スタイルを表示(inline)に切り替え
    div.setAttribute("style","display:block");
    div2.setAttribute("style", "display:block");
    stamp_div.setAttribute("style", "display:block");

  } else {
    //スタイルを非表示(none)に切り替え
    div.setAttribute("style", "display:none");
    div2.setAttribute("style", "display:none");
    stamp_div.setAttribute("style", "display:none");
    
  }
}

/*
 *スタンプホバーメソッド
 */
function over(x) {
  var num = x.id;
  var stamp_id = 'stamp_names'+num;
  var div = document.getElementsByClassName(stamp_id);
  let element = div.item(0);
  element.setAttribute("style","display:block");
}

/*
 *スタンプホバー解除メソッド
 */
function leave(x) {
  var num = x.id;
  var stamp_id = 'stamp_names'+num;
  var div = document.getElementsByClassName(stamp_id);
  let element = div.item(0);
  element.setAttribute("style","display:none");
}

function stamp() {
  //切り替える対象の状態を取得
  var div = document.getElementById('stamp-area');
  //取得した情報からスタイルについての状態のみをstateに代入
  state = div.style.display;
  //非表示中のときの処理
  if (state == "none") {
    //スタイルを表示(inline)に切り替え
    div.setAttribute("style", "display:inline");
  } else {
    //スタイルを非表示(none)に切り替え
    div.setAttribute("style", "display:none");
  }
}

//スタンプ動作
function stampClick(e) {
  const stamp_data = e.currentTarget.dataset['stamp'];
  const stamp_src = e.currentTarget.children[0].src;
  const board_data = document.getElementById('stamp-area').dataset['board'];
  const client_data = document.getElementById('stamp-area').dataset['client'];
  const personnel_data = document.getElementById('stamp-area').dataset['personnel'];

  //スタンプ画面を閉じる
  stamp();
  
  //ajax処理スタート
  $.ajax({
    headers: { //HTTPヘッダ情報をヘッダ名と値のマップで記述
      'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
    },  //↑name属性がcsrf-tokenのmetaタグのcontent属性の値を取得

    url: '/pskb/stamp', //通信先アドレスで、このURLをあとでルートで設定します
    method: 'POST', //HTTPメソッドの種別を指定します。1.9.0以前の場合はtype:を使用。
    data: { //サーバーに送信するデータ
      'stamp_id': stamp_data,//押されたスタンプの番号
      'board_id': board_data, //掲示板ID
      'client_id': client_data, //顧客ID
      'personnel_id': personnel_data //人員ID
    },
  })
  //通信成功した時の処理
  .done(function (data) {

    before_count = document.getElementsByClassName("stamp_cicrle").length;
    after_count = data.length;
    
    if(before_count > after_count){
      //カウントが0のスタンプは削除
      var delete_div = document.getElementById(stamp_data);
      delete_div.parentNode.removeChild(delete_div);

      for(i=0;i<data.length;i++){
        document.getElementsByClassName("stamp_count")[i].textContent = data[i].count;
      }
      
    }else if(before_count < after_count){
      //新しく押されたスタンプを追加

      //div要素の生成
      var div = document.createElement("div");
      div.className = 'stamp_cicrle';
      div.id = stamp_data;
      div.style = 'display:inline-block';

      //img要素の生成
      var img = document.createElement("img");
      img.src = stamp_src;
      img.alt = "スタンプ";
      img.className = 'stamp_button';

      //span要素の生成
      var span = document.createElement("span");
      span.className = "stamp_count"
      span.innerHTML = 1;

      //divの生成
      div.appendChild(img);
      div.appendChild(span);

      //domの生成
      document.getElementById('stamp-field').appendChild(div);

      for(i=0;i<data.length;i++){
        document.getElementsByClassName("stamp_count")[i].textContent = data[i].count;
      }

    }else{
      //スタンプ数に変化が無い場合は数字のみ変更
      for(i=0;i<data.length;i++){
        document.getElementsByClassName("stamp_count")[i].textContent = data[i].count;
      }
    }
  })
  //通信失敗した時の処理
  .fail(function () {

  });
}

