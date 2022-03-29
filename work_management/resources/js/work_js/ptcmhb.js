import { forEach } from 'lodash';
import { findMobile } from './ptcmrd'

//var HIerarchyBarクラス パンくずリストクラス
let HierarchyBar = {};

if(findMobile.device_name !== 'pc'){

HierarchyBar = ((bar_info) => {
    //@var object 作業管理システムのデータ階層
    let bar = {};
    //@var dom パンくずリストのdom
    let bar_element = null;
    if(document.getElementById('panlist')){
        bar_element = document.getElementById('panlist');
    }
    //@var string 次の表示リスト
    let next = '';
    
    //パンくずリストのオブジェクトデータの作成
    let createBarinfo = function createBarinfo(){
      bar_info.forEach(block => {
        block.forEach(info => {
          //@var string 親要素の値
          let key = Object.keys(info)[0];
          //"1.chaintree"と"0.notitle"は"作業管理システム"という名前に変える
          if(key === "1.chaintree"){
            key = "作業管理システム";
          }
          if(key === "0.notitle"){
            key = "作業管理システム";
          }
          //@var mix 子要素の値
          let value = Object.values(info)[0];
          //"0.notitle"はループをコンティニューする
          if(value === "0.notitle"){
            return;
          }
          //barにkeyがキーとして登録されていれば
          if(key in bar){
            bar[key].push(value);
          }else{
            bar[key] = [value];
          }
        });
      });
    };
  
    //パンくずリストのdom作成
    //@param array パンくずリストに表示する要素の配列
    let createBar = function createBar(barinfo){
      barinfo.forEach(info => {
        createBarElement(info, bar[info]);
      });
    }
    //リストの1つのdomを作成する
    //@param string info 表示する要素の情報
    //@param array barinfo infoの子要素
    let createBarElement = function createBarElement(info, barinfo){
      //タイトルの作成
      bar_element.appendChild(createTitleElement(info));
      //子要素があれば、次の選択の要素を作成する
      if(barinfo){
        bar_element.appendChild(createNextElement(barinfo));
      }
    }
  
    //タイトルのdomの作成
    //@param string タイトルの情報
    let createTitleElement = function createTitleElement(info){
      //@var dom タイトルのdiv要素
      let titleDiv = document.createElement('div');
      if(info === next){
          titleDiv.classList.add('current_bar');
      }
      //表示するタイトルを代入する
      if(info !== '作業管理システム'){
        titleDiv.innerText = info.split('.')[1];
        titleDiv.addEventListener('click', {info: info, handleEvent: page_move});
      }else{
        titleDiv.innerText = '作業管理システム';
      }
      return titleDiv;
    };
  
    let createNextElement = function createNextElement(info){
      //@var dom 次のリストのdom
      let nextDiv = document.createElement('div');
      //@var dom >の矢印のdom
      let nextClickDiv = document.createElement('div');
      //'>'を代入する
      nextClickDiv.innerText = '>';
      //マウスを合わせた時にポインターを表示する(pcの場合の確認のため)
      nextClickDiv.style.cursor = 'pointer';
      //マージンの設定
      nextClickDiv.style.margin = '0 5px';
      //@var boolean 表示、非表示の判断
      let clickon = false;
      //クリック処理(仮)
      nextClickDiv.addEventListener('click' ,() => {
        if(clickon === true){
          ul.classList.add('notdisplaylist');
          clickon = false;
        }else{
          ul.classList.remove('notdisplaylist');
          clickon = true;
        }
      });
      //@var dom 遷移リストを表示するdom
      let ul = document.createElement('ul');
      //cssを加える
      ul.classList.add('barlist');
      ul.classList.add('notdisplaylist');
      info.forEach(info => {
        //@var dom 遷移する要素、クリックして移動する
        let li = document.createElement('li');
        //タイトルを代入する
        li.innerText = info.split('.')[1];
        if(info.split('.')[0].substr(0,2) === 'ta'){
            li.classList.add('projection_bar');
        }
        //移動のクリック処理
        li.addEventListener('click', {info: info, handleEvent: page_move});
        //pc確認のためのポインター
        li.style = 'cursor: pointer;';
        ul.appendChild(li);
      });
      nextDiv.appendChild(nextClickDiv);
      nextDiv.appendChild(ul);
      return nextDiv;
    }

    //ページ移動
    let page_move = function(){
      //次の要素を保存する
      next = this.info;
      let id = this.info.split('.')[0];

      if(id === 'sslg'){
        //ログ確認の場合
        window.location = 'http://localhost:8000/pslg';
      }else if(id === 'ssnw'){
        window.location = 'http://localhost:8000/psnw01';
      }else if(id.substr(0, 2) === 'ji' || id.substr(0, 2) === 'bs'){
        //@var string Laravelのセッションid
        let clientId = document.getElementById('hidden_client_id').value;
        //@var string ノードのid
        let nodeId = id;
        //移動命令
        window.location = `http://localhost:8000/show/${clientId}/${nodeId}`;
      }else if(id.substr(0, 2) === 'kb'){
        //@var string Laravelのセッションid
        let clientId = document.getElementById('hidden_client_id').value;
        //@var string ノードのid
        let nodeId = id;
        if(nodeId === 'kb'){
          //移動命令
          window.location = `http://localhost:8000/pskb/`;
        }else{
          //移動命令
          window.location = `http://localhost:8000/pskb/show/${clientId}/${nodeId}`;
        }
      }else if(id.substr(0, 2) === 'sb'){
        //@var string Laravelのセッションid
        let clientId = document.getElementById('hidden_client_id').value;
        //@var string ノードのid
        let nodeId = id;
        if(nodeId === 'sb'){
          //移動命令
          window.location = `http://localhost:8000/pssb01/`;
        }else{
          //移動命令
          window.location = `http://localhost:8000/pssb01/show/${clientId}/${nodeId}`;
        }
      }else if(id.substr(0, 2) === 'ta'){
        //@var string Laravelのセッションid
        let clientId = document.getElementById('hidden_client_id').value;
        //@var string ノードのid
        let nodeId = id;
        //移動命令
        window.location = `http://localhost:8000/show/${clientId}/${nodeId}`;
      }else if(id.substr(0, 2) === 'ss'){
        //@var string Laravelのセッションid
        let clientId = document.getElementById('hidden_client_id').value;
        //@var string ノードのid
        let nodeId = id;
        //システム設計への移動は、ログ画面に移動する
        if(nodeId === 'ss'){
          //移動命令
          window.location = `http://localhost:8000/pslg/`;
        }else if(nodeId === 'ssnw'){
          //移動命令
          window.location = `http://localhost:8000/psnw01/`;
        }else if(nodeId === 'sslg'){
          //移動命令
          window.location = `http://localhost:8000/pslg/`;
        }
      }
    }
  
    let chain = [];
    let createChainBar = function createChainBar(title){
      if(title !== '作業管理システム'){
        Object.keys(bar).forEach(key => {
          if(bar[key].includes(title)){
            chain.push(title);
            createChainBar(key);
          }
        });
      }else{
        chain.push(title)
      }
    };
  //パンくずリストに表示する次の要素を画面移動の前に保存する
  window.addEventListener('beforeunload', () => {
    localStorage.setItem('next', next);
  });
  
  //変数barにパンくずリスト階層のオブジェクトデータを作成、代入する
  createBarinfo();
  //次の要素を取得する
  next = localStorage.getItem('next');
  //@var string パス
  let pathname = document.location.pathname;
  if(pathname === '/'){
    next = 'bs00000001.部署A';
  }else if(pathname.split('/')[1] === 'show'){
    //@var string パスの最後のid
    let next_id = pathname.split('/')[3];
    bar_info.forEach(block => {
      block.forEach(info => {
        if(Object.keys(info)[0].split('.')[0] === next_id){
          next = Object.keys(info)[0];
        }
        if(Object.values(info)[0].split('.')[0] === next_id){
          next = Object.values(info)[0];
        }
      })
    });
  }
  //次の要素がない(初期表示の場合)
  if(!next){
    next = 'bs00000001.部署A';
  }
  //リストに表示する階層の要素をchainに代入する
  createChainBar(next);
  //パンくずリストのdom作成
  createBar(chain.reverse());
  })(treeChain);
}
  export {HierarchyBar}