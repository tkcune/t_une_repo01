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

    let base64_img_list = {
      'back' : 'iVBORw0KGgoAAAANSUhEUgAAAOAAAADSCAIAAADc/VsQAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAkNSURBVHhe7d3dj5xlHcbxuXdnZ2Y7Q9m1S4tS0ba0FC3FhJ4RA6Wgoii10qASBCJgfInGA+MfoEY9MCGmxChKfMEIBUmJivJXKL6dqbHabZFqQrvdeXZnnhnW5Hdy7/387mQn4+5V9/s56ZWQ6czsXp2Di2fuJwz6SzVH79U/Wlql+LeFRAjBEjalYZiwlAjtayzF6m/aYykRJuvuXwcooKCQRkEhjYJCGgWFNAoKaRQU0sLS+b9ZTCz9+lFLsU79kqWUP4NhMygH7hC+0KreO9uHv2opMTWzkz5BGgWFNAoKaRQU0igopFFQSKOgkBa6Z/9sMVG+/GlLsfbUsqUKXA+6qQ0GA0uJhebbLcVah79mKdGY280nKKRRUEijoJBGQSGNgkIaBYU0CgppofB30P5vPmUp1q4Xliqwg25qg6GFlLuD3v51Swl2UKijoJBGQSGNgkIaBYU0CgppFBTSQnH2TxYT/ZeqvxffrrtHirKDbnK5HbS1y1KsdeQblhLsoFBHQSGNgkIaBYU0CgppFBTSKCikhWLe30F/9bClWLue+V6813h/H3On0xEesvIg51G5Gzg5D8k9/9rnXu+FZZ5n7a85jPCTWeE9Ue4h9ucq/tfiawut3ZZirfd801KiMbeHT1BIo6CQRkEhjYJCGgWFNAoKaRQU0kIx79wUvlbrv/iQpdhI14OuedIb6SErD1r7pOc9Ue75q/+2zHNkRs2h80zdS4uWEs1W01KsPuG/6MyqmvvheKr/tsHQPx902rke9H3fspRozF3HJyikUVBIo6CQRkEhjYJCGgWFNAoKaRQU0rJD/akHLcXak5v+4AbnXZb+uQXLPQuphc51lmLD7QcsJab++pKl2EzDfZr1+cUM/P9ZsTDtXLB8F0M9LlsUFNIoKKRRUEijoJBGQSGNgkJadgd94QFLsfbkpriRl3cd8YqitLBKUd9uKTHcdYelROvGo5Ziw+KipcTSy1+yFJsd/sdSIoQRrkpes4F/7fPCFmcHff/jlhKNq9hBoY2CQhoFhTQKCmkUFNIoKKRRUEgLXX8HLX9+v6VYO3QtVdjoHdR5/qG/z5XD6sf0wrSlxIXpPZZirUPVV9CumN57m6VUvWUh1j/r/mqKX37BUmx2eN5SIgT/bNnxLaSZK2IXO9U/tObd37aUYAeFOgoKaRQU0igopFFQSKOgkEZBIS10z/zBYqJ83ttB3YNVN3wHHTjHtC47V3CuKKavsRTrvfkWS4npQ9U/mca26jNa/ytzSKzzmvvn3HusFb/wdtDXLCVCZu0c5QDbaqV/J69F5+v/zQ+dsJRgB4U6CgppFBTSKCikUVBIo6CQRkEhLRRnfm8xUZ78mKXYljDO78V7E1zI3PnKv1vUxV71P7nlne+2lGgePG4pNrXzZkuJidZWS6u5rzn7dqp/Bv2z7kRdvPg5S7HsDrrmX80IytLfQbc6O+g9T1hKsINCHQWFNAoKaRQU0igopFFQSKOgkJbdQZ+5z1JsS83fQTN3zXd4O2jP3+0uTLjHcE7eeK+lWPOdH7SUmNz6FkuxiXrDUsIfYtdpB1069VlLsZnyVUuJENbjwyi3g16511KseTSzg+7lExTSKCikUVBIo6CQRkEhjYJCGgWFtFD8091B+08fsxRrTy5bqjDCRYfVK+Dry95CWqsd+YqFRGv/ey3FwvSspQprfs0jvMnMQuzuoOf8HfSFz1iKzfT9HdT+/N/KfS/+Cud80GPftZRo7NjHJyikUVBIo6CQRkEhjYJCGgWFNAoKaaH7j1csJvo/+bClWGdddtALSxZSxZ47LSW2HvmypdjkzFstJYbOfZJqE5MWEro76PI5S4nc+aDjU/pHFixe6Xwv/tj3LCUa29lBoY2CQhoFhTQKCmkUFNIoKKRRUEjL7qA/Pmop1pnY4PNBL3Z7lhKFc9Hh1K2ft5Ro7b3VUmyi5V9C6ry23BmcI+yg8/75oCcfsRSbLf9lKeE//zjlrgf1vhd/75OWEo0d1/MJCmkUFNIoKKRRUEijoJBGQSGNgkJadgf94T2WYp2JrqUKY1vbMlcvZv5T6fyTW6x1LCX6u2+3FGvc9BFLicbbDlmKhfoWS4lRzgedd48sKJ57zFJstqd8Peg+S7Hm8e9bSrCDQh0FhTQKCmkUFNIoKKRRUEijoJCW3UGfuttSrBMWLVUQbXw5KC0lumX1a+7O7LKUqN90v6VY66B7N6b6bOaL+RZW6Z9xfzXFyUctxWaX5i0lNn4HndlvKda87weWEuygUEdBIY2CQhoFhTQKCmkUFNIoKKSNtIMOFiyl1uWm5OM1dK7U7JfucLg4bFmKDW74gKVE/V3HLSWa195sKdZ/7S+WEsWzn7QUmy38HTRzpa63xK5dbgedvcFSrPnRpywl2EGhjoJCGgWFNAoKaRQU0igopFFQSAvd07+zmOg/eZelWCf454OuzymU4+VOd+57GTjXVvZC01JisXW1pUR5oPq+/FPXHrSU6J36oqXYtv7rlhK538z4fmuZ624XZ663FGt+/EeWEo2r9/MJCmkUFNIoKKRRUEijoJBGQSGNgkIaBYW00D39W4uJ/nfutBTrTCxZqnAZDvWuzGW81W8z84Dh5KSlRBHalmLdYcNSotM7bynW3OgPnNxQP+sc3PDA05YSDPVQR0EhjYJCGgWFNAoKaRQU0igopIXu3zM76B2WYp2afyOvy/GC5VF4b3OUExCGzmkXmeMUvNNo1+eU2oxRDm74xE8tJdhBoY6CQhoFhTQKCmkUFNIoKKRRUEjL7qBPHLEU6wz9G3ltlh10vMY4Xm7wzz+3g25zdtAHf2YpwQ4KdRQU0igopFFQSKOgkEZBIY2CQlp2Bz1x2FKsU7tkqYLT+A2+TBHj5uyt5cDfQefeYSnWfOgZSwl2UKijoJBGQSGNgkIaBYU0CgppFBTScjtoeeI2S7H2YIQdlCH0/4tz4W9mB+1eVb2DNh5+1lKCHRTqKCikUVBIo6CQRkEhjYJCGgWFtNA9/YrFRO/xWyzFrigXLKX4Xvzmlpm7L84dsBRrPfK8pURjxz4+QSGNgkIaBYU0CgppFBTSKCikUVAIq9XeAMPcFVuCORH6AAAAAElFTkSuQmCC',
      'bs' : 'iVBORw0KGgoAAAANSUhEUgAAAOAAAADSCAIAAADc/VsQAAAABGdBTUEAALGPC/xhBQAAAAlwSFlzAAAOwwAADsMBx2+oZAAABURJREFUeF7t3b2KXVUYx+EZGcHGOxARJZ1Y2KXzA7yBkEJNFSwFK5s0IY2NlWApqaIWITcg+NGlsxC7oIh4BzaCA5MBl7CdvGTWcc3e+3/2PE/jKmYmZ7Y/Drwv6ySHJycnB5DqmfZfiCRQogmUaAIlmkCJJlCiWTNtzeHhYTvNbJlyvIMSTaBEEyjRBEo0gRItcYofnEP3fS8x+OsfXb/RTjM7vn+vnc4z8n/EOyjRBEo0gRJNoEQTKNEESrTl1kz925PBRUm5/ih/zf6XNNNTKl/AYnuiORz/8Us7TT182A4TnY/UOyjRBEo0gRJNoEQTKNFGp/jFZvNB5WhfvqQLn0OfYq+n+PpBlUzxbJVAiSZQogmUaAIlmkCJtsOaaXs3G0qrr5kOrl5th82wZmKrBEo0gRJNoEQTKNFWnuJ3uHDQr5oZy9H46IVX2mliySm+1H//JlG5gjDFs1UCJZpAiSZQogmUaAIl2ixrpv5NzRz+evRFO008d+WDdprqv5YxvGbqXx6Vr39f1M+5Ys3EFgiUaAIlmkCJJlCijU7x/QKH0x1G+3mm+MFnUr7+dX9m+e1/fvtmO008//b37TTx5CP1Dko0gRJNoEQTKNEESjSBEq1eM82xE9kXF37d4R/lIy33L8+++H47bcXfv3/ZThPWTGyBQIkmUKIJlGgCJZpAibbDmmmOnUjgfZzSyKLkKba3zrvwB+UdlGgCJZpAiSZQogmUaKOfSdredYdyDi2tPsWvuwOZabNxhndQogmUaAIlmkCJJlCiCZRoO6yZSnt93aF/UVIafHSn5lgzzcGaCWoCJZpAiSZQogmUaBuc4tOuO+yq/1LOupZ5UN5BiSZQogmUaAIlmkCJJlCiJa6ZBu9A9G+USquvmUrlc+7X/y8V9VvmmXgHJZpAiSZQogmUaAIl2ugUX+ofORe7A9E/sc6x1ljSdz981k4Tb73xUTtN9H/lYp58+N5BiSZQogmUaAIlmkCJJlCizbJm6je4kFpseVQqX3y5uxk3uCd679Y37TTx1SfvtNNE/8+8e/fDdpp46eUr7XSe33591E4TN29+3k7/8g5KNIESTaBEEyjRBEo0gRKtXjOteyXn6wcP2mni3WvX2mmi/ysXM75m6t8olQavIw3+Qf3fbs3EFgiUaAIlmkCJJlCi1dN6OR0PGhzDj67faKeJ4/v32mmi/2d+fPtOO028/tqr7XSeH3/6uZ0mPr1zu53+a3C67x+Zy690WQRmIVCiCZRoAiWaQIkmUKLNsmbq3yiVBm97DP5B/d++5Jqp1H8vpH/NVBrcKJWsmdgCgRJNoEQTKNEESjSXRc5yWaSdJvr/9FLnwH7KX2DLnhEo0QRKNIESTaBEEyjRllszlfrvhfSvmUqDG6WSyyLtdB5rJjZLoEQTKNEESjSBEu1SXxYZ/DUzp/jB2x6D90JKpng2S6BEEyjRBEo0gRJNoERzWeT/W33NVBrcKK34l4icsmZizwiUaAIlmkCJJlCi7fCPyQbeIFnsXkgpc4ovLTawl0zxbJZAiSZQogmUaAIlmkCJtvKaqTS4UVr3Xkj5PE+Vj3Td3dMcRjZKJe+gRBMo0QRKNIESTaBEEyjREtdMpcXuKJWsmTpZM3G5CJRoAiWaQIkmUKLtzRS/LlN8J1M8l4tAiSZQogmUaAIlmkCJJlCiCZRoAiWaQIkmUKIJlGgCJZpAiSZQogmUaAIlmkCJJlCi1Z9JKl2SDyqNf/yo5DNJZ/hMElsgUKIJlGgCJZpAiSZQogmUaAIlmkCJJlCiCZRoAiXYwcFjJESfW9VsV0IAAAAASUVORK5CYII=',
      'copy' : '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',
      'delete' : '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',
      'insert' : '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',
      'ji' : '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aYQIQgQhggozguhEiODhDoQIQgQhgtS6UGFGiCBERAq1ESJOaYQIQgQhggozguhEiODhDoQIQgQhgtS6UGFGiCBERAq1ESJa41E0x38JDaQGLprQym7w15In3fneLzU89Gv/0ECIeOcn//308L3ww8/8XQP/zwgRypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERyoLxQYiHKnvlt/5oA/9kBynETqmJPSBEXPnQVxp+7A9fGhH/3AcRyoIRIuKhyggRhAg765Wa2ANCBCGCe4+CEBGhLBghIh6qjBBBiLCzXqmJPSBEECK49ygIERHKghEi4qHKCBGECDvrlZrYA0IEIYJ7j4IQEaEsGCEiHqqMEEGIsLNeqYk9IEQQIrj3KAgREcqCESLiocoIEYQIO+uVmtgDQgQhgnuPghARoSwYISIeqowQQYiws16piT0gRBAiuPcoCBERyoIRIuKhyggRhAg765Wa2ANCBCGCe4+CEBGhLBghIh6qjBBBiLCzXqmJPSBEECK49ygIERHKghEi4qHKCBGECDvrlZrYA0IEIYJ7j4IQEaEsGX/xiOYIEYQI/r0O/Nu/QRAiLv/y3zQ88dxLI+J/VAURSm3EVvJrNEeIIEQQIrj3KAgRhAjuPQpCRIRSG7GV/BrNESIIEYQI7j0KQgQhgnuPghARodRGbCW/RnOECEIEIYJ7j4IQQYjg3qMgREQotRFbya/RHCGCEEGI4N6jIEQQIrj3KAgREUptxFbyazRHiCBEECK49ygIEYQI7j0KQkSEUhuxlfwazREiCBGECO49CkIEIYJ7j4IQEaHURmwlv0ZzhAhCBCGCe4+CEEGI4N6jIEREKLURW8mv0RwhghBBiODeoyBEECK49ygIERFKbcR2Qvo7nlL8+coIEfxviOCun/2LlvQfnRrCnT/zlw2EiLd94sUR8Rc8iFBqs8VSarEUf74yQgQhwhCRTj4EQgQhgnuPghARodRmi6XUYin+fGWECEKEISKdfAiECEIE9x4FISJCqc0WS6nFUvz5yggRhAhDRDr5EAgRhAjuPQpCRIRSmy2WUoul+POVESIIEYaIdPIhECIIEdx7FISICKU2WyylFkvx5ysjRBAiDBHp5EMgRBAiuPcoCBERSm22WEotluLPV0aIIEQYItLJh0CIIERw71EQIiKU2myxlFosxZ+vjBBBiDBEpJMPgRBBiODeoyBERCi12WIptViKP18ZIYIQYYhIJx8CIYIQwb1HQYiIUGqzxVJqsRR/vjJCBCHCEJFOPgRCBCGCe4+CEBGh1GaLBb9Gc6/45rc08G9fYIh6fkSvfurPGi6//8sNjz/70in53qv/2sbz4GHcdOm+hgilNmIr+TWaI0QQIlKI8ORDIEQQIqhhRZSU8Tx4GISICKU2Yiv5NZojRBAiUojw5EMgRBAiqGFFlJTxPHgYhIgIpTZiK/k1miNEECJSiPDkQyBEECKoYUWUlPE8eBiEiAilNmIr+TWaI0QQIlKI8ORDIEQQIqhhRZSU8Tx4GISICKU2Yiv5NZojRBAiUojw5EMgRBAiqGFFlJTxPHgYhIgIpTZiK/k1miNEECJSiPDkQyBEECKoYUWUlPE8eBiEiAilNmIr+TWaI0QQIlKI8ORDIEQQIqhhRZSU8Tx4GISICKU2Yiv5NZojRBAiUojw5EMgRBAiqGFFlJTxPHgYhIgIpTZiK/k1miNEECJSiPDkQyBEECKoYUWUlPE8eBiEiAilNmIr+TWau/GO1zVwlfPgjh//XAP/ATdQA6gBj/zuiw08nD323LWGb/vwnzdECuuPENEcIYKbnQeECEIEIYKSQIjg4YwQQYiIFNYfIaI5QgQ3Ow8IEYQIQgQlgRDBwxkhghARKaw/QkRzhAhudh4QIggRhAhKAiGChzNCBCEiUlh/hIjmCBHc7DwgRBAiCBGUBEIED2eECEJEpLD+CBHNESK42XlAiCBEECIoCYQIHs4IEYSISGH9ESKaI0Rws/OAEEGIIERQEggRPJwRIggRkcL6I0Q0R4jgZucBIYIQQYigJBAieDgjRBAiIoX1R4hojhDBzc4DQgQhghBBSSBE8HBGiCBERApbj0zR3Csf/rkG/u0L7njnn/4v0tU3wmucdM9PfaHh23/n3xqIaUVUmH3P7/9Xw6UHH2mIY9dGMFg+PgjNESIIEdz7AHLZDK9xEiGCEEFMK6LCjBBBiIhj10YwWD4+CM0RIggR3PsActkMr3ESIYIQQUwrosKMEEGIiGPXRjBYPj4IzREiCBHc+wBy2QyvcRIhghBBTCuiwowQQYiIY9dGMFg+PgjNESIIEdz7AHLZDK9xEiGCEEFMK6LCjBBBiIhj10YwWD4+CM0RIggR3PsActkMr3ESIYIQQUwrosKMEEGIiGPXRjBYPj4IzREiCBHc+wBy2QyvcRIhghBBTCuiwowQQYiIY9dGMFg+PgjNESIIEdz7AHLZDK9xEiGCEEFMK6LCjBBBiIhj10YwWD4+CM0RIggR3PsActkMr3ESIYIQQUwrosKMEEGIiGPXRjBYPj4IzREiCBHc+wBy2QyvcRIhghBBTCuiwowQQYiIY9dGMDiT3XDxUsPtTzzbcMeTz7ddesdnTokhIr1J6aGnv9rwxo9/rYFMV/TIn1xr+8ZfuNoQ56yN1LDDESIIEdw7I6YVGSLSm5QIEYQIYloRFWaEiDhnbYSIHY4QQYjg3hkxrcgQkd6kRIggRBDTiqgwI0TEOWsjROxwhAhCBPfOiGlFhoj0JiVCBCGCmFZEhRkhIs5ZGyFihyNEECK4d0ZMKzJEpDcpESIIEcS0IirMCBFxztoIETscIYIQwb0zYlqRISK9SYkQQYggphVRYUaIiHPWRojY4QgRhAjunRHTigwR6U1KhAhCBDGtiAozQkScszZCxA5HiCBEcO+MmFZkiEhvUiJEECKIaUVUmBEi4py1ESJ2OEIEIYJ7Z8S0IkNEepMSIYIQQUwrosKMEBHnrI0QcSbjf1QF8VBl11+8q+GW7/uNtle9/VOnhP8LwSuuvKXh/nf/esPDH32hgZi68FG4/K7fbIuTLBpHRzy08XgJxEOVESKoMCOmFREiCBGECHIBqXXho0CFWZxk0Tg64qGNx0sgHqqMEEGFGTGtiBBBiCBEkAtIrQsfBSrM4iSLxtERD208XgLxUGWECCrMiGlFhAhCBCGCXEBqXfgoUGEWJ1k0jo54aOPxEoiHKiNEUGFGTCsiRBAiCBHkAlLrwkeBCrM4yaJxdMRDG4+XQDxUGSGCCjNiWhEhghBBiCAXkFoXPgpUmMVJFo2jIx7aeLwE4qHKCBFUmBHTiggRhAhCBLmA1LrwUaDCLE6yaBwd8dDG4yUQD1VGiKDCjJhWRIggRBAiyAWk1oWPAhVmcZJF4+iIhzYeL4F4qDJCBBVmxLQiQgQhghBBLiC1LnwUqDCLkywaR0c8tPF4CcRDlREiqDAjphURIggRhAhyAal14aNAhVmcZNE4OuKhJeMvjnDESA033v1Qw00PvL3t1rdebXjVE3+02MXv+kDDy1/zHQ38L8Xgmz74fMMbrn6t4Vue+Y+GBz78xYa7H/35ttsefLSBv0BGHHvZiK3k1zhihAhCBBVmhAhS60KIIEQQIggRhAhCBCGCCjNCBCEijr1sxFbyaxwxQgQhggozQgSpdSFEECIIEYQIQgQhghBBhRkhghARx142Yiv5NY4YIYIQQYUZIYLUuhAiCBGECEIEIYIQQYigwowQQYiIYy8bsZX8GkeMEEGIoMKMEEFqXQgRhAhCBCGCEEGIIERQYUaIIETEsZeN2Ep+jSNGiCBEUGFGiCC1LoQIQgQhghBBiCBEECKoMCNEECLi2MtGbCW/xhEjRBAiqDAjRJBaF0IEIYIQQYggRBAiCBFUmBEiCBFx7GUjtpJf44gRIggRVJgRIkitCyGCEEGIIEQQIggRhAgqzAgRhIg49rIRW8mvccQIEYQIKswIEaTWhRBBiCBEECIIEYQIQgQVZoQIQkQce9mIreTXOGKECEIEFWaECFLrQoggRBAiCBGECEIEIYIKM0IEISKOvWzEVmrvugs3N/CfaAOtgL/963XbD37ilJAp+EdqXfwnbCdd/sDfNhBil4d+79oxHnj6nxru/aEPNfDXkojIFowQQYggRNBZL2JaESGC1LoQIggRpNaFznoRIggRhIiIbMEIEYQIQgSd9SKmFREiSK0LIYIQQWpd6KwXIYIQQYiIyBaMEEGIIETQWS9iWhEhgtS6ECIIEaTWhc56ESIIEYSIiGzBCBGECEIEnfUiphURIkitCyGCEEFqXeisFyGCEEGIiMgWjBBBiCBE0FkvYloRIYLUuhAiCBGk1oXOehEiCBGEiIhswQgRhAhCBJ31IqYVESJIrQshghBBal3orBchghBBiIjIFowQQYggRNBZL2JaESGC1LoQIggRpNaFznoRIggRhIiIbMEIEYQIQgSd9SKmFREiSK0LIYIQQWpd6KwXIYIQQYiIyA6O1HDzd3+kgZvh1sevTqVb3vyxxcgUV37rhbNCxHjNk880RIIHR4ggRBAiuMpEal0IEbSyJUIEISISPDhCBCGCEMFVJlLrQoiglS0RIggRkeDBESIIEYQIrjKRWhdCBK1siRBBiIgED44QQYggRHCVidS6ECJoZUuECEJEJHhwhAhCBCGCq0yk1oUQQStbIkQQIiLBgyNEECIIEVxlIrUuhAha2RIhghARCR4cIYIQQYjgKhOpdSFE0MqWCBGEiEjw4AgRhAhCBFeZSK0LIYJWtkSIIEREggdHiCBEECK4ykRqXQgRtLIlQgQhIhI8uAuXH2u47YnPNdzy2LMNt771D6b/O357ePW7P9/w+o9dG9GFe6/UzC73gt8eCBHcexS0WJpd7gW/PRAiuPcoaLE0u9wLfnsgRHDvUdBiaXa5F/z2QIjg3qOgxdLsci/47YEQwb1HQYul2eVe8NsDIYJ7j4IWS7PLveC3B0IE9x4FLZZml3vBbw+ECO49ClosveyG27+h4cLrf6TldT/Qcv/bprXwnw3D7Y+/f0T8J7hKs8sxECK49yhosTS7HAMhgnuPghZLs8sxECK49yhosTS7HAMhgnuPghZLs8sxECK49yhosTS7HAMhgnuPghZLs8sxECK49yhosTS7HAMhgnuPghb/x8VL/w2HzXEee2S0KAAAAABJRU5ErkJggg==',
      'last' : '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',
      'lo' : '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',
      'move' : 'iVBORw0KGgoAAAANSUhEUgAAAOAAAADSCAIAAADc/VsQAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAbhSURBVHhe7d2/jxR1GMfxmdnZvVu5Y4FoYmzs7WwVf8TexFjZekaMomAE8VQCUUBBD6KYKInBv0A6GwsLLW2MiQmd9hbHcZfdY3/NCOapyPf7TGDY28/A+5VJeBKOvdvjnW/xZHcnnUwmSVxRFDYB85DZn4AkAoU0AoU0AoU0AoU0AoU0AoW0dDQa2RgynU5tAuaBExTSCBTSCBTSCBTSCBTSCBTSWDM1yb8nVmwK2bXQs2mHZWMbIjL3JZ0LH1+yKYQTFNIIFNIIFNIIFNIIFNIIFNIIFNLYgzbJ8PffbAoZ9bdsunMLhXdUTSZeJP0/f7EporvVsilk8dR5m0I4QSGNQCGNQCGNQCGNQCGNQCGNQCGNPWiTlPZnWOq96rJK4f1HX7v8tU0h4/W/bYpYbKU2hex+33twTlBII1BII1BII1BII1BII1BII1BIq7UHLT9/y6aI8TS3aWeVpbcxdP/ylizz9nazMxr1bYpY+vSyTXduXHhr0vbVP2wKuf7zDzaFLB36xqaIrfMHbQrZc/Q7m0I4QSGNQCGNQCGNQCGNQCGNQCGt1pqpf/ZdmyI6rx6xaWf5a6YkrdgizWfJdPO0WFy0KaLVe8SmkEHi3pq69J5W/uWHNoXkB7z/x6y3z6aIjS+8dSRrJjQYgUIagUIagUIagUIagUIagUJarT3ooGoP+tDqVzbtrOFwaFNIp9OxKSKtWpRqGkxu2BRSXjptU0jxxJM2hSw//ZJNQQvepyvetHHuDZtC2IOiwQgU0ggU0ggU0ggU0ggU0ggU0nT3oP5rOv0fO8+9tzu3WhV7O1n+72RjbdWmoEcftyFk9yveqjLLvYOscm3MHhT3LQKFNAKFNAKFNAKFNAKFNAKFNN09qP+aTn+X6e9BZZXDig+GvH7R23Tu2h7YFFIe9+72UmTee+r9H6ubVPy2tz953aaQyYlvbQrhBIU0AoU0AoU0AoU0AoU0AoW0ea6ZKj4k0dXQdwb7Bn/9alNE66crNoUMj5y1KWQ579oUMk29NVPhfrBjJ2nbFDE+9Y5NIfmqd5MQTlBII1BII1BII1BII1BII1BII1BIuz8/fnGOxon3G2ttr9sUsnnhpE0R6bHPbArptffYFOQunTevePczLje9lz72Vt60KaIovDVqlnmnJCcopBEopBEopBEopBEopBEopBEopNXag26ePWxTxO5V762uTTW2P4M2/NdGnn7PhpB8/7M2RSw8/6JNIe3Eeyt2mUxsCun/+L1NIeNN79/uXfFe7lkTJyikESikESikESikESikESikESik1dqDXj9XsQftfXAf7kHLsbcIvXHmqE0h13rLNoU8dtC7Y/YtFfcR99XZg3pPec/KIZtmgBMU0ggU0ggU0ggU0ggU0ggU0mqtmdbXvBeP3bTv6AWbGqUsvWfdXztuU0h7cM2mkIWPvI8aTNoVn2NYD2sm4F4jUEgjUEgjUEgjUEgjUEgjUEirtQftV3384q5mfvzisPA+qrBYe9umkNGxizaFLCfe3XOy2Z4X7EGBe41AIY1AIY1AIY1AIY1AIY1AIY3b0AQU29s2hWx0vVdtLiW5TSG13jZcF3tQ4F4jUEgjUEgjUEgjUEgjUEgjUEgj0ICs23WufUnuXJ3/l52xa47KpHSu6bTrXGWrdC77BrNBoJBGoJBGoJBGoJBGoJBGoJDW1EAL18hlD/GgKVve1V1yrnI8dS57/NngBIU0AoU0AoU0AoU0AoU0AoU0AoU03UBLly08I9ou+6I4+wmaxn41EaPR2Lny4dS5sk7LuezbzwYnKKQRKKQRKKQRKKQRKKQRKKQRKKQ1NdDMZau/CHuIBrInEDFxtVqlc5XpunMlk9S7ZokTFNIIFNIIFNIIFNIIFNIIFNIIFNKaGqit/iLsiyJsWRpnX6fHnl7E1JW77Bvo4QSFNAKFNAKFNAKFNAKFNAKFNN1AU5cthO6KfYMGsicQ0XHZQzQNJyikESikESikESikESikESikESikzTZQex3YXbGHiLB1aIR90QPG1qERt93e+PbLZd9gHjhBIY1AIY1AIY1AIY1AIY1AIY1AIS31b089nXo3W946c9imiNb+F2wKydwFm+wyc5LMai+YphXnRavGSrIovH87/eeqTSHj5GGbQva+dsCmGeAEhTQChTQChTQChTQChTQChTQChbRae9DJqWM2RQzysU1BzdyDJlXbyrtWVm1Y0zovzXRfJptl3k3Is2detimk+9RzNs0AJyikESikESikESikESikESik1VozVSqn3vKiocqZLcDSGkukutwnlc7vMys5QSGNQCGNQCGNQCGNQCGNQCGNQCEtne+H6wGeJPkP7QEn7JCItQ4AAAAASUVORK5CYII=',
      'new' : 'iVBORw0KGgoAAAANSUhEUgAAAOAAAADSCAIAAADc/VsQAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAUsSURBVHhe7d2/q09xHMdxX9ePQSYTZVE2pWtwr5+b0Wq1+R/8AzcmKUoKJYuBGK2EsCtlwoDuqiT3Xnd4n/rUOXF/fN3v6/J4LF4DxfXsDO++99zRz58/t/QsLi7WgonaWr9CJIESTaBEEyjRBEo0gRJt9OPHj5qNhYWFWo3RaFSLf8XS0lKtVJ6gRBMo0QRKNIESTaBEEyjRBEq04Tvo4GfwTpw4Uasz+GeJtWPHjlqda9eu1WocOXKkVgBPUKIJlGgCJZpAiSZQogmUaOs6M7148aIWm8H09HStzq1bt2o1nJlgpQRKNIESTaBEEyjRBEo0gRJNoEQTKNEESjSBEk2gRBMo0QRKNIESTaBEEyjRBEo0gRJNoEQTKNEESjSBEk2gRBMo0QRKNIESTaBEEyjRBEo0gRJNoEQTKNEESjSBEm2S76ifmZmp1ZiamqpFzzq/4N5RD2MmUKIJlGgCJZpAiSZQovlRiP8RZyYYM4ESTaBEEyjRBEo0gRJNoEQTKNEESjSBEk2gRBMo0QRKNIESTaBEEyjRBEo0gRJNoEQTKNEESjSBEk2gRBMo0QRKNIESTaBEEyjRBEq0Sb7d7v3797Ua8/PzteiZnZ2ttSbebgdjJlCiCZRoAiWaQIkmUKJN8sx0/fr1Wo0PHz7Uoufy5cu11sSZCcZMoEQTKNEESjSBEk2gRPOzOv8jhw8frtW5e/durUb/t02QJyjRBEo0gRJNoEQTKNEESjRnJqJ5ghJNoEQTKNEESjSBEk2gRBMo0dxBN73Bb/V8+PBhrcbU1FSt1du6deBZ9vjx41qNPXv21BoHT1CiCZRoAiWaQIkmUKIJlGjOTKEG39V/9uzZWo39+/fXaty/f79WYzQa1Vq9r1+/1moM/n0WFxdrNd68eVNrlTxBiSZQogmUaAIlmkCJJlCiTfLMdPTo0VqN9ZxCNoWXL1/WavT/1f2v9rLnz5/XaqznK/bt27dajV27dtVak7m5uVqNR48e1eq8fv261m95ghJNoEQTKNEESjSBEk2gRBMo0XzcLsLx48drdZ48eVKrsXv37lpr0v8g3LFjx2o1Xr16VWt8Tp06Vatz8ODBWo3bt2/X6niCEk2gRBMo0QRKNIESTaBEc2baaN+/f6/VOH36dK3OCj+NtioTPDNt27atVmfww5b9ojxBiSZQogmUaAIlmkCJJlCiOTNttIsXL9Zq3Lt3r1bn48ePtRqDJ6ozZ87U+pOlpaVancEXfa38VfaDf3zwW0/7Bi9czkxsMgIlmkCJJlCiCZRoAiWaM9NGu3PnTq3GpUuXanXevXtX608WFhZq/Un/KnTy5MlajXX+tw5eqfpvOHNm4l8gUKIJlGgCJZpAiSZQogmUaO6gG23wTDgzM1Or8ze+thP8rs63b9/W6pw/f75Wo/8jPT1BiSZQogmUaAIlmkCJJlCiOTNF6J97tm/fXqvx9OnTWmPSP28t+xtnpv4/8MGDB7Ua+/btq9XxBCWaQIkmUKIJlGgCJZpAibauMxNrcPPmzVqNQ4cO1erMzs7Wagx+A2f/E0CTNfg375+Zrly5Uuu3PEGJJlCiCZRoAiWaQIkmUKINn5lW/j4qNtK5c+dqNT59+lSrMXgo7F929u7dW6vx7NmzWo0bN27UavRfCbas/779ZQcOHKi1Sp6gRBMo0QRKNIESTaBEEyjRBEo0d9B/0+fPn2s1rl69Wqvz5cuXWo3p6elajQsXLtRq7Ny5s9Zf4wlKNIESTaBEEyjRBEqwLVt+ATPyglIv3b60AAAAAElFTkSuQmCC',
      'next' : 'iVBORw0KGgoAAAANSUhEUgAAAOAAAADSCAIAAADc/VsQAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAdjSURBVHhe7d3di1R1HMfx/c3uzJ51RXuAbiqiiOoPqL8iCDII7Lq77quLqIgeIdIe6TnLiILqLg1SKx/yuVWzNKyohGSFatX06DpnTgrfi+B8z3dslmE+R9+vm/2AjJ45+2EuvvP1d9L87Hdj9cZnt1uqSK1kCViAciwqUst+ApIoKKRRUEijoJBGQSGNgkIaBYU0CgppKd+72qIn+/5lSw4G9bhQpf30lAzq0VgUFNIoKKRRUEijoJBGQSGNgkIaBYW0lO9516In218/qA/nq/Fodmgune8ORnJ7F6L2VxO/Ez5BIY2CQhoFhTQKCmkUFNIoKKRRUEhL+Uw4B933giVHPHRs3JS0WZPFi+e99MKL5RMU0igopFFQSKOgkEZBIY2CQhoFhTQKCmkpn1ll0ZPtWWnJEc+KYwO/NprqlgvaoQ5eG70wLeQ2DIvgNdXewx4ni6C5KCikUVBIo6CQRkEhjYJCGgWFNAoKaSnf/bZFTzZTP6jvMxUfijIci3e7PUueEyfnLXkWT41bqmhPti25ynCMH385ADbq0WgUFNIoKKRRUEijoJBGQSGNgkIaBYW0lO98y6Inm1lhyTG0QX395LbX61ryzJVXWvKk2+6z5Dn76yZLFdmRLZY80+3oksbHh/QREN/8kXw9MOAlMahHg1FQSKOgkEZBIY2CQhoFhTQKCmkUFNL6Dep3P2vJMbRy1++o94rCkmdu8jpLns7twTE+Y612ZqnizMEvLLkOfmLBMzU/a8nTTmctVYy34ts7klH8UDCoR4NRUEijoJBGQSGNgkIaBYU0CgppKd/xhkVPtiOYg47gZJE+C8tTN1jyTN7xiiXPxJKrLVWkVvROTx/eY8lzeld0cMvUn99aqsjKU5Y8E2PRGSqxEfzaQvE74RMU0igopFFQSKOgkEZBIY2CQhoFhTQKCmkp3x4O6rc/bckxgnIXRe2G7znHs2hQ37nzNUueiaX1g/rw3PEUbhYX+Zwlz4mZ2mXndOgzS57pk79Y8nRSeNhJ/fWGp5kPa8LfC/9iPkEhjYJCGgWFNAoKaRQU0igopFFQSKOgkJbybdH4Otv2jCVHOLgNh9t9JsL1iiIaQR/Prrfk6Sx73ZJn4rJrLFWkVu1jPM9bwHS7LGofHzp/ZL8lT/fAWkue8odPLXmmWyctVXTaE5b+v9TnPtTeiF74Qj5BIY2CQhoFhTQKCmkUFNIoKKRRUEijoJCW8q2vWvRkW5+y5FhAucPxdXC8dZ9B/aJwo35Z9H8HBh7UxwPqMv7CIrgRZXTYeS//y5Jn/rftljxndq2yVLH42AFLnixc1O/zRuvfKYN6NBgFhTQKCmkUFNIoKKRRUEijoJCW8m/COejmJy1V9Rt8DUPRC08Wmb7Rkqdz15uWPOEcNFrjXchtKMv686/jvzd8JGSvd9qS5+TO9y1VdHa8aMmzONWuV58z8G3gUYhoMAoKaRQU0igopFFQSKOgkEZBIY2CQlrfQf3jlhwjKHefQX34rM7O3e9Y8gy+sGw/B1HW72aX4VNJx8L7cPrnLZY8+dcrLFVcceonS55OCkfqg+JZnWgwCgppFBTSKCikUVBIo6CQRkEhjYJCWso3vWTRk21+wpJjeBv1tQPhoruAjfrl71nyDH4EeB/RcLvs1u6oF3OHLXlObKs9HeSc9r4PLHmWTlqoGo/faLz7Pig26tFgFBTSKCikUVBIo6CQRkEhjYJCGgWFtH6D+o2PWXKMoNzF2ej0leNLbrLkGXxQn8J32or+tDh+xJLnzI/rLFUU+z625Jmc3WvJs2gi2lIPztQZ3lcvAQb1aDAKCmkUFNIoKKRRUEijoJBGQSGNgkJayjdGx5JnXz5qqSoeXw9HUYQb9UtuseTp3LPakmfi8mstVYWn0Jz9I5qZz+/+0JJn/NAaSxWLunOWPBPhtwOxkUzjA73wivgEhTQKCmkUFNIoKKRRUEijoJBGQSGt3xx0wyOWqkYxB+2FJ4vMLb3ZkqezPDqNo9VqW6qYP7Tekqec+ciSp300mpJO1/6b/bakLyIcAY4Go6CQRkEhjYJCGgWFNAoKaRQU0igopKX8q+cterIND1tyjGDztdcrLHn+7lxlydO99V5LnvL3nZYqFh+dseSZ6h6z5GmFR4AHB4SrrRUPD4N6NBgFhTQKCmkUFNIoKKRRUEijoJBGQSEt5RtWWvRk6x6yVDWKle946j1fRpf0T1G/v35+ub12YDw5Fn07kOKLKuNLBkeAo8koKKRRUEijoJBGQSGNgkIaBYU0CgppKV+/wqInGtTrlTvezS7DJfVUPy6Ol9svndX3IWGjHg1GQSGNgkIaBYU0CgppFBTSKCikUVBIS/n65yx6ss8ftFTVZ6OeATb+q/aLkDJFVeETFNIoKKRRUEijoJBGQSGNgkIaBYW0lK8L56BrH7BUdck8TBJDxRwUDUZBIY2CQhoFhTQKCmkUFNIoKKRRUEjrN6hfc7+lqnhQz9HXuDAM6tFgFBTSKCikUVBIo6CQRkEhjYJC2NjYv0FrhBvvNaRUAAAAAElFTkSuQmCC',
      'ng' : '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',
      'ok' : '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',
      'open' : 'iVBORw0KGgoAAAANSUhEUgAAAOAAAADSCAIAAADc/VsQAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAASpSURBVHhe7d0/TlR7HMZhhsRCKky4JZEN4C5cgZV0sAZCZgdDNQ0JHRugMjaswcZlaEIgl0aKCX8uxq/mnJwZ7oCOvMM8T6Fv8sshxnw8CSdzsLe0tHR7e3v3KwTqqZNky/U7ROpdXV3VbLi5uakFT+HFixc/hjso0QRKNIESTaBEEyjRBEq03mg0qtlwcHBQq2F/f78W/CF7e3u12nZ3d38Md1CiCZRoAiWaQIkmUKIJlGjjHzMNh8NaDf1+vxb8IYPBoFbbr9jcQYkmUKIJlGgCJZpAiSZQos3qMdPLly9rNXz+/LlW2+vXr2v91Ot9f2Gfh5r0Evmkv8/pXzr/+vVrrYY3b97Uavv27Vut/+MxE/NNoEQTKNEESjSBEk2gRBMo0ebs43aHh4e12ra3t2sxzsePH2u1vX//vtZPf/nnxnkOynwTKNEESjSBEk2gRBMo0Rburc5Xr17Vavvy5UutpzPpw2/r6+u1Gs7Pz2vNM4+ZmG8CJZpAiSZQogmUaAsX6L8T1PGT6k1w9w17V13z3LmDEk2gRBMo0QRKNIESTaBEEyjRBEo0gRJNoEQTKNEESjSBEk2gRBNouQ1Wf8SFJFCiCZRoAiWaQIkmUKIJlGgCJZpAiSZQogmUaAIlmkCJJlCiCfQ+9cO7OuqY2RMo0QRKNIESTaBEEyjRBEo0gd6nXlrrqGNmT6BEEyjRBEo0gRJNoEQTKNEESjSBEk2gRBMo0QRKNIESTaBEE+h96h25jjpm9gRKNIESTaBEEyjRBEo0gRKtNxqNajYMh8NaDf1+v1aey8vLWg2np6e1GjY2Nmq1jf0KIVZWVmo1fPr0qVbD5uZmrbaxXyHBYDCo1fYrNndQogmUaAIlmkCJJlCiCZRoAiXaM3kOurq6Wqvh4uKi1hTm7jnoWGtra7Xazs7OaoXxHJT5JlCiCZRoAiWaQIn2TAK9+4a9q85+Q70j11HHee6+Wx+rjueQOyjRBEo0gRJNoEQTKNEESjSBEk2gRBMo0QRKNIESTaBEEyjRBEo0gd7ndoI6ZvYESjSBEk2gRBMo0QRKNIESTaBEEyjRBEo0gRJNoEQTKNEESjSBlvqxYI9VX6WjjqdT19AgUKIJlGgCJZpAiSZQogmUaAJ9jHp3rqEeFHXUBR11WVtd01HXLCSBEk2gRBMo0QRKNIESTaBEEyjRBFrqUeR06vlkQx1MrS5rq7OO+iMuJIESTaBEEyjRBEo0gRJt4QJdnaCO2+q76446nkJd0FHHbXXW8c84dc1z5w5KNIESTaBEEyjRBEo0gRKtNxqNajYMh8NaDf1+v9bTefv2ba22Dx8+1GpYXp7VP7/uBzh6D3y1bexHQB76Rbqur69rte3s7NRqOz4+rvVEBoNBrbZfsbmDEk2gRBMo0QRKNIESTaBEEyjR/upz0KOjo1ptW1tbtfg9s3vD7uTkpFbDu3fvaj2W56DMN4ESTaBEEyjRBEo0gRJtzj5uxzPjMRPzTaBEEyjRBEo0gRJNoEQbH+j1OHUGf0611VHH7qCEEyjRBEo0gRJNoEQTKNG+/+9mNSGPOyjRBEo0gRJNoEQTKNEESjSBEmxp6T97U6Ppr5ppxQAAAABJRU5ErkJggg==',
      'remove' : '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',
      'road' : 'iVBORw0KGgoAAAANSUhEUgAAAOAAAADSCAIAAADc/VsQAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAapSURBVHhe7d1biNRlHMZxZ1fHmTGx1lMZkSWpeci6iNTQsCIoKKgoCIpKyiwVo0wDSaIUbbETVEYhnc1asjQPWaQkUldReeh0EVqa7UamQbiru2tz8dz8+v//Sys18+z0/bDgc7XLLl/fi5fl3dzx48d7Aa7q9C9giUBhjUBhjUBhjUBhjUBhjUBhjUBhjUBhjUBhjUBhjUBhjUBhjUBhjUBhjUBhjUBhjUBhjUBhjUBhjUBhjUBhjUBhjUBhjUBhjUBhjUBhjUBhjUBhjUBhjUBhjUBhjUBhjReWu+H3Q4e1ony+j1ZCv1JJqyfIiiGXy2lVHCcorBEorBEorBEorBEorBEorBEorNX+PWhLy69a0eaPPtZK2LP3R63ozyNHtKL67GvCAQMGaEUTxo/Vii6/7FKthPr6f+00+XLHLq2o0DevFY0eNVKr4jhBYY1AYY1AYY1AYY1AYY1AYY1AYa127kGb1ryrFW3+cItWdOxYu1bC0aNtWlFn939W9XX1WlGxWNCKsu5Ny2beOV0rGnnOCK1o5+7dWgkrnl+pFc26e4ZWNHbMaK2K4wSFNQKFNQKFNQKFNQKFNQKFNQKFNQKFtR52Uf/ya6u0ErZs/UQryufTfwl30sQLtRKmXTJVKzr7rOFaUWtr+sV+2e6vv9GK1qx9Xyvat2+/VsKggQO1oquuvEIrWvXm21oJ+YxfTJ4za6ZWNGb0KK2K4wSFNQKFNQKFNQKFNQKFNQKFNQKFNdN70K3btmtFr7yaeQ/av38/rWj6bbdoRRdMOE/Lybr1m7QSmt5J/43sjvYOrahP9rO6pWJRK5ozm3tQoDsIFNYIFNYIFNYIFNYIFNYIFNYIFNaqfFH/28GDWlHj8qe1oqznkssWPHCvVlTF14FPwMGMH0jZ0sYntaLmlhatqK4u8/QpFtLfNeGiHugeAoU1AoU1AoU1AoU1AoU1AoW1Kt+D7tiZ/ifPHl68TCu66cbrtRJuuP5arZ7g0KHDWtGKF9LfPi7bsSv9xeS+GS9TdIF7UODfQaCwRqCwRqCwRqCwRqCwRqCwVol70C6+xBurm7SiDRs3a0VPLV+qlTB06BAtJ80Zv8D6zHMvaEVf7diplZD1Eu8JKGY83DD//rla0fhxY7QqjhMU1ggU1ggU1ggU1ggU1ggU1ggU1ggU1ipxUd/Z2amV8MiSx7Si1rb0v922cME8rYT+/U/SctKW8Y380pz+2kIul9P6L2V9lcGDBmlFhUJfrYrjBIU1AoU1AoU1AoU1AoU1AoU1AoW1Kt+DznvwIa2o4ZSTtaL75s7SSiiVSlqoIZygsEagsEagsEagsEagsEagsEagsEagsFblQEulQupHa1tr6kdnNn1G1BZOUFgjUFgjUFgjUFgjUFgjUFgjUFir8gvLL738ula0ddt2reiJxiVaCYMHpz86UPN+PnBA6+8y34AYdtqpWvY4QWGNQGGNQGGNQGGNQGGNQGGNQGGtEvegXfj88y+0osXLlmtFs2beoZVw+WXTtGrU4cN/aEWNjz+tFe3bv18r4ZWVz2vZ4wSFNQKFNQKFNQKFNQKFNQKFNQKFNQKFtSpf1De3/KoVLW18XCs6cqRVK2HRwvla0enDhmn1BB0dHVoJa9dv1IrealqjFU27ZIpWwsw7p2vZ4wSFNQKFNQKFNQKFNQKFNQKFNQKFtSrfg2Z99Q2bNmtFq99Ov/MrG37mGVrR7bferBWNOPssLScbNqZ/42Vr1r6vFRWKfbWiRxct1EpoaGjQsscJCmsECmsECmsECmsECmsECmsECmsECmtVvqjvrmdXvKiVsP3Tz7SioUOGaEVTp0zWSrh48kStKOtTtba2aSV8+933WtH6DR9oRXv27tVKqO/dWyuafc8MrWjcmHO1EnK5zMeX3XCCwhqBwhqBwhqBwhqBwhqBwhqBwloPuwftwqrVTVpR1nsHpWJRK6GuLv3/bdb1YRc/weOdnVpRe3v6Aw35PumXnWWz77lLKzp/wnitWsQJCmsECmsECmsECmsECmsECmsECmu1cw+a9Y3s2fuTVvTeuvR3EMp++GGPVnSsvV3rH+vdu14rmjzpIq3oumuu1kooFAta/yecoLBGoLBGoLBGoLBGoLBGoLBGoLBGoLBWOxf1FXDgl2atKJ/PayU0nHKyVtSDnk6oLk5QWCNQWCNQWCNQWCNQWCNQWCNQWOMeFNY4QWGNQGGNQGGNQGGNQGGNQGGNQGGNQGGNQGGNQGGNQGGNQGGNQGGNQGGNQGGNQGGsV6+/AOrVc1arVvGWAAAAAElFTkSuQmCC',
      'search' : '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',
      'ss' : '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',
      'tizu' : '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',
      'top' : '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',
      'tree' : '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',
      'under' : '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',
      'updown' : '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',
      'ur' : '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',

    };
    
    if(src === 'lo'){
      //ログアウトの場合
      src = 'ss';
    }else if(src === 'kb'){
      //システム設計の場合
      src = 'ss';
    }else if(src === 'sb'){
      //システム設計の場合
      src = 'tizu';
    }

    src = `<img width = "15" length = "17" src = "data:image/png;base64,${base64_img_list[src]}">`;

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