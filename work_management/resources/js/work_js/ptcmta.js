import {clipboard} from './ptcmcb';

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
    let imgName = this.getImgName();

    //div要素に、アイコンとタイトルを挿入する
    segment.innerHTML = '<img src = "/image/' + imgName + '.png" width = "15" height = "17">' + this.title;

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
    }else{
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
      palent.openBox();
      palent.openDisplayChild();
      //また親要素を引数にして再帰的に、openBottomUpTreeを呼び出す
      palent.openBottomUpTree();
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
      //目的のノードクラスの親要素のツリーを開く
      palent.openBox();
      palent.openDisplayChild();
      //また親要素を引数にして再帰的に、openBottomUpTreeを呼び出す
      palent.openBottomUpTree();
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

  //ノードのidから表示するアイコンの名前を取得する
  //@return string アイコンの名前
  getImgName(){
    //@var stirng 返すアイコンの名前
    let imgName = 'ur';
    
    if(this.id.substr(0, 2) === 'ji'){
      //人事の場合
      imgName = 'ji';
    }else if(this.id.substr(0, 2) === 'bs'){
      //部署の場合
      imgName = 'bs';
    }else if(this.id.substr(0, 2) === 'ta'){
      //投影の場合
      if(this.fromLink.length !== 0){
        imgName = this.fromLink[0].substr(0, 2);
      }
    }else if(this.id.substr(0, 2) === 'ur'){
      //ユーザー情報の場合
      imgName = 'ur';
    }else if(this.id.substr(0, 2) === 'lo'){
      //ログアウトの場合
      imgName = 'lo';
    }else if(this.id.substr(0, 2) === 'ss'){
      //システム設計の場合
      imgName = 'ss';
    }

    return imgName;
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
       if(topNode.id === 'bs' || topNode.id === 'ss'){
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
  let createElement = function createElement(treeTop) {
    treeTop.child.forEach(node => {
      //<div id="chaintree"></div>にdomを追加
      treeTop.element.append(node.createTree());
      
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

  if(document.getElementById('chaintree') && document.getElementById('chaintree').tagName === 'DIV'){
    // treeTop.prototype.chainparser = chainparser;

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
    createElement(treeTop);
    //投影を斜体にする
    onSyncTree(treeTop);

    //ツリーのdom要素を生成した時は、ツリーは表示しているので、非表示にする。
    closeTree(treeTop);
  }else{
    console.log('chaintree div tag is no');
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

  //ページ移動後のイベント
  window.addEventListener('DOMContentLoaded', function(){

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

      //@var string 開くノードのid
      let nodeId = document.location.pathname.split('/')[3];
      //投影のノードをクリックしたら
      if(nodeId.substr(0, 2) === 'ta'){
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
          currentClipboard(node);
        }
      }
    }
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
        let node = chainparser.searchNodeId(Storage(hiddenStorage[id]), tree);
        if(node !== undefined){
          displayNoneNode(node);
        }
      });
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
    //@var string 詳細行の部署のid
    let nodeId = document.getElementById('parent').children[0].children[1].children[0].innerText.substr(3);
    //隠蔽のメソッド
    TreeAction.changeDisplay(nodeId);
  });
}

//再表示イベント
//詳細行の表示ではない時は、イベントを追加しない
if(document.getElementById('open_tree')){
  document.getElementById('open_tree').addEventListener('click', () => {
    //@var string 詳細行の部署のid
    let nodeId = document.getElementById('parent').children[0].children[1].children[0].innerText.substr(3);
    //隠蔽のメソッド
    TreeAction.reOpenNode(nodeId);
  });
}

//ツリー画面の露出ボタンのクリックイベント
document.getElementById('openTree').addEventListener('click', () => {
  TreeAction.openTree();
});

export {TreeAction};