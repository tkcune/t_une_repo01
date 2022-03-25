import {clipboard} from './ptcmcb';
import { findMobile } from './ptcmrd'

//@var TreeActionクラス 公開するクラス,ツリー機能クラス
let TreeAction = {};

//TreeActionの名前空間
//ツリー作成クラス
TreeAction.createTree = {};
//ノードのdomなどを作成するクラス
TreeAction.node = {};
//ツリーの探索やcss名を決定するクラス
TreeAction.chainparser = {};

//ノードクラス
TreeAction.node = class Node {
    
  //@param string dir ツリーのディレクトリ
  //@param int id ノードのid
  constructor(nodeDir, id) {

    //@var string dir ツリーのディレクトリ
    this.dir = nodeDir;

    //@var string id データベースのid
    this.id = id;

    //@var string className htmlのcss名となる
    this.className;

    //@var string title dom要素に表示する文字
    this.title = nodeDir.split('/').pop();

    //@var array child 子ノードクラスを格納する配列
    this.child = [];

    //@var dom element ノードのdom要素
    this.element = null;

    //@var array toLink 投影先のリンク、idとディレクトリ
    this.toLink = [];

    //@var array fromLink 投影元のリンク、idとディレクトリ
    this.fromLink = [];

    //@var boolean hide 隠蔽/表示を切り替える
    this.hide = false;
  }

  //ツリーノードのdomを作成する
  //@return dom node.element 作成したツリーのdom
  createTree(){

    //domを作成して、node.elementに代入する。
    this.createElement();

    if(this.child !== []){

      //子要素がある子要素のdomを追加する
      this.child.forEach(child => {

        //Node.createTree(child)によって子要素のdomを作成しつつ、
        //返り値の子要素のdomを追加する
        this.appendTree(child.createTree());
      });
    }
    return this.element;
  }

  //ノードのdom要素を生成する。
  createElement() {

    //展開されるツリーのdom生成
    if(this.className === 'expandtree' || this.className === 'lastexpandtree'){

      this.createExpandTree();

    }else if(this.className === 'linetree'){
      //linetreeのdom生成

      this.createLineTree();

    }else{
      //expandtreeとlinetree以外のdom生成
      //secondtree,endtreeはlinetreeの単要素
      //first,normaltree,lastnormaltree,lastreeはexpandtreeの単要素

      this.createLeafTree();

    }
  }

  //expandtree系を作成
  createExpandTree(){
    //div要素を生成する。
    let div = this.createFirstdiv();
      
    //展開するボタンのあるツリー生成、挿入する
    div.appendChild(this.createTreeBox());
  
    //展開されるツリーを作成する
    //@var dom ul ul要素
    let ul = document.createElement('ul');
  
    //@var dom li li要素
    let li = document.createElement('li');

    //li要素をulに追加
    ul.appendChild(li);
  
    //クラスのdom要素のthis.elementフィールドに、データのdom要素を保存する。
    div.appendChild(ul);
    this.element = div;
  }

  //linetreeを生成する。
  createLineTree(){
    //linetreeのdom生成

    //div要素の生成する
    let div = this.createFirstdiv();

    if(this.title === 'マイツリー'){
      div.innerText = 'マイツリー';
    }
    
    this.element = div;
  }

  //expandtree系やlinetree以外のdomを生成する
  createLeafTree(){
    //expandtreeとlinetree以外のdom生成
    //secondtree,endtreeはlinetreeの単要素
    //first,normaltree,lastnormaltree,lastreeはexpandtreeの単要素

    //@var dom ul ul要素
    let ul = document.createElement('ul');
      
    //classの追加により、cssを適用させる
    ul.classList.add(this.className);
      
    //@var dom li li要素
    let li = document.createElement('li');
    
    //segmentというdiv要素を加える
    li.appendChild(this.createSegment());
      
    ul.appendChild(li);

    this.element = ul;
  }

  //ノードクラスにdomを最後に追加
  //@param dom element 追加するdom要素
  appendTree(element){
    
    //展開されるツリーのdom挿入
    if(this.className === 'expandtree' || this.className === 'lastexpandtree'){

      //@var dom li expandtree系の子要素のdomの最初親要素はli
      let li = this.element.children[1].children[0];
      li.appendChild(element);

    }else if(this.className === 'linetree'){
      //linetreeのdom挿入

      //linetree系は、div要素の並びなのでそのままdomを追加する
      this.element.appendChild(element);

    }
  }

  //ボックスのボタンとタイトルのdom要素を生成する。
  //@return dom divTreeBox ボックスのボタンとタイトルがあるdom要素
  createTreeBox() {

    //@var dom divTreeBox 返すdom要素
    //div要素の生成
    let divTreeBox = document.createElement('div');
    
    //class名を付ける
    divTreeBox.classList.add('treebox');
    
    //二つのdom要素を生成して、挿入する
    //expandboxは、プラス、マイナスのボタンがあるボックス
    //titleboxは、ボタンの横にあるタイトル
    divTreeBox.appendChild(this.createExpandBox());
    divTreeBox.appendChild(this.createTitleBox());

    return divTreeBox;
  }

  //expandboxの展開されるボックスの生成
  //@return dom div 展開されるボックスのdom
  createExpandBox(){
    //@var dom div divTreeBoxに追加するdom
    let div = document.createElement('div');
    //boxValueの0番目に、クラス名
    div.classList.add('expandbox');
      
    //boxValueの1番目に、表示する文字。
    div.innerText = '-';
    
    //ボックスを展開するクリックイベントを付ける
    this.addExpandEvent(div);

    return div;
  }

  //@param dom div クリックイベントを付けるdom
  //クリックイベントを付ける
  addExpandEvent(div){
    //@param イベント　event
    div.addEventListener('click',(event) => {

      //@var dom box treeBoxのexpandboxのdom
      let box = this.element.children[0].children[0];
  
      //プラス,マイナス文字を反転する。
      if(box.innerText === '+'){
        box.innerText = '-';
        //ツリーのhtmlの表示に切り替える
        this.openDisplayChild();
      }else if(box.innerText === '-'){
        //子要素全体を非表示にする
        this.noneDisplayTree();
      }
      
    });
  }

  //titleboxの生成
  //@return dom div 展開ボックスのタイトルのdom
  createTitleBox(){
    //@var dom div divTreeBoxに追加するdom
    let div = document.createElement('div');
    //boxValueの0番目に、クラス名
    div.classList.add('titlebox');

    //titleboxにsegmentを追加する
    div.appendChild(this.createSegment());

    return div;
  }

  //segmentというclass名をdiv要素を作成する
  createSegment(){
    //@var dom imgとタイトルを格納する
    let segment = document.createElement('div');

    //segmentというcss名を追加する
    segment.classList.add('segment');
      
    //@var string アイコンの名前
    let img = this.getImg();

    //div要素に、アイコンとタイトルを挿入する
    segment.innerHTML =  img + this.title;

    //クリック処理を追加する
    segment.addEventListener('click', {node: this, handleEvent: this.displayDetail});

    return segment;
  }

  //詳細行に表示
  displayDetail() {
    //@var array dirArray ディレクトリの文字を分割した配列
    let dirArray = this.node.dir.split('/');
    //一番後ろの文字を削除
    dirArray.pop();
    //親要素のタイトルを取得する
    let topDir = dirArray.pop();

    //フォーカス
    this.node.focus();
    
    //クリップボードに選択したノードクラスのidとディレクトリを保存する
    clipboard.select(this.node.dir, this.node.id);

    //選択したノードがカレントノード
    clipboard.current(clipboard.getSelectDir(), clipboard.getSelectId());

    //選択したノードの親要素がマイツリーである場合は、本来のノードクラスまでツリーを開く
    if(topDir === "マイツリー"){
      //@var string id 選択したノードクラスのid
      let id = this.node.id;
      //@var Nodeクラス selectNode 本来のノードクラス
      let selectNode = this.node.prototype.chainparser.searchNodeId(id, this.node.prototype.tree);
      //本来のノードクラスまでツリーを開く
      selectNode.openBottomUpTree();
    }

    if(this.node.id === 'sslg'){
      //ログ確認の場合
      window.location = 'http://localhost:8000/pslg';
    }else if(this.node.id === 'ssnw'){
      window.location = 'http://localhost:8000/psnw01';
    }else if(this.node.id.substr(0, 2) === 'ji' || this.node.id.substr(0, 2) === 'bs'){
      //@var string Laravelのセッションid
      let clientId = document.getElementById('hidden_client_id').value;
      //@var string ノードのid
      let nodeId = this.node.id;
      //移動命令
      window.location = `http://localhost:8000/show/${clientId}/${nodeId}`;
    }else if(this.node.id.substr(0, 2) === 'kb'){
      //@var string Laravelのセッションid
      let clientId = document.getElementById('hidden_client_id').value;
      //@var string ノードのid
      let nodeId = this.node.id;
      if(nodeId === 'kb'){
        //移動命令
        window.location = `http://localhost:8000/pskb/`;
      }else{
        //移動命令
        window.location = `http://localhost:8000/pskb/show/${clientId}/${nodeId}`;
      }
    }else if(this.node.id.substr(0, 2) === 'sb'){
      //@var string Laravelのセッションid
      let clientId = document.getElementById('hidden_client_id').value;
      //@var string ノードのid
      let nodeId = this.node.id;
      if(nodeId === 'sb'){
        //移動命令
        window.location = `http://localhost:8000/pssb01/`;
      }else{
        //移動命令
        window.location = `http://localhost:8000/pssb01/show/${clientId}/${nodeId}`;
      }
    }else if(this.node.id.substr(0, 2) === 'ta'){
      //@var string Laravelのセッションid
      let clientId = document.getElementById('hidden_client_id').value;
      //@var string ノードのid
      let nodeId = this.node.id;
      //移動命令
      window.location = `http://localhost:8000/show/${clientId}/${nodeId}`;
    }
  }

  //選択したノードを太字にする
  focus(){

    //@var string beforeSelectId 前回選択したid
    let beforeSelectId = clipboard.getCurrentId();
    //@var Nodeクラス beforeSelect　前回選択したノードクラス
    let beforeSelect = this.prototype.chainparser.searchNodeId(beforeSelectId, this.prototype.tree);
    
    //前回選択したノードクラスのフォーカスを消す
    beforeSelect?.outFocusNode();

    //@var array dirArray ディレクトリを分割した配列
    let dirArray = this.dir.split('/');
    //一番後ろの文字を削除
    dirArray.pop();
    //親要素のタイトルを取得する
    //@var string topDir 第一階層のタイトル
    let topDir = dirArray.pop();

    if(topDir === "マイツリー"){
      //@var string id マイツリーに登録した本来のツリーノードのid
      let id = this.id;
      //@var Nodeクラス selectNode 本来のノードクラス
      let selectNode = this.prototype.chainparser.searchNodeId(id, this.prototype.tree);
      //本来のノードをフォーカスする
      selectNode.onFocusNode();
    }else{
      //選択したノードクラスをフォーカスする
      this.onFocusNode();
    }
  }

  //フォーカスする(選択したタイトルを太字にする)
  onFocusNode(){
    //div要素の場合とul要素の場合を分ける
    if(this.element.nodeName === 'DIV'){
      this.element.children[0].children[1].classList.add('focus');
    }else{
      this.element.children[0].classList.add('focus');
    }
  }

  //フォーカスを外す(選択したタイトルの太字を戻す)
  outFocusNode(){
    //div要素の場合
    if(this.element.nodeName === 'DIV'){
      this.element.children[0].children[1].classList.remove('focus');
    }else{
      this.element.children[0].classList.remove('focus');
    }
  }

  //投影先のタイトルを斜体にする
  onSync(){
    //div要素の場合
    if(this.element.nodeName === 'DIV'){
      this.element.children[0].children[1].classList.add('sync');
    }else{
      this.element.children[0].classList.add('sync');
    }
  }

  //目的のノードクラスまでツリーを開く
  //@var Nodeクラス tree ツリーの全体のインスタンス
  openBottomUpTree(){

    //@var dom box treeBoxのexpandboxのdom
    let box = this.element.children[0].children[0];
    
    //@var array splitDir ディレクトリの文字を分割した配列
    let splitDir = this.dir.split('/');
    //最後の文字を削除する
    splitDir.pop();
    
    //配列が1の時は、chaintreeなので、openBottomUpTreeを呼ばない
    if(splitDir.length !== 1){
      //@var Nodeクラス palent 目的のノードクラスの親要素
      let palent = this.prototype.chainparser.searchNodeDir(splitDir.join('/'), this.prototype.tree);
      
      //目的のノードクラスの親要素のツリーを開く
      //親要素が閉じているなら開く
      if(palent.element.children[0].children[0].innerText === "+"){
        //目的のノードクラスの親要素のツリーを開く
        palent.openBox();
        palent.openDisplayChild();
        //また親要素を引数にして再帰的に、openBottomUpTreeを呼び出す
        palent.openBottomUpTree();
      }
    }
  }

  //目的のノードクラスまでツリーを開く
  //@var Nodeクラス tree ツリーの全体のインスタンス
  openBottomUpTreePageMove(){

    //@var dom box treeBoxのexpandboxのdom
    let box = this.element.children[0].children[0];
    
    //@var array splitDir ディレクトリの文字を分割した配列
    let splitDir = this.dir.split('/');
    //最後の文字を削除する
    splitDir.pop();
    //子ノードを開く
    this.openBox();
    this.openDisplayChild();
    
    //配列が1の時は、chaintreeなので、openBottomUpTreeを呼ばない
    if(splitDir.length !== 1){
      //@var Nodeクラス palent 目的のノードクラスの親要素
      let palent = this.prototype.chainparser.searchNodeDir(splitDir.join('/'), this.prototype.tree);
      
      //親要素が閉じているなら開く
      if(palent.element.children[0].children[0].innerText === "+"){
        //目的のノードクラスの親要素のツリーを開く
        palent.openBox();
        palent.openDisplayChild();
        //また親要素を引数にして再帰的に、openBottomUpTreeを呼び出す
        palent.openBottomUpTree();
      }
    }
  }

  //現在のスパイラルでは使わない
  //ツリーを閉じる
  // closeBottomUpTree(){
  //   //@var array splitDir ディレクトリの文字を分割した配列
  //   let splitDir = this.dir.split('/');
  //   //最後の文字を削除する
  //   splitDir.pop();
  //   //配列が1の時は、chaintreeなので、openBottomUpTreeを呼ばない
  //   if(splitDir.length !== 1){
  //     //@var Nodeクラス palent 目的のノードクラスの親要素
  //     let palent = this.prototype.chainparser.searchNodeDir(splitDir.join('/'), this.prototype.tree);
  //     //目的のノードクラスの親要素のツリーを閉じる
  //     palent.closeBox();
  //     palent.noneDisplayNode();
  //     //また親要素を引数にして再帰的に、closeBottomUpTreeを呼び出す
  //     palent.closeBottomUpTree();
  //   } 
  // }
  //現在のスパイラルでは使わない

  //@param Nodeクラス node 非表示にするノードクラス
  //子要素全体を非表示にする
  noneDisplayTree() {

    //expandtree系のノードの場合
    if(this.className === 'expandtree' || this.className === 'lastexpandtree'){
      this.closeBox();
    }

    //子要素を非表示にする
    this.child.forEach(child => {
      child.noneDisplayNode();
      child.noneDisplayTree();
    });
  }

  //palentのchildのelementを非表示にする。
  noneDisplayNode(){

    //展開するツリーボックスの場合
    if(this.className === 'expandtree' || this.className === 'lastexpandtree'){
      
      //@var Nodes配列 childNodes 
      let childNodes = this.element.childNodes;
      //展開するボックスの子要素(treeboxなど)を表示、非表示にする
      for(let i = 0; i < childNodes.length; i++){
        childNodes[i].classList.add('unexpand');
      }
    }else{
      //展開するツリーボックス以外のノードの場合
      this.element.classList.add('unexpand');
    }

  }

  //子要素のツリーを表示する
  openDisplayChild(){
    this.child.forEach(child => {
      
      //隠蔽ではないノードなら画面に表示する
      if(child.hide === false){
        child.openDisplayNode();
      }
    });
  }
  //ツリーのボックスを展開する
  openDisplayNode(){

    //展開するツリーボックスの場合
    if(this.className === 'expandtree' || this.className === 'lastexpandtree'){
      
      //@var Nodes配列 childNodes 
      let childNodes = this.element.childNodes;
      //展開するボックスの子要素(treeboxなど)を表示、非表示にする
      for(let i = 0; i < childNodes.length; i++){
        childNodes[i].classList.remove('unexpand');
      }
    }else{
      //展開するツリーボックス以外のノードの場合
      this.element.classList.remove('unexpand');
    }
  }

  //ボックスを開く
  openBox(){

    //子要素があるなら
    if(this.child.length !== 0){
      //@var dom box treeBoxのexpandboxのdom
      let box = this.element.children[0].children[0];
  
      //プラス文字を反転する。
      if(box.innerText === '+'){
        box.innerText = '-';
      }
    }
  }

  //ボックスを閉じる
  closeBox(){

    //子要素があるなら
    if(this.child.length !== 0){
      //@var dom box treeBoxのexpandboxのdom
      let box = this.element.children[0].children[0];
  
      //マイナス文字を反転する。
      if(box.innerText === '-'){
        box.innerText = '+';
      }
    }
  }

  //expnadtreeやlinetreeの最初のdivタグを作成する
  //@return dom div classNameがcss名のdiv
  createFirstdiv() {
    
    //var dom div 返すdiv
    let div = document.createElement('div');
    div.classList.add(this.className);
    return div;
  }

  //ノードのidから表示するsrcの値を取得する
  //@return string src
  getImg(){
    //@var stirng 返すアイコンの名前
    let src = '';
    if(this.fromLink.length !== 0){
      src = this.fromLink[0].substr(0, 2);
    }else{
      src = this.id.substr(0, 2);
    }
    
    if(src === 'ji'){
      // src = '<img width = "15" length = "17" src="/image/ji.png">';
      //人事の場合
      src = '<img width = "15" length = "17" src="data:image/png;base64,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aYQIQgQhggozguhEiODhDoQIQgQhgtS6UGFGiCBERAq1ESJOaYQIQgQhggozguhEiODhDoQIQgQhgtS6UGFGiCBERAq1ESJa41E0x38JDaQGLprQym7w15In3fneLzU89Gv/0ECIeOcn//308L3ww8/8XQP/zwgRypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERyoLxQYiHKnvlt/5oA/9kBynETqmJPSBEXPnQVxp+7A9fGhH/3AcRyoIRIuKhyggRhAg765Wa2ANCBCGCe4+CEBGhLBghIh6qjBBBiLCzXqmJPSBEECK49ygIERHKghEi4qHKCBGECDvrlZrYA0IEIYJ7j4IQEaEsGCEiHqqMEEGIsLNeqYk9IEQQIrj3KAgREcqCESLiocoIEYQIO+uVmtgDQgQhgnuPghARoSwYISIeqowQQYiws16piT0gRBAiuPcoCBERyoIRIuKhyggRhAg765Wa2ANCBCGCe4+CEBGhLBghIh6qjBBBiLCzXqmJPSBEECK49ygIERHKghEi4qHKCBGECDvrlZrYA0IEIYJ7j4IQEaEsGX/xiOYIEYQI/r0O/Nu/QRAiLv/y3zQ88dxLI+J/VAURSm3EVvJrNEeIIEQQIrj3KAgRhAjuPQpCRIRSG7GV/BrNESIIEYQI7j0KQgQhgnuPghARodRGbCW/RnOECEIEIYJ7j4IQQYjg3qMgREQotRFbya/RHCGCEEGI4N6jIEQQIrj3KAgREUptxFbyazRHiCBEECK49ygIEYQI7j0KQkSEUhuxlfwazREiCBGECO49CkIEIYJ7j4IQEaHURmwlv0ZzhAhCBCGCe4+CEEGI4N6jIEREKLURW8mv0RwhghBBiODeoyBEECK49ygI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">';
    }else if(src === 'bs'){
      // src = '<img width = "15" length = "17" src="/image/bs.png">';
      //部署の場合
      src = '<img width = "15" length = "17" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAOAAAADSCAIAAADc/VsQAAAABGdBTUEAALGPC/xhBQAAAAlwSFlzAAAOwwAADsMBx2+oZAAABURJREFUeF7t3b2KXVUYx+EZGcHGOxARJZ1Y2KXzA7yBkEJNFSwFK5s0IY2NlWApqaIWITcg+NGlsxC7oIh4BzaCA5MBl7CdvGTWcc3e+3/2PE/jKmYmZ7Y/Drwv6ySHJycnB5DqmfZfiCRQogmUaAIlmkCJJlCiWTNtzeHhYTvNbJlyvIMSTaBEEyjRBEo0gRItcYofnEP3fS8x+OsfXb/RTjM7vn+vnc4z8n/EOyjRBEo0gRJNoEQTKNEESrTl1kz925PBRUm5/ih/zf6XNNNTKl/AYnuiORz/8Us7TT182A4TnY/UOyjRBEo0gRJNoEQTKNFGp/jFZvNB5WhfvqQLn0OfYq+n+PpBlUzxbJVAiSZQogmUaAIlmkCJtsOaaXs3G0qrr5kOrl5th82wZmKrBEo0gRJNoEQTKNFWnuJ3uHDQr5oZy9H46IVX2mliySm+1H//JlG5gjDFs1UCJZpAiSZQogmUaAIl2ixrpv5NzRz+evRFO008d+WDdprqv5YxvGbqXx6Vr39f1M+5Ys3EFgiUaAIlmkCJJlCijU7x/QKH0x1G+3mm+MFnUr7+dX9m+e1/fvtmO008//b37TTx5CP1Dko0gRJNoEQTKNEESjSBEq1eM82xE9kXF37d4R/lIy33L8+++H47bcXfv3/ZThPWTGyBQIkmUKIJlGgCJZpAibbDmmmOnUjgfZzSyKLkKba3zrvwB+UdlGgCJZpAiSZQogmUaKOfSdredYdyDi2tPsWvuwOZabNxhndQogmUaAIlmkCJJlCiCZRoO6yZSnt93aF/UVIafHSn5lgzzcGaCWoCJZpAiSZQogmUaBuc4tOuO+yq/1LOupZ5UN5BiSZQogmUaAIlmkCJJlCiJa6ZBu9A9G+USquvmUrlc+7X/y8V9VvmmXgHJZpAiSZQogmUaAIl2ugUX+ofORe7A9E/sc6x1ljSdz981k4Tb73xUTtN9H/lYp58+N5BiSZQogmUaAIlmkCJJlCizbJm6je4kFpseVQqX3y5uxk3uCd679Y37TTx1SfvtNNE/8+8e/fDdpp46eUr7XSe33591E4TN29+3k7/8g5KNIESTaBEEyjRBEo0gRKtXjOteyXn6wcP2mni3WvX2mmi/ysXM75m6t8olQavIw3+Qf3fbs3EFgiUaAIlmkCJJlCi1dN6OR0PGhzDj67faKeJ4/v32mmi/2d+fPtOO028/tqr7XSeH3/6uZ0mPr1zu53+a3C67x+Zy690WQRmIVCiCZRoAiWaQIkmUKLNsmbq3yiVBm97DP5B/d++5Jqp1H8vpH/NVBrcKJWsmdgCgRJNoEQTKNEESjSXRc5yWaSdJvr/9FLnwH7KX2DLnhEo0QRKNIESTaBEEyjRllszlfrvhfSvmUqDG6WSyyLtdB5rJjZLoEQTKNEESjSBEu1SXxYZ/DUzp/jB2x6D90JKpng2S6BEEyjRBEo0gRJNoERzWeT/W33NVBrcKK34l4icsmZizwiUaAIlmkCJJlCi7fCPyQbeIFnsXkgpc4ovLTawl0zxbJZAiSZQogmUaAIlmkCJtvKaqTS4UVr3Xkj5PE+Vj3Td3dMcRjZKJe+gRBMo0QRKNIESTaBEEyjREtdMpcXuKJWsmTpZM3G5CJRoAiWaQIkmUKLtzRS/LlN8J1M8l4tAiSZQogmUaAIlmkCJJlCiCZRoAiWaQIkmUKIJlGgCJZpAiSZQogmUaAIlmkCJJlCi1Z9JKl2SDyqNf/yo5DNJZ/hMElsgUKIJlGgCJZpAiSZQogmUaAIlmkCJJlCiCZRoAiXYwcFjJESfW9VsV0IAAAAASUVORK5CYII=' + '">';
    }else if(src === 'ur'){
      // src = '<img width = "15" length = "17" src="/image/ur.png">';
      //ユーザー情報の場合
      src = '<img  width = "15" length = "17" src="data:image/png;base64,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">';
    }else if(src === 'lo'){
      // src = '<img width = "15" length = "17" src="/image/lo.png">';
      //ログアウトの場合
      src = '<img width = "15" length = "17" src="data:image/png;base64,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">';
    }else if(src === 'ss'){
      // src = '<img width = "15" length = "17" src="/image/ss.png">';
      //システム設計の場合
      src = '<img width = "15" length = "17" src="data:image/png;base64,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">';
    }else if(src === 'kb'){
      // src = '<img width = "15" length = "17" src="/image/ss.png">';
      //システム設計の場合
      src = '<img width = "15" length = "17" src="data:image/png;base64,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">';
    }else if(src === 'sb'){
      // src = '<img width = "15" length = "17" src="/image/ur.png">';
      //システム設計の場合
      src = '<img width = "15" length = "17" src="data:image/png;base64,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aYQIQgQhggozguhEiODhDoQIQgQhgtS6UGFGiCBERAq1ESJOaYQIQgQhggozguhEiODhDoQIQgQhgtS6UGFGiCBERAq1ESJa41E0x38JDaQGLprQym7w15In3fneLzU89Gv/0ECIeOcn//308L3ww8/8XQP/zwgRypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERyoLxQYiHKnvlt/5oA/9kBynETqmJPSBEXPnQVxp+7A9fGhH/3AcRyoIRIuKhyggRhAg765Wa2ANCBCGCe4+CEBGhLBghIh6qjBBBiLCzXqmJPSBEECK49ygIERHKghEi4qHKCBGECDvrlZrYA0IEIYJ7j4IQEaEsGCEiHqqMEEGIsLNeqYk9IEQQIrj3KAgREcqCESLiocoIEYQIO+uVmtgDQgQhgnuPghARoSwYISIeqowQQYiws16piT0gRBAiuPcoCBERyoIRIuKhyggRhAg765Wa2ANCBCGCe4+CEBGhLBghIh6qjBBBiLCzXqmJPSBEECK49ygIERHKghEi4qHKCBGECDvrlZrYA0IEIYJ7j4IQEaEsGX/xiOYIEYQI/r0O/Nu/QRAiLv/y3zQ88dxLI+J/VAURSm3EVvJrNEeIIEQQIrj3KAgRhAjuPQpCRIRSG7GV/BrNESIIEYQI7j0KQgQhgnuPghARodRGbCW/RnOECEIEIYJ7j4IQQYjg3qMgREQotRFbya/RHCGCEEGI4N6jIEQQIrj3KAgREUptxFbyazRHiCBEECK49ygIEYQI7j0KQkSEUhuxlfwazREiCBGECO49CkIEIYJ7j4IQEaHURmwlv0ZzhAhCBCGCe4+CEEGI4N6jIEREKLURW8mv0RwhghBBiODeoyBEECK49ygI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">';
    }

    return src;
  }
}

