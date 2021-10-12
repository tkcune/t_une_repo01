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

    //@var boolean display 表示/非表示を切り替える
    this.display = true;

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

    //@var stirng アイコンの名前
    let img_name = this.getImgName();

    //li要素に、アイコンとタイトルを挿入する
    li.innerHTML = '<img src = "/image/' + img_name + '.png" width = "15" height = "17">' + this.title;

      
    //クリック処理。テスト用のクリック処理
    //クリックした要素を表示する
    li.addEventListener('click', {node: this, handleEvent: this.displayDetail});
    //
      
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

  //treeの特定位置に挿入
  //@param dom element 挿入するdom
  //@param int deleteId 挿入の基準となる子要素のid
  insertTree(element, deleteId){

    //@var 挿入する基準となるdom
    let insertElement;
    
    //基準のidがゼロ以上(最初以外)は、その前のdomを取得して、後ろにdomを挿入する
    if(deleteId > 0){
      insertElement = this.child[deleteId - 1].element;
      insertElement.after(element);
    }else{

      //基準がゼロの場合(最初のdom場合)は、後ろのdomを取得して、前に挿入する
      insertElement = this.child[deleteId + 1].element;
      insertElement.before(element);
    }
  }

  //ツリーの子要素を削除
  //node.childのelementのdom要素を削除
  deleteElement(deleteId) {
    if(this.className === 'expandtree' || this.className === 'lastexpandtree'){
      //expandtree系の場合li要素から、nodeのchildのdom要素を取得している。
      //@var dom li 削除されるdom要素の起点
      let li = this.element.children[1].children[0];
      
      li.children[deleteId].remove();
    }else{

      //treeTopの場合
      this.element.children[deleteId].remove();
    }
  }

  //削除などのツリーの再構成 nodeのchildのelement
  resettingClassElement() {

    //@var dom li nodeのelementのli要素、node.childのelement 
    let li = this.element.children[1].children[0];
      
    for(let i = 0; i < this.child.length; i++){

      //unexpand以外のcssの要素名を削除して、新しいcss名を付ける
      li.children[i].classList.remove('expandtree', 'lastexpandtree', 'normaltree', 'lastnormaltree', 'lasttree', 'firsttree', 'expandbox');
      li.children[i].classList.add(this.child[i].className);
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
  
      //マイナス文字を反転する。
      if(box.innerText === '+'){
        box.innerText = '-';
        //ツリーのhtmlの表示に切り替える
        this.openDisplayNode();
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
      
    //@var string アイコンの名前
    let img_name = this.getImgName();

    //div要素に、アイコンとタイトルを挿入する
    div.innerHTML = '<img src = "/image/' + img_name + '.png" width = "15" height = "17">' + this.title;

    //クリック処理を追加する
    div.addEventListener('click', {node: this, handleEvent: this.displayDetail});

    return div;
  }

  //詳細行に表示
  displayDetail() {
    //@var dom child 表示する子のdom
    // let child = document.getElementById('child');
    // child.innerText = event.target.node.title;
    //文字の表示
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

  //投影先のノード全体を斜体にする
  sync(){
    //投影先のノードを斜体にする
    this.onSync();
    //ノードの子要素を斜体にする
    this.child.forEach(child => {
      child.sync();
    });
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
      palent.openDisplayNode();
      //また親要素を引数にして再帰的に、openBottomUpTreeを呼び出す
      palent.openBottomUpTree();
    }
  }

  //ツリーを閉じる
  closeBottomUpTree(){
    //@var array splitDir ディレクトリの文字を分割した配列
    let splitDir = this.dir.split('/');
    //最後の文字を削除する
    splitDir.pop();
    //配列が1の時は、chaintreeなので、openBottomUpTreeを呼ばない
    if(splitDir.length !== 1){
      //@var Nodeクラス palent 目的のノードクラスの親要素
      let palent = this.prototype.chainparser.searchNodeDir(splitDir.join('/'), this.prototype.tree);
      //目的のノードクラスの親要素のツリーを閉じる
      palent.closeBox();
      palent.noneDisplayNode();
      //また親要素を引数にして再帰的に、closeBottomUpTreeを呼び出す
      palent.closeBottomUpTree();
    } 
  }

  //@param Nodeクラス node 非表示にするノードクラス
  //子要素全体を非表示にする
  noneDisplayTree() {

    //expandtree系の場合
    if(this.className === 'expandtree' || this.className === 'lastexpandtree'){
      this.noneDisplayNode();
      this.closeBox();
    }
    this.child.forEach(child => {
      child.noneDisplayTree();
    });
  }

  //palentのchildのelementを非表示にする。
  noneDisplayNode(){

    //@var HTMLCollection node expandtree系のul要素のli要素の子要素の配列 
    let node = this.element.children[1].children[0].children;
    for(let i = 0; i < node.length; i++){

      //子要素が展開するボックスのツリーとそれ以外のツリーで処理を分ける。
      if(node[i].classList.value.match('expandtree') || node[i].classList.value.match('lastexpandtree')){
        
        //@var Nodes配列 childNodes 
        let childNodes = node[i].childNodes;
        //展開するボックスの子要素(treeboxなど)を表示、非表示にする
        for(let i = 0; i < childNodes.length; i++){
          childNodes[i].classList.add('unexpand');
        }
      }else{

        //ul要素の場合
        node[i].classList.add('unexpand');
      }
    }
  }

  //ツリーのボックスを展開する
  openDisplayNode(){

    //@var array node expandtreeの子要素のdomの配列
    let node = this.element.children[1].children[0].children;
    
    for(let i = 0; i < node.length; i++){
      //子要素が展開するボックスのツリーとそれ以外のツリーで処理を分ける。
      if(node[i].classList.value.match('expandtree') || node[i].classList.value.match('lastexpandtree')){
        
        //@var Nodes配列 childNodes 
        let childNodes = node[i].childNodes;
        //展開するボックスの子要素(treeboxなど)を表示、非表示にする
        for(let i = 0; i < childNodes.length; i++){
          childNodes[i].classList.remove('unexpand');
        }
      }else{

        //ul要素の場合
        node[i].classList.remove('unexpand');
      }
    }
  }

  //ボックスのプラスマイナスの文字を変化させる
  ChangeBox() {
    
    //@var dom box treeBoxのexpandboxのdom
    let box = this.element.children[0].children[0];
    
    //プラスマイナス文字を反転する。
    if(box.innerText === '+'){
      box.innerText = '-';
    }else if(box.innerText === '-'){
      box.innerText = '+';
    }
  }

  //ボックスを開く
  openBox(){
    //@var dom box treeBoxのexpandboxのdom
    let box = this.element.children[0].children[0];
  
    //プラス文字を反転する。
    if(box.innerText === '+'){
      box.innerText = '-';
    }
  }

  //ボックスを閉じる
  closeBox(){
    //@var dom box treeBoxのexpandboxのdom
    let box = this.element.children[0].children[0];
  
    //マイナス文字を反転する。
    if(box.innerText === '-'){
      box.innerText = '+';
    }
  }

  //子要素の表示、非表示を切り替える。
  changeChildDisplay() {

    this.child.forEach(child => {
      //子要素が展開するボックスのツリーとそれ以外のツリーで処理を分ける。
      if(child.element.classList.value.match('expandtree') || child.element.classList.value.match('lastexpandtree')){
        let childNodes = child.element.childNodes;
        
        //展開するボックスの子要素(treeboxなど)を表示、非表示にする
        for(let i = 0; i < childNodes.length; i++){
          if(childNodes[i].className.match('unexpand')){
            childNodes[i].classList.remove('unexpand');
          }else{
            childNodes[i].classList.add('unexpand');
          }
        }
      }else{
        
        //展開するボックス以外のツリーの子要素、ul要素
        if(child.element.classList.value.match('unexpand')){
          child.element.classList.remove('unexpand');
        }else{
          child.element.classList.add('unexpand');
        }
      }
    });
  }

  //@return dom div classNameがcss名のdiv
  createFirstdiv() {
    
    //var dom div 返すdiv
    let div = document.createElement('div');
    div.classList.add(this.className);
    return div;
  }

  //@return Nodeクラス copyNode コピーしたノードクラス
  //オブジェクトの深い値コピーをする
  deepCopyNode(){

    //@var Nodeクラス copyNode コピーノードクラス
    //一からノードクラスを作成していく
    let copyNode = new Node(this.dir, this.id);
    copyNode.prototype = {
      chainparser: this.prototype.chainparser,
      tree: this.prototype.tree
    };

    copyNode.className = this.className;

    //dom要素は参照なので、cloneNodeで値コピーする
    copyNode.element = this.element.cloneNode(true);

    //投影元と投影先のディレクトリをコピーする。
    copyNode.toLink = [];
    this.toLink.forEach(link => {

      //linkの配列は参照なので、concatで値コピーしている
      copyNode.toLink.push(link.concat());
    });
    copyNode.fromLink = [];
    this.fromLink.forEach(link => {

      //linkの配列は参照なので、concatで値コピーしている
      copyNode.fromLink.push(link.concat());
    });

    //cloneNodeでは、クリックイベントは、コピーしないので、また新しくイベントを追加する
    //expandtree系の場合のクリック処理を設定
    if(copyNode.element.children[0].className.includes("treebox") === true){

      //@var dom expnadboxDiv 展開するボックスのdom
      let expandboxDiv = copyNode.element.children[0].children[0];
      //ボックスの展開のクリックイベントを付ける
      expandboxDiv.node = copyNode;
      copyNode.addExpandEvent(expandboxDiv);

      //@var dom titleboxDiv 展開するボックスのタイトル
      let titleboxDiv = copyNode.element.children[0].children[1];
      //タイトルの表示イベントを付ける
      titleboxDiv.addEventListener('click', {node: copyNode, handleEvent: this.displayDetail});

    }else{
      //展開するボックスではなく線だけのツリーの場合

      //@var dom li 線だけのツリーのli要素
      let li = copyNode.element.children[0];
      //クリック処理。テスト用のクリック処理
      //クリックした要素を表示する
      li.addEventListener('click', {node: copyNode, handleEvent: this.displayDetail});
      //
    }
    
    //子要素のコピー
    this.child.forEach(child => {

      //子要素のコピーを作成して、追加
      copyNode.child.push(child.deepCopyNode());
      
      //@var dom li 子要素のdomの親要素はli
      let li = copyNode.element.children[1].children[0];
      for(let i = 0; i < copyNode.child.length; i++){

        //cloneNodeでは、子要素のイベントも消えない。
        //コピーした子要素のdomを入れ替える事で、子要素のイベントを付ける。
        li.replaceChild(copyNode.child[i].element, li.children[i]);
      }
    });
    return copyNode;
  }

  //ノードのidから表示するアイコンの名前を取得する
  //@return string アイコンの名前
  getImgName(){
    //@var stirng 返すアイコンの名前
    let img_name;
    
    if(this.id.substr(0, 2) === 'ji'){
      //人事の場合
      img_name = 'ji';
    }else if(this.id.substr(0, 2) === 'bs'){
      //部署の場合
      img_name = 'bs';
    }else if(this.id.substr(0, 2) === 'ta'){
      //投影の場合
      img_name = this.fromLink[0].substr(0, 2);
    }else if(this.id.substr(0, 2) === 'ur'){
      //ユーザー情報の場合
      img_name = 'ur';
    }else if(this.id.substr(0, 2) === 'lg'){
      //ログアウトの場合
      img_name = 'lg';
    }
    return img_name;
  }
}

//Chainparserクラス
//@return ChainParserクラス チェインパーサーのクラス
TreeAction.chainparser = (() => {
  //データから第一階層のノード情報を除く
      //@param array sepalete 親子関係を連想配列にしたchainの配列
      //@return array list 第一階層のchainを除いたchainの配列
      let exceptSepalete = function exceptSepalete(sepalete) {

        //@var array list 
        let list = [];
        sepalete.forEach(chain => {

          //chianのキーがchaintreeの値が第一階層
          if(Object.keys(chain)[0] !== '1.chaintree') {
            list.push(chain);
          }
        });
        return list;
      }

      //一番上だけのclassNameを決定する。
      //linetreeか、expandtreeか決定する
      //子要素に子要素がなければ、linetree
      //それ以外は、expnadtree
      //@param Nodeクラス topNode 一番上のノード
      let decisionTreeClass = function decisionTreeClass(topNode) {
      
        //@var boolean isChild 子要素の子要素があるか表す変数
       let isChild =false;
        topNode.child.forEach(child => {
          //子要素の子要素が空でなければ、true
          if(child.child.length !== 0){
            isChild = true;
          }
        });

        //子要素があれば、展開されるボックスのあるツリー(expandtree)
        if(isChild === true){
          topNode.className = 'expandtree'
        }else if(isChild === false){
          topNode.className = 'linetree';
        }
      }

      //同じノードオブジェクトか比較。
      //@param Nodeクラス node 比較される
      //@param Nodeクラス compairNode 比較する
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

      //ディレクトリとidからNodeクラスを検索する
      //@param string nodeDir ノードのディレクトリ
      //@param string id ノードクラスのid
      //@param Nodeクラス node 検索されるノードクラス(インスタンスのtree)
      let searchNodeDirId = function searchNodeDirId(nodeDir, nodeId, node){
      
        //@var array nodeArray 展開したノードクラスを格納した配列
        let nodeArray = concatNode(node);
      
        //@var Nodeクラス search 検索するノードを代入する
        let search;
        nodeArray.forEach(child =>{
        
          //ノードクラスのディレクトリとidが等しかったら
          if(child.dir === nodeDir && child.id === nodeId){
            search = child;
          }
        });
        return search;
      }

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

      //@param nodeクラス node ノードクラス
      //@return string nodeの親のタイトル
      //ノードクラスの親のタイトルを返す。
      let searchPalentTitle = function searchPalentTitle(node){

        //@var array nodeDir ノードのディレクトリを'/'を区切りに配列にした
        let nodeDir = node.dir.split('/');

        //nodeのタイトルを配列から削除。
        nodeDir.pop();

        //次に最後尾から取ったものが、親のタイトル
        return nodeDir.pop();
      }

      //@param string fromNodeDir リンク元のディレクトリ
      //@param string fromNodeId リンク元のid
      //@param Nodeクラス toNode リンク先のノード
      //投影先と投影元をリンクさせる。
      let syncLinkNode = function syncLinkNode(fromNodeId, toNode, tree){

        //@var Nodeクラス fromNode 投影元のノードクラス
        let fromNode = searchNodeId(fromNodeId, tree);

        //リンク元がない。投影先ではない
        if(fromNode.fromLink.length === 0){
          //@var array concatFromNode リンク元を一列に展開
          //@var array concatToNode リンク先を一列に展開
          let concatFromNode = concatNode(fromNode);
          let concatToNode = concatNode(toNode);
      
          for(let i = 0; i < concatFromNode.length; i++){

            //リンク元にリンク先のディレクトリを入れる
            //リンク先にリンク元のディレクトリを入れる
            syncLink(concatFromNode[i], concatToNode[i]);
          }
        }else{
          //投影元を持っている。fromNodeは、投影先。
          fromNode.fromLink.forEach(fromLinkIdDir => {
          
            //@var Nodeクラス fromLinkNode 投影元のノードクラス
            let fromLinkNode = searchNodeDirId(fromLinkIdDir[1], fromLinkIdDir[0], tree);
          
            //@var array concatFromNode リンク元を一列に展開
            //@var array concatToNode リンク先を一列に展開
            let concatFromNode = concatNode(fromLinkNode);
            let concatToNode = concatNode(toNode);
          
            for(let i = 0; i < concatFromNode.length; i++){

              //リンク元にリンク先のディレクトリを入れる
              //リンク先にリンク元のディレクトリを入れる
              syncLink(concatFromNode[i], concatToNode[i]);
            }
          });
        }
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

      //ノードクラスのディレクトリを更新する
      //@param Nodeクラス node 更新するノード
      //@param string nodeDir 更新するノードの親ノードディレクトリ
      let recreateDir = function recreateDir(node, nodeDir){

        //親ディレクトリとノードタイトルを結合してディレクトリを更新する
        node.dir = nodeDir + '/' + node.title;
        node.child.forEach(child => {

          //子要素について更新したディレクトリを引数にして再帰的に呼び出す。
          recreateDir(child, node.dir);
        });
      }

      //ツリーのidを更新する。
      //貼付や投影などで、idを更新した時
      //@param Nodeクラス node 更新するノード
      //@param array nodeChain 上下関係のデータのオブジェクト
      let recreateId = function recreateId(node, nodeChain){

        //ノードチェインについてすべてループする
        nodeChain.forEach(chain => {

          //上下関係のオブジェクトのキーとバリューのタイトルのどちらが等しければ、idを更新する
          if(node.title === Object.keys(chain)[0].split('.')[1]){
            node.id = Object.keys(chain)[0].split('.')[0];
          }else if(node.title === Object.values(chain)[0].split('.')[1]){
            node.id = Object.values(chain)[0].split('.')[0];
          }
        });

        //子要素も再帰的にidを更新する
        node.child.forEach(child => {
          recreateId(child, nodeChain);
        });
      }

      //親要素、親要素の親要素を返す
      //@param Nodeクラス node ノードクラス
      //@param Nodeクラス tree グローバル変数のtree
      //@return array 親要素、親要素の親要素の配列
      let getTwoPalent = function getTwoPalent(node, tree){

        //@var array splitDir ディレクトリを分けた配列
        let splitDir = node.dir.split('/');

        //最後の文字を削除することによって、親要素のディレクトリになる
        splitDir.pop();

        //@vaar Nodeクラス palent 親要素のディレクトリ
        let palent = searchNodeDir(splitDir.join('/'), tree);

        //親要素のディレクトリから最後の文字を削除することによって、親要素の親要素ディレクトリとなる
        splitDir.pop();

        //@var Nodeクラス palentPalent 親要素の親要素のディレクトリ
        let palentPalent = searchNodeDir(splitDir.join('/'), tree);
        return [palent, palentPalent];
      }

      return {
        getTwoPalent: getTwoPalent,
        recreateId: recreateId,
        recreateDir: recreateDir,
        syncLinkNode: syncLinkNode,
        searchPalentNode: searchPalentNode,
        searchPalentTitle: searchPalentTitle,
        concatNode: concatNode,
        searchNodeDirId: searchNodeDirId,
        searchNodeId: searchNodeId,
        searchNodeDir: searchNodeDir,
        decisionExpandTreeClass: decisionExpandTreeClass,
        decisionLineTreeClass: decisionLineTreeClass,
        decisionChildClass: decisionChildClass,
        isEqual: isEqual,
        decisionTreeClass: decisionTreeClass,
        exceptSepalete: exceptSepalete,
        syncLink: syncLink
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
    
    //@var Nodeクラス this.topNode ひとかたまりのツリーの一番上のNodeクラス
    let topNode;
    
    //親要素のchain(例{'1.chaintree', '4.作業'})を取り除いた配列
    let exceptSepalete = chainparser.exceptSepalete(sepalete);
    
    //topNodeを作成する
    //{'1.chaintree': '5.aa'}sepaleteのデータの中で、keyがchaintreeの値が一番上の親
    sepalete.forEach(chain => {
      if(Object.keys(chain)[0] === '1.chaintree'){
        topNode = new Node(treeTop.dir + '/' + Object.values(chain)[0].split('.')[1], Object.values(chain)[0].split('.')[0]);
        topNode.prototype = {
          chainparser: chainparser,
          tree: treeTop
        };
      }
    });
    
    //データからクラスの階層構造を作成する。
    parse(topNode, exceptSepalete);

    //Nodeクラスの第一階層のcss名を決定する。
    chainparser.decisionTreeClass(topNode);

    return topNode;
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
    projectionChain.forEach(chain => {
      //@var Nodeクラス fromNode 投影元のノードクラス
      let fromNode = chainparser.searchNodeId(Object.keys(chain)[0].split('.')[0], treeTop);
      //@var Nodeクラス toNode 投影先のノードクラス
      let toNode = chainparser.searchNodeId(Object.values(chain)[0].split('.')[0], treeTop);
      
      //投影先と投影元をリンクさせる
      chainparser.syncLink(toNode, fromNode);
    });
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

  //@var nodeクラス this.treeTop ツリーの一番上のノード
  let treeTop = new Node('', 1);
  // treeTop.prototype.chainparser = chainparser;

  //@var dom this.element dom要素を格納 ツリーのdom要素を加えていく一番上のツリー<div id="chaintree"></div>
  treeTop.element = document.getElementById('chaintree');
  
  //@var string this.className ツリーのcssのクラス名
  treeTop.className = 'chaintree';
  
  //トップツリーの子要素のクラスを作成して、追加。
  treesepalete.forEach(sepalete => {

    //treeTopのchildに、かたまりごとのノードを追加していく。
    treeTop.child.push(createTopNode(sepalete, treeTop));
  });

  //ツリーの全体のcssのクラス名を決める
  decisionClass(treeTop);
  //投影
  syncProjection(projectionChain);
  //domを生成して、ツリーを描画する
  createElement(treeTop);
  //投影を斜体にする
  onSyncTree(treeTop);

  //ツリーのdom要素を生成した時は、ツリーは表示しているので、非表示にする。
  treeTop.child.forEach(child => {
    child.noneDisplayTree();
  });

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

  //クリップボードのデータを保存する
  let copyNode = function copyNode(){
    //マイツリーのノードクラスるか判断する
    if(checkLineTree(clipboard.getSelectDir()) === true){
      //@var Nodeクラス node 本来のノードクラス
      let node = searchNodeId(clipboard.getSelectId());
      //マイツリーのノードクラスの場合、本来のノードクラスのディレクトリとidをコピーする
      clipboard.copyNode(node.dir, node.id);
    }else{
      //選択したノードクラスのディレクトリとidをコピーする
      clipboard.copyNode(clipboard.getSelectDir(), clipboard.getSelectId());
    }
  }

  //@param string ノードのid
  //カレント機能
  let current = function current(nodeId){
    //@var Nodeクラス currentNode カレントノード
    let currentNode = chainparser.searchNodeId(String(nodeId), tree);
    //カレントノードを太字にする
    currentNode.focus();
    //カレントノードを開く
    currentNode.openBottomUpTree();
    //クリップボードの選択ノードをカレントノードにする
    clipboard.select(currentNode.dir, currentNode.id);
    clipboard.current(currentNode.dir, currentNode.id);
  }

  //@param string nodeId ノードのid
  //ノードを開く
  let openNode = function openNode(nodeId){
    //@var Nodeクラス 開くノードクラス
    let node = chainparser.searchNodeId(String(nodeId), tree);
    //ノードを開く
    node.openBottomUpTree();
  }

  //@param string nodeId ノードのid
  //ノードを閉じる
  let closeNode = function closeNode(nodeId){
    //@var Nodeクラス 閉じるノードクラス
    let node = chainparser.searchNodeId(String(nodeId), tree);
    //ノードを開く
    node.closeBottomUpTree();
  }

  //ツリーのノードをidから検索する
  //@var string nodeId ノードクラスのid
  //@return Nodeクラス 検索したノードクラス
  let searchNodeId = function searchNodeId(nodeId){
    return chainparser.searchNodeId(nodeId, tree).deepCopyNode();
  }

  //クリックイベントを追加する
  //@param object callback クリックイベントの処理
  let addNodeClickEvent = (callback) => {
    //@var array titleboxのdom
    let titleboxArray = document.getElementsByClassName('titlebox');
    //@var array firsttreeのdom
    let firsttreeArray = document.getElementsByClassName('firsttree');
    //@var array normaltreeのdom
    let normaltreeArray = document.getElementsByClassName('normaltree');
    //@var array lasttreeのdom
    let lasttreeArray = document.getElementsByClassName('lasttree');
    //@var array secondtreeのdom
    let secondtreeArray = document.getElementsByClassName('secondtree');
    //@var array endtreeのdom
    let endtreeArray = document.getElementsByClassName('endtree');
    //コールバック関数が存在するなら
    if(callback){
      for(let i = 0; i < titleboxArray.length; i++){
        //@var string titleboxのディレクトリ
        let titleboxDir = getTitleboxDir(titleboxArray[i]);
        //@var Nodeクラス クリック処理を追加するノード
        let node = chainparser.searchNodeDir(titleboxDir, tree);
        //idを引数にして、クリックイベントを追加する
        titleboxArray[i].addEventListener('click', {id: node.id, title: node.title, handleEvent: callback});
      }
      for(let i = 0; i < firsttreeArray.length; i++){
        //@var string firsttreeのディレクトリ
        let firsttreeDir = getTitleboxDir(firsttreeArray[i]) + '/' + firsttreeArray[i].children[0].innerText;
        //@var Nodeクラス クリック処理を追加するノード
        let node = chainparser.searchNodeDir(firsttreeDir, tree);
        //idを引数にして、クリックイベントを追加する
        firsttreeArray[i].children[0].addEventListener('click', {id: node.id, title: node.title, handleEvent: callback});
      }
      for(let i = 0; i < normaltreeArray.length; i++){
        //@var string normaltreeのディレクトリ
        let normaltreeDir = getTitleboxDir(normaltreeArray[i]) + '/' + normaltreeArray[i].children[0].innerText;
        //@var Nodeクラス クリック処理を追加するノード
        let node = chainparser.searchNodeDir(normaltreeDir, tree);
        //idを引数にして、クリックイベントを追加する
        normaltreeArray[i].children[0].addEventListener('click', {id: node.id, title: node.title, handleEvent: callback});
      }
      for(let i = 0; i < lasttreeArray.length; i++){
        //@var string lasttreeのディレクトリ
        let lasttreeDir = getTitleboxDir(lasttreeArray[i]) + '/' + lasttreeArray[i].children[0].innerText;
        //@var Nodeクラス クリック処理を追加するノード
        let node = chainparser.searchNodeDir(lasttreeDir, tree);
        //idを引数にして、クリックイベントを追加する
        lasttreeArray[i].children[0].addEventListener('click', {id: node.id, title: node.title, handleEvent: callback});
      }
      for(let i = 0; i < secondtreeArray.length; i++){
        //@var string secondtreeのディレクトリ
        let secondtreeDir = getLinetreeDir(secondtreeArray[i]);
        //@var Nodeクラス クリック処理を追加するノード
        let node = chainparser.searchNodeDir(secondtreeDir, tree);
        //idを引数にして、クリックイベントを追加する
        secondtreeArray[i].children[0].addEventListener('click', {id: node.id, title: node.title, handleEvent: callback});
      }
      for(let i = 0; i < endtreeArray.length; i++){
        //@var string endtreeのディレクトリ
        let endtreeDir = getLinetreeDir(endtreeArray[i]);
        //@var Nodeクラス クリック処理を追加するノード
        let node = chainparser.searchNodeDir(endtreeDir, tree);
        //idを引数にして、クリックイベントを追加する
        endtreeArray[i].children[0].addEventListener('click', {id: node.id, title: node.title, handleEvent: callback});
      }
    }
  }

  //隠蔽/表示のメソッド
  //@param string nodeId 隠蔽するノードのid
  let changeDisplay = function changeDisplay(nodeId){
    //@var Nodeクラス 隠蔽/表示するノード
    let node = chainparser.searchNodeId(nodeId, tree);
    
    if(node.display === true){
      //displayがtrueの場合は、表示されているので、隠蔽する
      displayNoneNode(node);
      
    }else if(node.display === false){
      //displayがfalseならば、隠蔽しているので、表示する
      displayOpenNode(node);
      
    }
  }

  //表示しているノードを隠蔽する。投影も含めて
  //@param Nodeクラス 隠蔽するノード
  let displayNoneNode = function displayNoneNode(node){
    //隠蔽ノードのdisplayを変更する
    node.display = false;
    //@var Nodeクラス 隠蔽するノードの親ノード
    let palent = chainparser.searchPalentNode(node.id, tree);
    //ノードクラスを隠蔽する
    displayNone(node, palent);
    //隠蔽ノードに投影先があるなら、投影先も隠蔽する
    if(node.toLink !== []){
      //投影先のidをループする
      node.toLink.forEach(linkNodeId => {
        //@var Nodeクラス 投影先のノード
        let linkNode = chainparser.searchNodeId(linkNodeId, tree);
        //displayを変更する
        linkNode.display = false;
        //@var Nodeクラス 投影先の親ノード
        let linkPalent = chainparser.searchPalentNode(linkNodeId, tree);
        //投影先を隠蔽する
        displayNone(linkNode, linkPalent);
      });
    }
  }
  
  //ノードを非表示にして、隣のノードの表示を変える
  //@var Nodeクラス child 非表示にするノード
  //@var Nodeクラス palent 非表示にするノードの親ノード
  let displayNone = function displayNone(child, palent){
    
    //ノードを非表示にする
    child.element.classList.add('unexpand');

    //隣のノードのcss名を変更する
    //子ノードが親ノードの先頭にあるなら
    if(chainparser.isEqual(child, palent.child[0])){
      //@var string 子ノードの次のノードのcss名
      let className = child.element.nextElementSibling.className;
      //normaltreeならば、次のノードは、先頭になるので、firstree
      if(className === 'normaltree'){
        child.element.nextElementSibling.className = 'firsttree';
        //lastreeならば、次のノードは、1つしかないので、lastnormaltree
      }else if(className === 'lasttree'){
        child.element.nextElementSibling.className = 'lastnormaltree'
      }
      //子ノードが親ノードの最後にあるなら
    }else if(chainparser.isEqual(child, palent.child[palent.child.length - 1])){
      //@var string 子ノードの一つ前のノード
      let className = child.element.previousElementSibling.className;
      //expandtreeならば、前のノードは、最後のexpandtreeになる
      if(className === 'expandtree'){
        child.element.previousElementSibling.className = 'lastexpandntree';
        //firsttreeならば、最後のnormaltreeになる
      }else if(className === 'firsttree'){
        child.element.previousElementSibling.className = 'lastnormaltree';
        //normaltreeならば、最後のtreeになる
      }else if(className === 'normaltree'){
        child.element.previousElementSibling.className = 'lasttree';
      }
    }
  }

  //隠蔽したノードを表示する。投影も含めて
  //@param Nodeクラス 表示するノード
  let displayOpenNode = function(node) {
    //displayを変更する
    node.display = true;
    //@var Nodeクラス 表示するノードの親ノード
    let palent = chainparser.searchPalentNode(node.id, tree);
    //ノードを表示する
    displayOpen(node, palent);
    //表示ノードに投影先があるなら、投影先も表示する
    if(node.toLink !== []){
      //投影先のidをループする
      node.toLink.forEach(linkNodeId => {
        //@var Nodeクラス 表示先のノード
        let linkNode = chainparser.searchNodeId(linkNodeId, tree);
        //displayを変更する
        linkNode.display = true;
        //@var Nodeクラス 投影先の親ノード
        let linkPalent = chainparser.searchPalentNode(linkNodeId, tree);
        //投影先を表示する
        displayOpen(linkNode, linkPalent);
      });
    }
  }

  //隠蔽ノードを表示に替える
  //@var Nodeクラス child 表示するノード
  //@var Nodeクラス palent 表示するノードの親ノード
  let displayOpen = function displayOpen(child, palent){
    //ノードを表示にする
    child.element.classList.remove('unexpand');

    //隣のノードのcss名を変更する
    //子ノードが親ノードの先頭にあるなら
    if(chainparser.isEqual(child, palent.child[0])){
      //@var string 子ノードの次のノードのcss名
      let className = child.element.nextElementSibling.className;
      //firsttreeならば、次のノードは、normaltree
      if(className === 'firsttree'){
        child.element.nextElementSibling.className = 'normaltree';
        //lastnormalreeならば、次のノードは、最後なので、lasttree
      }else if(className === 'lastnormaltree'){
        child.element.nextElementSibling.className = 'lasttree'
      }
      //子ノードが親ノードの最後にあるなら
    }else if(chainparser.isEqual(child, palent.child[palent.child.length - 1])){
      //@var string 子ノードの一つ前のノード
      let className = child.element.previousElementSibling.className;
      //lastexpandtreeならば、前のノードは、expandtreeになる
      if(className === 'lastexpandtree'){
        child.element.previousElementSibling.className = 'expandntree';
        //lastnormaltreeならば、最初のtreeになる
      }else if(className === 'lastnormaltree'){
        child.element.previousElementSibling.className = 'firsttree';
        //lasttreeならば、normaltreeとなる
      }else if(className === 'lasttree'){
        child.element.previousElementSibling.className = 'normaltree';
      }
    }
  }

  //全体の隠蔽したツリーを表示する
  let openTree = function openTree(){
    //ツリー全体をループする
    chainparser.concatNode(tree).forEach(node => {
      if(node.display === false){
        //displayがfalseならば、隠蔽しているので、表示する
        displayOpenNode(node);
      }
    });
  }

  //titleboxのディレクトリを取得する
  //@var dom 取得したいディレクトリのdom
  //@return string ディレクトリ
  let getTitleboxDir = function getTitleboxDir(dom){
    if(dom.id !== 'chaintree'){
      //@var string 親ノードのディレクトリ
      let palentDir = getTitleboxDir(dom.parentElement);
      //展開するボックスならタイトルをディレクトリにつける
      if(dom.classList.value.match('expandtree') || dom.classList.value.match('lastexpandtree')){
        //@var string ノードのタイトル
        let title = dom.children[0].children[1].innerText;
        return palentDir + '/' + title;
      }
      return palentDir;
    }else if(dom.id === 'chaintree'){
      //chaintreeから呼び出し元に返る
      return '';
    }
  }

  //linetreeのディレクトリを取得する
  //@param dom ノードのdom
  //@param string ディレクトリ
  let getLinetreeDir = (dom) => {
    //ユーザー情報とログアウトは、notitleを付けて、返す
    if(dom.children[0].innerText === 'ユーザ情報' || dom.children[0].innerText === 'ログアウト'){
      return '/notitle/' + dom.children[0].innerText;
    }else{
      //ユーザー情報とログアウト以外は、マイツリーを付けて、返す
      return '/マイツリー/' + dom.children[0].innerText;
    }
  }

  // let getLinetreeDir = function 
  //linetreeにあるか判断する
  //@var string nodeDir ノードクラスのディレクトリ
  //@return boolean linetreeにあるか判断する
  let checkLineTree = function checkLineTree(nodeDir){
    
    //@var string topDir ディレクトリの第一階層のタイトル("部署")
    let topDir = getTopDir(nodeDir);
    //linetreeにあるか判断する
    if(topDir === "マイツリー" || topDir === "notitle"){
      return true;
    }else{
      return false;
    }
  }

  //一番上位が同じか、判断する
  //@var string toNodeDir 先頭先のディレクトリ
  //@var string fromNodeDir コピーしたノードのディレクトリ
  //@return boolean 第一階層が同じか判断する
  let checkSameTree = function checkSameTree(toNodeDir, fromNodeDir){
    //@var string toTopDir 第一階層のノードのタイトル
    let toTopDir = getTopDir(toNodeDir);
    
    //var sring fromToDir 第一階層のノードのタイトル
    let fromTopDir = getTopDir(fromNodeDir);
    //第一階層のタイトルが等しいなら、同じツリー内部にある
    if(toTopDir === fromTopDir){
      return true;
    }else{
      return false;
    }
  }

  //第一階層のタイトルを取得する
  //@param string nodeDir ディレクトリ
  //@return string ディレクトリの第一階層のタイトル
  let getTopDir = function getTopDir(nodeDir){
    //@var array splitToDir ディレクトリの文字を分割した配列
    let splitToDir = nodeDir.split('/');
    //先頭の空を削除する
    splitToDir.shift();
    //ディレクトリの一番先頭文字を取得する
    return splitToDir.shift();
  }

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
          storage[node.id] = node.id;
        }
      }
      //displayがfalseなら、hiddenstorageに保存する
      if(node.display === false){
        hiddenStorage[node.id] = node.id;
      }
    });
    
    //配列は保存できないので、json文字列に変換して保存する
    localStorage.setItem('id', JSON.stringify(storage));
    localStorage.setItem('hiddenId', JSON.stringify(hiddenStorage));
    //クリップボードのデータを保存する
    localStorage.setItem('currentId', clipboard.getCurrentId());
    localStorage.setItem('currentDir', clipboard.getCurrentDir());
    localStorage.setItem('selectId', clipboard.getSelectId());
    localStorage.setItem('selectDir', clipboard.getSelectDir());
    localStorage.setItem('copyId', clipboard.getCopyId());
    localStorage.setItem('copyDir', clipboard.getCopyDir());
  });

  //ページ移動後のイベント
  window.addEventListener('DOMContentLoaded', function(){
    //idをキーの存在で、ページ移動をしたと判断する
    if(localStorage.hasOwnProperty('id')){
      //@var array ツリーを開いていたノードのidの配列
      let storage = JSON.parse(localStorage.getItem('id'));
    
      //ツリーに対して変更があったか、判断する
      if(treeaction_chain !== null){
        //oepn,apend,updateなら、ノードを開く
        if(treeaction_chain['action'] === 'open'){
          chainparser.searchNodeId(treeaction_chain['id'], tree).openBottomUpTree();
        }else if(treeaction_chain['action'] === 'delete'){
          //deleteならば、そのノードを開かない
          if(treeaction_chain['id'] in storage){
            delete storage[treeaction_chain['id']];
          }
        }
      }
      //ページ移動前に開いていたノードを開く
      Object.keys(storage).forEach(id => {
        //@var Nodeクラス 開くノード
        let node = chainparser.searchNodeId(storage[id], tree);
        node.openBottomUpTree();
      });
      //@var array 隠蔽していたノードのid
      let hiddenStorage = JSON.parse(localStorage.getItem('hiddenId'));

      Object.keys(hiddenStorage).forEach(id => {
        //@var Nodeクラス 隠蔽するノード
        let node = chainparser.searchNodeId(hiddenStorage[id], tree);
        displayNoneNode(node);
      });
      //ページ移動前のクリップボードのデータを復元する
      clipboard.current(localStorage.getItem('currentDir'), localStorage.getItem('currentId'));
      clipboard.select(localStorage.getItem('selectDir'), localStorage.getItem('selectId'));
      clipboard.copyNode(localStorage.getItem('copyDir'), localStorage.getItem('copyId'));
      //@var Nodeクラス カレントノード
      let currentNode = chainparser.searchNodeId(clipboard.getCurrentId(), tree);
      //カレントノードをカレントにする
      if(currentNode !== undefined && currentNode !== null){
        currentNode.focus();
      }
    }
  });
  
  return {
    copyNode: copyNode,
    searchNodeId: searchNodeId,
    current: current,
    openNode: openNode,
    closeNode: closeNode,
    openTree: openTree,
    addNodeClickEvent: addNodeClickEvent,
    changeDisplay: changeDisplay
  }

})(treeChain, projectionChain);

TreeAction.addNodeClickEvent(function(){
  //@var string Laravelのセッションid
  let clientId = document.getElementById('parent').parentElement.children.client_id.value;
  //@var string ノードのid
  let nodeId = this.id;
  //移動命令
  window.location = `http://localhost:8000/show/${clientId}/${nodeId}`;
});

//隠蔽のイベント
document.getElementById('parent').children[3].children[0].children[1].children[6].addEventListener('click', () => {
  //@var string 詳細行の部署のid
  let nodeId = document.getElementById('parent').children[0].children[1].children[0].innerText.substr(3);
  //隠蔽のメソッド
  TreeAction.changeDisplay(nodeId);
});

document.getElementById('openTree').addEventListener('click', () => {
  TreeAction.openTree();
});

export {TreeAction};