//Chainparserクラス
//@return ChainParserクラス チェインパーサーのクラス
TreeAction.chainparser = (() => {

      //一番上だけのclassNameを決定する。
      //linetreeか、expandtreeか決定する
      //子要素に子要素がなければ、linetree
      //それ以外は、expnadtree
      //@param Nodeクラス topNode 一番上のノード
      let decisionTreeClass = function decisionTreeClass(topNode) {
      
        let isExpand = false;
       if(topNode.id !== '0'){
         isExpand = true;
       }

        //子要素があれば、展開されるボックスのあるツリー(expandtree)
        if(isExpand === true){
          topNode.className = 'expandtree'
        }else if(isExpand === false){
          topNode.className = 'linetree';
        }
      }

      //同じノードオブジェクトか比較。
      //@param Nodeクラス node 比較される
      //@param Nodeクラス compairNode 比較する
      //@return boolean 結果
      //idが同じであれば、同じノード
      let isEqual = function isEqual(node, compairNode) {
      
        if(node.id !== compairNode.id){
          return false;
        }

        return true;
      }

      //子要素のクラス名を決定していく。
      //@param Nodeクラス node 階層のノード
      //@param Nodeクラス palent nodeの親ノード
      let decisionChildClass = function decisionChildClass(node, palent) {
      
        //linetreeの場合
        if(node.className === 'linetree') {

          //メソッドの入力のノードと親ノードの最後のノードが等しければ
          if(isEqual(node, palent.child[palent.child.length - 1]) === true){
            decisionLineTreeClass(node, true);
          }else{
            decisionLineTreeClass(node, false);
          }

          //expandtree系の場合
        }else if(node.className === 'expandtree' || node.className === 'lastexpandtree'){

          //親ノードがツリーの一番上chaintreeの場合
          if(palent.dir === ''){

            //メソッドの入力のノードと親ノードの最後のノードが等しければ、最後のノード。
            if(isEqual(node, palent.child[palent.child.length - 1]) === true){
              node.className = 'lastexpandtree';
            }else{
              node.className = 'expandtree';
            }
            //nodeのcssを決定していく。
            decisionExpandTreeClass(node);
          }else{
          
            //palentからcssを作り直し。
            decisionExpandTreeClass(palent)
          }
        }
      }

      //lientreeの子要素のクラスの名前を決める
      let decisionLineTreeClass = function decisionLineTreeClass(node, isLastFlag){
        for(let i = 0; i < node.child.length; i++){
          //全体の中の最後のツリーでかつ子要素の最後はendtree
          if(isLastFlag === true && i === node.child.length - 1){
            node.child[i].className = 'endtree';
          }else{
            node.child[i].className = 'secondtree';
          }
        }
      }

      //expnadtreeの子要素のクラス名を決める
      let decisionExpandTreeClass = function decisionExpandTreeClass(node) {
      
        //上から下にツリークラスを決定していく
        for(let i = 0; i < node.child.length; i++){
          //expnadtreeの最初のツリー
          if(i === 0){
            //子要素の子要素が0より多ければ、展開されるので、expandtree
            if(node.child[i].child.length > 0){
              //子要素が1であるならば展開されるexpandtreeが最後
              if(node.child.length === 1){
                node.child[i].className = 'lastexpandtree';
              }else{
                node.child[i].className = 'expandtree'
              }
            }else{
              //子要素の子要素がなければ、子要素は展開されない
              if(i === node.child.length - 1){
                //最初の子要素でかつ、要素がひとつなら
                node.child[i].className = 'lastnormaltree';
              }else{
                //最初の要素で、続きがあるなら
                node.child[i].className = 'firsttree';
              }
            }
            //最後の要素かつ i が0以上
          }else if(i === node.child.length - 1 && i > 0){
            if(node.child[i].child.length > 0){
              //展開されるツリーの場合
              node.child[i].className = 'lastexpandtree';
            }else{
              //nodeの子要素がひとつだけではなくかつ最後の子要素の場合
              node.child[i].className = 'lasttree'
            }
          }else {
            //最初の子要素でも最後の子要素でもない
            if(node.child[i].child.length > 0){
              //展開されるツリーの場合
              node.child[i].className = 'expandtree';
            }else{
              //nodeの子要素がひとつだけではなくかつ最後の子要素の場合
              node.child[i].className = 'normaltree'
            }
          }

          //展開するボックスについて再帰的に、決定していく
          if(node.child[i].child.length !== 0){
            //子要素があるなら、子要素のクラス名を決定していく。
            decisionExpandTreeClass(node.child[i]);
          }
        }
      }

      //@param string nodeDir ノードクラスのディレクトリ
      //@param Nodeクラス node chaintreeのノードクラス
      //@return Nodeクラス search 検索したノードクラス
      //ノードのディレクトリからノードクラスを探索
      let searchNodeDir = function searchNodeDir(nodeDir, node){
      
        //ツリーの一番上chaintreeの場合
        //本来のchaintree.dirは '' 空文字
        //探索する時に.join('/')しているので、'/'を追加
        if(nodeDir === '/' || nodeDir === ''){
          return node;
        }
      
        //ノードクラスをnodoと子要素ごとに一列配列に展開する
        //@var array nodeArray 展開したノードクラスを格納する配列
        let nodeArray = concatNode(node);

        //@var Nodeクラス search 検索したノードを入れる
        let search;

        nodeArray.forEach(child => {
          if(child.dir === nodeDir){
            search = child;
          }
        });
        return search;
      }

      //idからノードクラスを検索する
      //@param string nodeId 探索するノードクラスのid
      //@param Nodeクラス node chaintreeのノードクラス
      //@return Nodeクラス search 検索したノードクラス
      let searchNodeId = function searchNodeId(nodeId, node){
      
        //ノードクラスをnodoと子要素ごとに一列配列に展開する
        //@var array nodeArray 展開したノードクラスを格納する配列
        let nodeArray = concatNode(node);

        //var Nodeクラス search 検索したノードクラスを入れる
        let search;

        nodeArray.forEach(child => {
          if(child.id === nodeId){
            search = child;
          }
        });
        return search;
      }

      //現在のスパイラルでは使わない
      //ディレクトリとidからNodeクラスを検索する
      //@param string nodeDir ノードのディレクトリ
      //@param string id ノードクラスのid
      //@param Nodeクラス node 検索されるノードクラス(インスタンスのtree)
      // let searchNodeDirId = function searchNodeDirId(nodeDir, nodeId, node){
      
      //   //@var array nodeArray 展開したノードクラスを格納した配列
      //   let nodeArray = concatNode(node);
      
      //   //@var Nodeクラス search 検索するノードを代入する
      //   let search;
      //   nodeArray.forEach(child =>{
        
      //     //ノードクラスのディレクトリとidが等しかったら
      //     if(child.dir === nodeDir && child.id === nodeId){
      //       search = child;
      //     }
      //   });
      //   return search;
      // }
      //現在のスパイラルでは使わない

      //@param Nodeクラス node 展開されるノード
      //@return array array ノードを展開した配列
      //ノードクラスを子要素も含めて一列展開する
      let concatNode = function concatNode(node){

        //var array array 返値 初期値はnode
        let array = [node];

        //子要素を取得する。
        //linetreeは除く。
        //子要素がないなら、returnする
        if(node.child.length > 0){
          node.child.forEach(child => {
          
            //concatNodeの返り値はarrayなので、変数のarrayと返り値concatする事で、一列の配列になる
            array = array.concat(concatNode(child));
          });
        }
        return array;      
      }

      //ノードの親要素を返す
      //@param string nodeId 親要素を探すノードのid
      //@param Nodeクラス node ツリーノードのインスタンス
      //@return Nodeクラス 親ノード
      let searchPalentNode = function searchPalentNode(nodeId, node){
        //@var Nodeクラス 親ノードを代入する
        let search = null;
        //@var Nodeクラス 子ノード
        let childNode = searchNodeId(nodeId, node);
        //ノード全体をループする
        concatNode(node).forEach(palent => {
          //子要素があるなら
          if(palent.child !== []){
            palent.child.forEach(child => {
              //親要素の中に子要素があるか、調べる
              if(isEqual(child, childNode)){
                search = palent;
              }
            });
          }
        });
        //結果を返す
        return search;
      }

      //@param Nodeクラス fromNode 投影元のノード
      //@param Nodeクラス toNode 投影先のノード
      //投影元と投影先をリンクさせる
      let syncLink = function syncLink(fromNode, toNode){
        //リンク元にリンク先のディレクトリを入れる
        //リンク先にリンク元のディレクトリを入れる
        fromNode.toLink.push(toNode.id);
        toNode.fromLink.push(fromNode.id);
      }

      //隠蔽ノードを表示に替える
      //@var Nodeクラス child 表示するノード
      //@var Nodeクラス palent 表示するノードの親ノード
      let displayOpen = function displayOpen(child, palent){
        //ノードを表示にする
        child.openDisplayNode();

        //隣のノードのcss名を変更する
        //子ノードが親ノードの先頭にあるなら
        if(getIndexNodeOpen(child, palent) === 0){
          //@var Nodeクラス 次のノードクラス
          let nextNode = getNextNode(child, palent);
          
          if(nextNode !== null){
            //firsttreeならば、次のノードは、normaltree
            if(nextNode.element.className === 'firsttree'){
              nextNode.element.className = 'normaltree';
              //lastnormalreeならば、次のノードは、最後なので、lasttree
            }else if(nextNode.element.className === 'lastnormaltree'){
              nextNode.element.className = 'lasttree'
            }
          }
          //子ノードが親ノードの最後にあるなら
        }else if(getIndexNodeOpen(child, palent) === getLengthChildOpen(palent)){
          //@var Nodeクラス 次のノードクラス
          let backNode = getBackNode(child, palent);
          if(backNode !== null){
            //lastexpandtreeならば、前のノードは、expandtreeになる
            if(backNode.element.className === 'lastexpandtree'){
              backNode.element.className = 'expandtree';
              //lastnormaltreeならば、最初のtreeになる
            }else if(backNode.element.className === 'lastnormaltree'){
              backNode.element.className = 'firsttree';
              //lasttreeならば、normaltreeとなる
            }else if(backNode.element.className === 'lasttree'){
              backNode.element.className = 'normaltree';
            }
          }
        }else{
          //中間はchildを元に戻す
          child.element.className = child.className;
        }
      }

      //ノードを非表示にして、隣のノードの表示を変える
      //@var Nodeクラス child 非表示にするノード
      //@var Nodeクラス palent 非表示にするノードの親ノード
      let displayNone = function displayNone(child, palent) {
    
        //ノードを非表示にする
        child.noneDisplayNode();

        //隣のノードのcss名を変更する
        //子ノードが親ノードの先頭にあるなら
        if(getIndexNodeHide(child, palent) === 0){
          //@var Nodeクラス 次のノードクラス
          let nextNode = getNextNode(child, palent);
          //次ノードクラスがあるなら
          if(nextNode !== null){
            //normaltreeならば、次のノードは、先頭になるので、firstree
            if(nextNode.element.className === 'normaltree'){
              nextNode.element.className = 'firsttree';
            //lastreeならば、次のノードは、1つしかないので、lastnormaltree
            }else if(nextNode.element.className === 'lasttree'){
              nextNode.element.className = 'lastnormaltree';
            }
          }
        //子ノードが親ノードの最後にあるなら
        }else if(getIndexNodeHide(child, palent) === getLengthChild(palent)){
          //@var Nodeクラス 次のノードクラス
          let backNode = getBackNode(child, palent);
          //後ろのノードクラスがあるなら
          if(backNode !== null){
            //expandtreeならば、前のノードは、最後のexpandtreeになる
            if(backNode.element.className === 'expandtree'){
              backNode.element.className = 'lastexpandtree';
              //firsttreeならば、最後のnormaltreeになる
            }else if(backNode.element.className === 'firsttree'){
              backNode.element.className = 'lastnormaltree';
              //normaltreeならば、最後のtreeになる
            }else if(backNode.element.className === 'normaltree'){
              backNode.element.className = 'lasttree';
            }
          }
        }
      }

      //hideを抜いたノードクラスのインデックス
      //@param Nodeクラス node インデックスを探すノードクラス
      //@param Nodeクラス palent 親クラス
      //@return int インデックス
      let getIndexNodeHide = function getIndexNodeHide(node, palent){
        //@var int インデックス
        let index = 0;
        for(let i = 0; i < palent.child.length; i++){
          //隠蔽していて、目的のノードクラスか、調べる
          if(palent.child[i].hide === true && isEqual(node, palent.child[i]) === true){
              return index;
          }else if(palent.child[i].hide !== true){
              //見つからないなら、インデックスを増やす
              index++;
          }
        }
        return index;
      }

      //ノードクラスのインデックス
      //@param Nodeクラス node インデックスを探すノードクラス
      //@param Nodeクラス palent 親クラス
      //@return int インデックス
      let getIndexNodeOpen = function getIndexNodeOpen(node, palent){
        //@var int インデックス
        let index = 0;
        for(let i = 0; i < palent.child.length; i++){
          //隠蔽していて、目的のノードクラスか、調べる
          if(palent.child[i].hide === false && isEqual(node, palent.child[i]) === true){
              return index;
          }else if(palent.child[i].hide !== true){
              //見つからないなら、インデックスを増やす
              index++;
          }
        }
        return index;
      }

      //hideを抜いた親クラスのchildの数を数える
      //@param Nodeクラス palent 数える親クラス
      //@return int childの数
      let getLengthChild = function getLengthChild(palent){
        //@var int 数
        let length = 0;
        for(let i = 0; i < palent.child.length; i++){
          //隠蔽していないchildを数える
          if(palent.child[i].hide === false){
            length++
          }
        }

        return length;
      }

      //隠蔽から表示の時の子ノードの数を数える
      //@param Nodeクラス palent 親クラス
      //@return int 子ノードの数
      let getLengthChildOpen = function getLengthChildOpen(palent){
        //@var int 数
        let length = 0;
        for(let i = 0; i < palent.child.length; i++){
          //隠蔽していないchildを数える
          if(palent.child[i].hide === false){
            length++
          }
        }

        return length - 1;
      }

      //hideを除いた次のノードクラスを取得する。
      //@param Nodeクラス child　次の基準となるノードクラス
      //@param Nodeクラス palent 親クラス
      //@return Nodeクラス 次のノードクラスなければ、null
      let getNextNode = function getNextNode(child, palent){
        //@var int childのインデックス
        let index;
        //@var Nodeクラス 次のノードクラスを代入する
        let search = null;
        //childのインデックスを探す
        for(let i = 0; i < palent.child.length; i++){
          if(isEqual(child, palent.child[i]) === true){
            index = i;
          }
        }
        
        //indexの位置から、次のノードクラスを取得する(間の子ノードがhideがtrueでないノード)
        for(let i = index + 1; i < palent.child.length - 1; i++){
          if(palent.child[i].hide === false){
            search = palent.child[i];
            break;
          }
        }
        return search;
      }

      //hideを除いた後ろのノードクラスを取得する。
      //@param Nodeクラス child　次の基準となるノードクラス
      //@param Nodeクラス palent 親クラス
      //@return Nodeクラス 後ろのノードクラスなければ、null
      let getBackNode = function getBackNode(child, palent){
        //@var int childのインデックス
        let index;
        //@var Nodeクラス 後ろのノードクラスを代入する
        let search = null;
        //childのインデックスを探す
        for(let i = 0; i < palent.child.length; i++){
          if(isEqual(child, palent.child[i]) === true){
            index = i;
          }
        }

        for(let i = index - 1; i >= 0; i--){
          if(palent.child[i].hide === false){
            search = palent.child[i];
            break;
          }
        }

        return search;
      }

      return {
        searchPalentNode: searchPalentNode,
        concatNode: concatNode,
        searchNodeId: searchNodeId,
        searchNodeDir: searchNodeDir,
        decisionChildClass: decisionChildClass,
        isEqual: isEqual,
        decisionTreeClass: decisionTreeClass,
        syncLink: syncLink,
        displayOpen: displayOpen,
        displayNone: displayNone
      }
})();

//ツリーインスタンスとツリーのdomを作成する
//@param array treesepalete 上下関係のオブジェクトのデータの配列
//@param array projectionChain 投影データ
//@param Nodeクラス Node ノードクラス
//@param ChainParser chainparser チェインパーサーのクラス
//@return Nodeクラス ツリーインスタンス
TreeAction.createTree = function(treesepalete, projectionChain, Node, chainparser) {

  //ツリーの子要素のクラスを決定する。
  //@param Nodeクラス treeTop 第一階層のツリーノードクラス
  let decisionClass = function decisionClass(treeTop){
    treeTop.child.forEach(node => {
      chainparser.decisionChildClass(node, treeTop);
    });
  }

  //ツリーの子要素のdom要素を構築、追加していく。
  //@param Nodeクラス treeTop 第一階層のツリーノードクラス
  //@param dom fragment 仮のツリーのdom
  let createElement = function createElement(treeTop, fragment) {
    treeTop.child.forEach(node => {
      fragment.append(node.createTree());
    });
  }

  //@param array sepalete 上下関係だけのツリーのデータ構造
  //@param Nodeクラス treeTop　ツリーの一番上の親ノードのクラス
  //@return Nodeクラス topNode 親ノードクラス
  let createTopNode = function createTopNode(sepalete ,treeTop) {
    
    try {
      //@var Nodeクラス this.topNode ひとかたまりのツリーの一番上のNodeクラス
      let topNode;
    
      //topNodeを作成する
      //{'1.chaintree': '5.aa'}sepaleteのデータの中で、keyがchaintreeの値が一番上の親
      for(let i = 0; i < sepalete.length; i++){
        if(String(Object.keys(sepalete[i])[0]) === '1.chaintree'){
          topNode = new Node(treeTop.dir + '/' + String(Object.values(sepalete[i])[0].split('.')[1]), String(Object.values(sepalete[i])[0].split('.')[0]));
          topNode.prototype = {
            chainparser: chainparser,
            tree: treeTop
          };
          delete sepalete[i];
        }
      }

      //データからクラスの階層構造を作成する。
      parse(topNode, sepalete);

      //Nodeクラスの第一階層のcss名を決定する。
      chainparser.decisionTreeClass(topNode);

      treeTop.child.push(topNode);
    } catch (error) {
      console.log('tree_generate_data_is_bug');
    }

  }

  //nodeを再帰的に追加していく。
  //@param Nodeクラス node ノードの子要素を追加
  //@return Nodeクラス node 再起メソッドの返り値
  let parse = function parse(node, sepalete) {
    //nodeの子要素があるか、探索する
    sepalete.forEach(chain => {
      
      //chain変数のkeyに、親要素のidがある。
      //node.idが親要素のid
      if(node.id === Object.keys(chain)[0].split('.')[0]){

        //@var Nodeクラス child 子ノードクラス。parseで子ノードの子要素を追加する
        let child = new Node(node.dir + '/' + Object.values(chain)[0].split('.')[1], Object.values(chain)[0].split('.')[0]);
        child.prototype = {
          chainparser: chainparser,
          tree: node.prototype.tree
        };
        
        //子要素がある場合は、parseをもう一度呼び出す。
        //子要素の子要素を構築していく。
        node.child.push(parse(child, sepalete));
      }
    });
    return node;
  }

  //@param Nodeクラス treeTop ツリー全体のノード
  //@param array projectionChain 投影データの配列
  //ノードを投影
  let syncProjection = function syncProjection(projectionChain){
    //配列であるかをチェックする
    if(Array.isArray(projectionChain)){
      projectionChain.forEach(chain => {
        //列挙可能か、判断する(このロジックで正しいか、わからない)
        if(chain !== undefined && chain !== null){
          //@var Nodeクラス fromNode 投影元のノードクラス
          let fromNode = chainparser.searchNodeId(String(Object.keys(chain)[0]), treeTop);
          //@var Nodeクラス toNode 投影先のノードクラス
          let toNode = chainparser.searchNodeId(String(Object.values(chain)[0]), treeTop);
            
          if(fromNode !== undefined && toNode !== undefined){
            //投影先と投影元をリンクさせる
            chainparser.syncLink(toNode, fromNode);
          }
        }
      });
    } 
  }

  //投影のノードを斜体にする
  //@param Nodeクラス treeTop ツリー全体のインスタンス
  let onSyncTree = function onSyncTree(treeTop){
    //全ノードを取得して、回す
    chainparser.concatNode(treeTop).forEach(node => {
      //投影のノードを斜体にする
      if(typeof(node.id) === 'string'){
        if(node.id.substr(0, 2) === 'ta'){
          node.onSync();
        }
      }
    })
  }

  //ツリーのdom要素を生成した時は、ツリーは表示しているので、非表示にする。
  //@param Nodeクラス treeTop ツリー全体のインスタンス
  let closeTree = function closeTree(treeTop){
    treeTop.child.forEach(child => {
      if(child.className === 'expandtree' || child.className === 'lastexpandtree'){
        child.child.forEach(childChild => {
          childChild.noneDisplayTree();
        })
      }
    });
  }

  //@var nodeクラス this.treeTop ツリーの一番上のノード
  let treeTop = new Node('', '1');

  if(findMobile.device_name === 'pc'){
    if(document.getElementById('chaintree') && document.getElementById('chaintree').tagName === 'DIV'){
    
      //@var dom 仮のツリーのdom
      let fragment = document.createDocumentFragment();
  
      //@var dom this.element dom要素を格納 ツリーのdom要素を加えていく一番上のツリー<div id="chaintree"></div>
      treeTop.element = document.getElementById('chaintree');
  
      //@var string this.className ツリーのcssのクラス名
      treeTop.className = 'chaintree';
    
      if(Array.isArray(treesepalete)){
        treesepalete.forEach(sepalete => {
  
          //treeTopのchildに、かたまりごとのノードを追加していく。
          createTopNode(sepalete, treeTop);
        });
      }
  
      //ツリーの全体のcssのクラス名を決める
      decisionClass(treeTop);
      //投影
      syncProjection(projectionChain);
      //domを生成して、ツリーを描画する
      createElement(treeTop, fragment);
      //投影を斜体にする
      onSyncTree(treeTop);
  
      //ツリーのdom要素を生成した時は、ツリーは表示しているので、非表示にする。
      closeTree(treeTop);
  
      //実際のdomにfragmentのdomを追加する
      treeTop.element.append(fragment);
    }else{
      console.log('chaintree div tag is no');
    }
  }else{
    document.getElementById('tree').style = 'display: none';
  }

  //ツリーインスタンスを返す
  return treeTop;
}

//ツリーアクションクラス
//@param array treesepalete 上下関係のオブジェクトのデータの配列
//@param array projectionChain 投影データ
//@return TreeActionクラス ツリー機能クラス
TreeAction = ((treesepalete, projectionChain) => {

  //@var Nodeクラス ノードクラス
  let Node = TreeAction.node;
  //@var ChainParser チェインパーサーのクラス
  let chainparser = TreeAction.chainparser;
  //@var Nodeクラス ツリーインスタンス
  let tree = TreeAction.createTree(treesepalete, projectionChain, Node, chainparser);

  //隠蔽/表示のメソッド
  //@param string nodeId 隠蔽するノードのid
  let changeDisplay = function changeDisplay(nodeId){

    //nodeIdが文字列か判断する
    if(typeof(nodeId) === 'string'){
      //@var Nodeクラス 隠蔽/表示するノード
      let node = chainparser.searchNodeId(nodeId, tree);
    
      //nodeが見つかれば
      if(node !== undefined){
        if(node.hide === false){
          //displayがtrueの場合は、表示されているので、隠蔽する
          displayNoneNode(node);
        
        }else if(node.hide === true){
          //displayがfalseならば、隠蔽しているので、表示する
          displayOpenNode(node);
        }
      }
    }
  }

  //表示しているノードを隠蔽する。投影も含めて
  //@param Nodeクラス 隠蔽するノード
  let displayNoneNode = function displayNoneNode(node){
    //隠蔽ノードのdisplayを変更する
    node.hide = true;
    //@var Nodeクラス 隠蔽するノードの親ノード
    let palent = chainparser.searchPalentNode(node.id, tree);
    //ノードクラスを隠蔽する
    chainparser.displayNone(node, palent);
    //隠蔽ノードに投影先があるなら、投影先も隠蔽する
    if(node.toLink !== []){
      //投影先のidをループする
      node.toLink.forEach(linkNodeId => {
        //@var Nodeクラス 投影先のノード
        let linkNode = chainparser.searchNodeId(linkNodeId, tree);
        //displayを変更する
        linkNode.hide = true;
        //@var Nodeクラス 投影先の親ノード
        let linkPalent = chainparser.searchPalentNode(linkNodeId, tree);
        //投影先を隠蔽する
        chainparser.displayNone(linkNode, linkPalent);
      });
    }
  }

  //隠蔽したノードを表示する。投影も含めて
  //@param Nodeクラス 表示するノード
  let displayOpenNode = function(node) {
    //displayを変更する
    node.hide = false;
    //@var Nodeクラス 表示するノードの親ノード
    let palent = chainparser.searchPalentNode(node.id, tree);
    //ノードを表示する
    if(palent.element.children[0].children[0].innerText === '-'){
      chainparser.displayOpen(node, palent);
    }
    //表示ノードに投影先があるなら、投影先も表示する
    if(node.toLink !== []){
      //投影先のidをループする
      node.toLink.forEach(linkNodeId => {
        //@var Nodeクラス 表示先のノード
        let linkNode = chainparser.searchNodeId(linkNodeId, tree);
        //displayを変更する
        linkNode.hide = false;
        //@var Nodeクラス 投影先の親ノード
        let linkPalent = chainparser.searchPalentNode(linkNodeId, tree);

        //表示されているツリー内であれば、表示する
        if(linkPalent.element.children[0].children[0].innerText === '-'){
          //投影先を表示する
          chainparser.displayOpen(linkNode, linkPalent);
        }
      });
    }
  }

  //全体の隠蔽したツリーを表示する
  let openTree = function openTree(){
    //ツリー全体をループする
    chainparser.concatNode(tree).forEach(node => {
      if(node.hide === true){
        //displayがfalseならば、隠蔽しているので、表示する
        //@var Nodeクラス 表示するノードの親ノード
        let palent = chainparser.searchPalentNode(node.id, tree);

        displayOpenNode(node, palent);
      }
    });
  }

  //@var string nodeId 再表示するノードのid
  //閉じているノードを表示する
  let reOpenNode = function reOpenNode(nodeId){

    //nodeIdを文字列か、調べる
    if(typeof(nodeId) === 'string'){
      //@var Nodeクラス 再表示するノード
      let node = chainparser.searchNodeId(nodeId, tree);
      
      //nodeが見つかれば
      if(node !== undefined){
        //ツリーを下から上に開く
        node.openBottomUpTree();
        //ノードをカレントにする
        node.focus();
        //クリップボードのデータをカレントにする
        clipboard.select(node.dir, node.id);
        clipboard.current(node.dir, node.id);
      }
    }
  }

  //現在のスパイラルでは使わない
  // let getLinetreeDir = function 
  //linetreeにあるか判断する
  //@var string nodeDir ノードクラスのディレクトリ
  //@return boolean linetreeにあるか判断する
  // let checkLineTree = function checkLineTree(nodeDir){
    
  //   //@var string topDir ディレクトリの第一階層のタイトル("部署")
  //   let topDir = getTopDir(nodeDir);
  //   //linetreeにあるか判断する
  //   if(topDir === "マイツリー" || topDir === "notitle"){
  //     return true;
  //   }else{
  //     return false;
  //   }
  // }
  //現在のスパイラルでは使わない

  //現在のスパイラルでは使わない
  //一番上位が同じか、判断する
  //@var string toNodeDir 先頭先のディレクトリ
  //@var string fromNodeDir コピーしたノードのディレクトリ
  //@return boolean 第一階層が同じか判断する
  // let checkSameTree = function checkSameTree(toNodeDir, fromNodeDir){
  //   //@var string toTopDir 第一階層のノードのタイトル
  //   let toTopDir = getTopDir(toNodeDir);
    
  //   //var sring fromToDir 第一階層のノードのタイトル
  //   let fromTopDir = getTopDir(fromNodeDir);
  //   //第一階層のタイトルが等しいなら、同じツリー内部にある
  //   if(toTopDir === fromTopDir){
  //     return true;
  //   }else{
  //     return false;
  //   }
  // }
  //現在のスパイラルでは使わない

  //現在のスパイラルでは使わない
  //第一階層のタイトルを取得する
  //@param string nodeDir ディレクトリ
  //@return string ディレクトリの第一階層のタイトル
  // let getTopDir = function getTopDir(nodeDir){
  //   //@var array splitToDir ディレクトリの文字を分割した配列
  //   let splitToDir = nodeDir.split('/');
  //   //先頭の空を削除する
  //   splitToDir.shift();
  //   //ディレクトリの一番先頭文字を取得する
  //   return splitToDir.shift();
  // }
  //現在のスパイラルでは使わない

  //ページ移動前のイベント
  window.addEventListener('beforeunload', function(){
    //@var array idを保存する連想配列
    let storage = {};
    //@var array 隠蔽しいてるノードのidを保存する
    let hiddenStorage = {};
    //全体のツリーを探索する
    chainparser.concatNode(tree).forEach(node => {
      //展開するツリーノードか、判断する
      if(node.className === 'expandtree' || node.className === 'lastexpandtree'){
        //展開している(非表示ではない)ノードのidを保存する
        if(!node.element.children[0].classList.value.match('unexpand')){
          //ボックスがマイナス文字(開いているなら)
          if(node.element.children[0].children[0].innerText === '-'){

            //親のノードidを削除する
            //上からツリーを開くので、子ノードが開く時に、親ノードも開く
            Object.keys(storage).forEach(key => {
              //@var Nodeクラス 親ノード
              let palent = chainparser.searchPalentNode(node.id, tree);
              //storageに親ノードが保存してあれば、削除する
              if(palent.id === key){
                delete storage[key];
              }
            });
            storage[node.id] = node.id;
          }
        }
      }
      //displayがfalseなら、hiddenstorageに保存する
      if(node.hide === true){
        hiddenStorage[node.id] = node.id;
      }
    });
    
    //配列は保存できないので、json文字列に変換して保存する
    localStorage.setItem('id', JSON.stringify(storage));
    localStorage.setItem('hiddenId', JSON.stringify(hiddenStorage));
    //クリップボードのデータを保存する
    storeClipboard();
  });

  //ページ移動前のクリップボードのデータの保存
  let storeClipboard = function storeClipboard() {
    localStorage.setItem('currentId', clipboard.getCurrentId());
    localStorage.setItem('currentDir', clipboard.getCurrentDir());
    localStorage.setItem('selectId', clipboard.getSelectId());
    localStorage.setItem('selectDir', clipboard.getSelectDir());
  }
  //ページ移動後のクリップボードのデータの復元
  let restoreClipboard = function restoreClipboard(){
    clipboard.current(localStorage.getItem('currentDir'), localStorage.getItem('currentId'));
    clipboard.select(localStorage.getItem('selectDir'), localStorage.getItem('selectId'));
  }
  //カレントのデータをクリップボードのデータに代入する
  let currentClipboard = function currentClipboard(node){
    clipboard.current(node.dir, node.id);
    clipboard.select(node.dir, node.id);
  }
  //ページ移動後にページ移動前のノードを開く
  let openNodeAfterPageMove = function openNodeAfterPageMove(){
    //@var array ツリーを開いていたノードのidの配列
    let storage = JSON.parse(localStorage.getItem('id'));
    //ページ移動前に開いていたノードを開く
    if(storage !== null && storage !== undefined){
      Object.keys(storage).forEach(id => {
        //@var Nodeクラス 開くノード
        let node = chainparser.searchNodeId(String(storage[id]), tree);
        if(node !== undefined){
          node.openBottomUpTreePageMove();
        }
      });
    }
  }

  //ページ移動前に、隠蔽していたノードを、ページ移動後にも隠蔽する
  let hideDisplayAfterPageMove = function hideDisplayAfterPageMove(){
    //@var array 隠蔽していたノードのid
    let hiddenStorage = JSON.parse(localStorage.getItem('hiddenId'));

    if(hiddenStorage !== null && hiddenStorage !== undefined){
      Object.keys(hiddenStorage).forEach(id => {
        //@var Nodeクラス 隠蔽するノード
        let node = chainparser.searchNodeId(hiddenStorage[id], tree);
        if(node !== undefined){
          displayNoneNode(node);
        }
      });
    }
  }

  //ノードまでスクロールする
  //@var Nodeクラス スクロールするノード
  let scrollNode = function scrollNode(node){
    
    //@var dom id=treeのdom
    let tree_tag = document.getElementById('tree');
    //@var int ツリー画面の表示領域とノードの位置の差(width)
    let left = (tree_tag.getBoundingClientRect().left + tree_tag.clientWidth)*0.6 - node.element.getBoundingClientRect().left;
    //@var int ツリー画面の表示領域とノードの位置の差(height)
    let top = (tree_tag.getBoundingClientRect().top + tree_tag.clientHeight)*0.6 - node.element.getBoundingClientRect().top;

    //表示領域よりはみ出していたら、移動する
    if(left < 0){
      tree_tag.scrollLeft = 18 - left + 75;
    }
    if(top < 0){
      tree_tag.scrollTop = 9 - top + 60;
    }
  }

  //ページ移動前のクリップボードのデータを復元する
  restoreClipboard();
  //ページ移動前に開いていたノードを開く
  openNodeAfterPageMove();
  //ページ移動前に隠蔽していたノードを閉じる
  hideDisplayAfterPageMove();

  //一覧表示からのノードの表示
  if(document.location.pathname.split('/')[1] === 'show'){
    
    //@var Nodeクラス 開くノード
    let node;

    //@var array パスをスラッシュで区切った配列
    let path_array = document.getElementById('copyTarget').parentNode.action.split('/');
    //@var string 詳細行の部署のid
    let nodeId = path_array[path_array.length - 1];
    
    //投影のノードをクリックしたら
    if(nodeId.slice(0, 2) === 'ta'){
      //@var Nodeクラス 投影ノード
      let projectionNode = chainparser.searchNodeId(nodeId, tree);
      //投影元のノードを取得する
      node = chainparser.searchNodeId(projectionNode.fromLink[0], tree);
    }else{
      //@var Nodeクラス 開くノード
      node = chainparser.searchNodeId(nodeId, tree);
    }

    if(node !== undefined){
      //ノードを開く
      node.openBottomUpTree();
      //ノードをカレントにする
      node.focus();
      scrollNode(node);
      //クリップボードのデータをカレントにする
      currentClipboard(node);
    }
  }else if(document.location.pathname.split('/')[1] === ''){
    //indexルートの場合
    //@var Nodeクラス カレントにするノード 
    let node = chainparser.searchNodeId('bs00000001', tree);
    
    if(node !== undefined){
      node.openBottomUpTree();
      node.focus();
      scrollNode(node);
      currentClipboard(node);
    }
  }else{
    //複写、移動、削除の場合
    if(document.getElementById('back_treeaction').value === 'delete' || document.getElementById('back_treeaction').value === 'open'){
      //@var Nodeクラス ツリーの開くノード
      let node = chainparser.searchNodeId(document.getElementById('action_node_id').value, tree);

      if(node !== undefined){
        //ノードを開く
        node.openBottomUpTree();
        //ノードをカレントにする
        node.focus();
        scrollNode(node);
        //クリップボードのデータをカレントにする
        currentClipboard(node);
      }
    }else{
      //showルーティングでも、ツリー機能ルーティングでもない場合
      //@var Nodeクラス カレントにするノード 
      let node = chainparser.searchNodeId(clipboard.getCurrentId(), tree);
      if(node !== undefined){
        node.openBottomUpTree();
        node.focus();
        scrollNode(node);
        currentClipboard(node);
      }
    }
  }
  
  return {
    openTree: openTree,
    changeDisplay: changeDisplay,
    reOpenNode: reOpenNode
  }
})(treeChain, projectionChain);

//隠蔽のイベント
//詳細行の表示ではない時は、イベントを追加しない
if(document.getElementById('tree_change_display')){
  document.getElementById('tree_change_display').addEventListener('click', () => {
    //@var array パスをスラッシュで区切った配列
    let path_array = document.getElementById('copyTarget').parentNode.action.split('/');
    //@var string 詳細行のid
    let nodeId = path_array[path_array.length - 1];
    //隠蔽のメソッド
    TreeAction.changeDisplay(nodeId);
  });
}

//再表示イベント
//詳細行の表示ではない時は、イベントを追加しない
if(document.getElementById('open_tree')){
  document.getElementById('open_tree').addEventListener('click', () => {
    //@var array パスをスラッシュで区切った配列
    let path_array = document.getElementById('copyTarget').parentNode.action.split('/');
    //@var string 詳細行のid
    let nodeId = path_array[path_array.length - 1];
    //隠蔽のメソッド
    TreeAction.reOpenNode(nodeId);
  });
}

//ツリー画面の露出ボタンのクリックイベント
document.getElementById('openTree').addEventListener('click', () => {
  TreeAction.openTree();
});

export {TreeAction};