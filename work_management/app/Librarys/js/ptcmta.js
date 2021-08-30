import {clipboard} from "./ptcmcb";

//TreeActionの即時関数に定義され、returnされたメソッドが公開するメソッド。
//TreeActionの名前空間はTreeActionの即時関数で利用される。
//@var TreeActionクラス 公開するクラス
let TreeAction = {};

//TreeActionの名前空間

//ツリーのdom生成
TreeAction.createTree = {};

//ツリーのNodeクラス
TreeAction.node = {};

//ツリーのチェインパーサークラス
TreeAction.chainparser = {};

//ツリーの削除機能クラス
TreeAction.delete = {};

//ツリーの貼付機能クラス
TreeAction.paste = {};

//ツリーの移動機能クラス
TreeAction.move = {};

//ツリーの投影クラス
TreeAction.projection = {};

//ツリーの追加クラス
TreeAction.append = {};

//ノードクラス
TreeAction.node = class Node {
    
  //@param string dir ツリーのディレクトリ
  //@param int id ノードのid
  constructor(nodeDir, id) {

    //@var string dir ツリーのディレクトリ
    this.dir = nodeDir;

    //@var int id データベースのid
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

    //一番上に表示するタイトルがあれば、タイトルを挿入する
    //notitleでなければ
    if(this.title === 'マイツリー'){
      div.innerText = this.title;
      //クリック処理。テスト用のクリック処理
      //クリックした要素を表示する
      div.node = this;
      div.clipboard = clipboard;
      
      div.addEventListener('click', this.displayDetail);
      //
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
      
    //li要素に、タイトルを挿入する
    li.innerText = this.title;
      
    //クリック処理。テスト用のクリック処理
    //クリックした要素を表示する
    li.node = this;
    li.clipboard = clipboard;
      
    li.addEventListener('click', this.displayDetail);
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

      //+-文字の変化
      this.ChangeBox();

      //ツリーのhtmlの表示、非表示を切り替える
      this.changeChildDispaly();
    });
  }

  //titleboxの生成
  //@return dom div 展開ボックスのタイトルのdom
  createTitleBox(){
    //@var dom div divTreeBoxに追加するdom
    let div = document.createElement('div');
    //boxValueの0番目に、クラス名
    div.classList.add('titlebox');
      
    //boxValueの1番目に、表示する文字。
    div.innerText = this.title;

    //クリックメソッドにnodeの引数を渡す
    div.node = this;
    div.clipboard = clipboard;
    div.addEventListener('click', this.displayDetail);

    return div;
  }

  //詳細行に表示
  displayDetail(event) {
    //@var dom palent 表示する親のdom
    //@var dom child 表示する子のdom
    let palent = document.getElementById('palent');
    let child = document.getElementById('child');
  
    //文字の表示
    //@var array dirArray ディレクトリの文字を分割した配列
    let dirArray = event.target.node.dir.split('/');
    //一番後ろの文字を削除
    dirArray.pop();
    //親要素のタイトルを取得する
    let topDir = dirArray.pop();
    palent.innerText = topDir;
    child.innerText = event.target.node.title;
    //選択したノードの親要素がマイツリーである場合は、本来のノードクラスまでツリーを開く
    if(topDir === "マイツリー"){
      //@var string id 選択したノードクラスのid
      let id = event.target.node.id;
      //@var Nodeクラス selectNode 本来のノードクラス
      let selectNode = event.target.node.prototype.chainparser.searchNodeId(id, event.target.node.prototype.tree);
      //本来のノードクラスまでツリーを開く
      selectNode.openBottomUpTree( event.target.node.prototype.tree);
    }
    //クリップボードに選択したノードクラスのidとディレクトリを保存する
    event.target.clipboard.select(event.target.node);
  }

  //目的のノードクラスまでツリーを開く
  //@var Nodeクラス tree ツリーの全体のインスタンス
  openBottomUpTree(tree){
    //@var array splitDir ディレクトリの文字を分割した配列
    let splitDir = this.dir.split('/');
    //最後の文字を削除する
    splitDir.pop();
    //配列が1の時は、chaintreeなので、openBottomUpTreeを呼ばない
    if(splitDir.length !== 1){
      //@var Nodeクラス palent 目的のノードクラスの親要素
      let palent = this.prototype.chainparser.searchNodeDir(splitDir.join('/'), tree);
      //目的のノードクラスの親要素のツリーを開く
      palent.openBox();
      palent.openDisplayNode();
      //また親要素を引数にして再帰的に、openBottomUpTreeを呼び出す
      palent.openBottomUpTree(tree);
    }
  }

  //@param Nodeクラス node 非表示にするノードクラス
  //子要素全体を非表示にする
  noneDisplayTree() {

    //expandtree系の場合
    if(this.className === 'expandtree' || this.className === 'lastexpandtree'){
      this.noneDisplayNode();
      this.ChangeBox();
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

  openBox(){
    //@var dom box treeBoxのexpandboxのdom
    let box = this.element.children[0].children[0];
  
    //プラス文字を反転する。
    if(box.innerText === '+'){
      box.innerText = '-';
    }
  }

  //子要素の表示、非表示を切り替える。
  changeChildDispaly() {

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
      titleboxDiv.node = copyNode;
      titleboxDiv.clipboard = clipboard;
      titleboxDiv.addEventListener('click', this.displayDetail);

    }else{
      //展開するボックスではなく線だけのツリーの場合

      //@var dom li 線だけのツリーのli要素
      let li = copyNode.element.children[0];
      //クリック処理。テスト用のクリック処理
      //クリックした要素を表示する
      li.node = copyNode;
      li.clipboard = clipboard;
      
      li.addEventListener('click', this.displayDetail);
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
}

//Chainparserクラス
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
      //dirが同じであれば、同じノード
      let isEqual = function isEqual(node, compairNode) {
      
        if(node.dir !== compairNode.dir){
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

      //チェインパーサーのメソッド
      return {
        getTwoPalent: getTwoPalent,
        recreateId: recreateId,
        recreateDir: recreateDir,
        syncLinkNode: syncLinkNode,
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

//@param array treesepalete ツリーの上下関係のオブジェクトのデータの配列
//@param array projectionChain ツリーの投影関係のオブジェクトのデータの配列
//@param Nodeクラス Node Nodeクラスのインスタンス
//@param ChainParserクラス chainparser チェインパーサーのクラスのインスタンス
TreeAction.createTree = function(treesepalete, projectionChain, Node, chainparser) {
  //ツリーの子要素のクラスを決定する。
  let decisionClass = function decisionClass(treeTop){
    treeTop.child.forEach(node => {
      chainparser.decisionChildClass(node, treeTop);
    });
  }

  //ツリーの子要素のdom要素を構築、追加していく。
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
      let fromNode = chainparser.searchNodeId(Object.keys(chain)[0].split('.')[0], treeTop);
      let toNode = chainparser.searchNodeId(Object.values(chain)[0].split('.')[0], treeTop);
      chainparser.syncLink(fromNode, toNode);
    });
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
  //domを生成して、ツリーを描画する
  createElement(treeTop);

  //ツリーのdom要素を生成した時は、ツリーは表示しているので、非表示にする。
  treeTop.child.forEach(child => {
    child.noneDisplayTree();
  });

  //投影
  syncProjection(projectionChain);

  return treeTop;
}

//@param ChainParserクラス chainparser チェインパーサーのクラスのインスタンス
//@param Nodeクラス tree ツリー全体のクラスのインスタンス
//ツリーの削除機能クラス
TreeAction.delete = function(chainparser, tree) {
  
  //@param string nodeDir 削除されるノードクラスのディレクトリ
  //@param string nodeId 削除されるノードクラスのid
  //削除機能
  let deleteNode = function deleteNode(nodeDir, nodeId) {

    //@var Nodeクラス node 削除されるノードクラス
    let node = chainparser.searchNodeDirId(nodeDir, nodeId, tree);

    //var array concatDeleteNode 削除されるノードクラスとそのノードクラスの子要素を一列に並べた配列
    let concatDeleteNode = chainparser.concatNode(node);
    
    //削除されるノードクラスに投影元がある場合について
    //投影元のノードクラスのプロパティtoLink(投影先のディレクトリ)を削除する
    for(let i = 0; i < concatDeleteNode.length; i++){

      //@var array fromLink 投影元のディレクトリ
      let fromLink = concatDeleteNode[i].fromLink[0];
      if(fromLink){

        //@var Nodeクラス fromLinkNode 投影元のノードクラス
        let fromLinkNode = chainparser.searchNodeId(fromLink, tree);
        
        //投影元のノードクラスから、投影先のディレクトリのデータを削除する
        fromLinkNode.toLink = fromLinkNode.toLink.filter(link => ((link[0] === concatDeleteNode[i].id) && (link[1] === concatDeleteNode[i].dir)) !== true); 
      }
    }

    //削除されるノードクラスに投影先がある場合について
    //投影元が削除されるので、投影先のノードクラスも削除する
    for(let i = 0; i < concatDeleteNode.length; i++){

      //@var array toLink 投影先のディレクトリ配列
      let toLink = concatDeleteNode[i].toLink;
      if(toLink){

        //投影先は複数ある場合があるので、ループする
        toLink.forEach(link => {

          //@var Nodeクラス toLinkNode 投影先のノードクラス
          let toLinkNode = chainparser.searchNodeId(link, tree);
          if(toLinkNode){
            //投影先のノードクラスを削除する
            deleteTreeNode(toLinkNode);
          }
        });
      }
    }
    //選択先のノードクラスを削除する。投影元のデータを持つ子要素のノードクラスも削除する
    deleteTreeNode(node);
  }

  //@param Nodeクラス node 削除するノードクラス
  //ノードクラスを削除するコード
  let deleteTreeNode = function deleteTreeNode(node){

    //@var array twoPalent 削除するノードクラスの親要素と親要素の親要素
    let twoPalent = chainparser.getTwoPalent(node, tree);

    //@var Nodeクラス palent 削除するノードクラスの親要素
    let palent = twoPalent[0];
    //@var Nodeクラス palentPalent 削除するノードクラスの親要素の親要素
    let palentPalent = twoPalent[1];

    //@var int deleteId domの中で削除するノードクラスの番号
    let deleteId;
    //親要素のdomから、削除するノードクラスの位置を検索する
    for(let i = 0; i < palent.child.length; i++){
      if(chainparser.isEqual(palent.child[i], node)){
        deleteId = i;
      }
    }
    
    //削除する要素を親要素のchildから取り除く。
    //親ノードから削除するノードクラスのデータを削除する
    palent.child = palent.child.filter(child => ((child.dir === node.dir) && (child.id === node.id)) === false);
    
    //ノードクラスのデータを削除した後に、ツリーの構造が変わるので、css名を変更する
    chainparser.decisionChildClass(palent, palentPalent);
    
    //削除するノードクラスのdomを削除する
    //palent.child.lengthが0ならば、子要素がない。
    //子要素がないので、親要素を新しく作り直す
    //子要素がないなら、expandtree,lastexpandtree以外のツリーになる。
    if(palent.child.length === 0){

      //作り直すツリーを一度削除するので、削除するidを探索する。
      for(let i = 0; i < palentPalent.child.length; i++){
        if(chainparser.isEqual(palentPalent.child[i], palent)){
          deleteId = i;
        }
      }
      //親要素の親要素から、親要素を削除する
      palentPalent.deleteElement(deleteId);

      //親要素のdomを作成する
      palent.createElement();

      //@var dom insertElement 新しく作成した親要素のdomを挿入する基準となるdom要素
      let insertElement;
      //expandtree系の場合
      if(palentPalent.className === 'expandtree' || palentPalent.className === 'lastexpandtree'){

        //親の親ノードクラスの子要素がひとつの場合、子要素のdomを削除する。挿入する基準がないので、追加となる
        if(palentPalent.child.length === 1){
          palentPalent.appendTree(palent.element);
        }else{
          //親の親ノードクラスの子要素がふたつ以上の場合、挿入する基準があるので、挿入。
          palentPalent.insertTree(palent.element, deleteId);
        }
        
      }else{
        //chaintreeの場合
        //ひとつ前のdomを取得する
        insertElement = palentPalent.element.children[deleteId - 1];
        
        //ひとつ前のdomから後ろに挿入する
        insertElement.after(palent.element);
        palentPalent.ChangeBox(palentPalent.child[deleteId]);
      }

    }else{
      //削除されるノードクラスの親要素の子要素がふたつ以上の場合
      //ノードクラスをdomから削除する
      palent.deleteElement(deleteId);
      //親ノードの子要素のcssを変更している部分をdomに反映させる。
      palent.resettingClassElement();
    }
  }
  //削除機能メソッドを返す
  return {
    deleteNode: deleteNode
  }
}

//@param Nodeクラス Node Nodeクラスのインスタンス
//@param ChainParser chainparser チェインパーサーのクラスのインスタンス
//@param Node tree ツリーの全体クラスのインスタンス
//ツリーの貼付機能クラス
TreeAction.paste = function(Node, chainparser, tree){
  //@param string toNodeDir 貼付先のノードクラスのディレクトリ
  //@param string toNodeId 貼付先のノードクラスのid
  //@param string fromNodeDir 貼付元のノードクラスのディレクトリ
  //@param string fromNodeId 貼付元のノードクラスのid
  //@param array fromNodeChain 貼付元のidを新しく更新した上下関係のオブジェクトのデータ
  let pasteNode = function pasteNode(toNodeDir, toNodeId, fromNodeDir, fromNodeId, fromNodeChain) {
    
    //@var Nodeクラス toNode 貼付先のノードクラス
    let toNode = chainparser.searchNodeDirId(toNodeDir, toNodeId, tree);

    //@var Nodeクラス fromNode 貼付元のノードクラスのコピー
    let fromNode = chainparser.searchNodeDirId(fromNodeDir, fromNodeId, tree).deepCopyNode();

    //コピーしたクラスのディレクトリを更新する
    chainparser.recreateDir(fromNode, toNodeDir);

    //上下関係のオブジェクトのデータがある場合について、idを更新する
    //移動の機能時は、idを更新しない
    //idを更新する
    if(fromNodeChain !== []){
      chainparser.recreateId(fromNode, fromNodeChain);
    }

    //投影元がある場合は、投影元のノードクラスに、貼付先のデータを追加する
    //@var array concarFromNode コピーしたノードとそのノードの子要素を一列に並べた配列
    let concatFromNode = chainparser.concatNode(fromNode);
    for(let i = 0; i < concatFromNode.length; i++){

      //@var array fromLink 投影元のディレクトリデータ
      let fromLink = concatFromNode[i].fromLink[0];
      
      if(fromLink){

        //@var Nodeクラス formLinkNode 投影元のノードクラス
        let fromLinkNode = chainparser.searchNodeId(fromLink, tree);
        //投影元のノードクラスに、貼付先のデータを追加する
        fromLinkNode.toLink.push([concatFromNode[i].id, concatFromNode[i].dir]);
        
      }
    }
    //貼付先に、コピーした貼付元のデータを追加する
    toNode.child.push(fromNode);

    appendFromNode(toNode, fromNode);
  }

  //@param Nodeクラス toNode 貼付先のノードクラス
  //@param Nodeクラス fromNode 貼付元のノードクラスのコピー
  let appendFromNode = function appendFromNode(toNode, fromNode){
    //@var array twoPalent 貼付先の親ノードと親の親ノードの配列
    let twoPalent = chainparser.getTwoPalent(toNode, tree);

    //@var Nodeクラス 貼付先の親ノード
    let palent = twoPalent[0];
    //@var Nodeクラス 貼付先の親の親ノード
    let palentPalent = twoPalent[1];

    //親からclassNameを決め直す
    chainparser.decisionChildClass(palent, palentPalent);


    //貼付先にdomを追加する

    //親要素のdom要素の削除と再構成
    //ノードクラスを追加した時に、貼付先の子要素が1の場合
    //新しく子要素ができた事になるので、貼付先を展開するボックスのあるツリーに変更する
    if(toNode.child.length === 1){
      //貼付先を一度削除して、ツリーを作り直す
      //@var int deleteId 削除する貼付先のdomの位置
      let deleteId;
      //削除する位置を検索する
      for(let i = 0; i < palent.child.length; i++){
        if(chainparser.isEqual(palent.child[i], toNode)){
          deleteId = i;
        }
      }
      
      //domを削除する
      palent.deleteElement(deleteId);
      //貼付先のdomを展開するボックスに作り直す
      toNode.createExpandTree();
      
      //貼付先に、コピーした移動元のdomを追加する
      toNode.appendTree(fromNode.element);
      
      //貼付先の子要素のcss名を、domに反映させる
      toNode.resettingClassElement();

      //新しく作り直した貼付先のdomを、親ノードのdomに追加する
      palent.insertTree(toNode.element, deleteId);
      
      //親ノードに新しくdomを追加したので、
      //親ノードの子要素のcss名を、domに反映させる
      palent.resettingClassElement();
    }else{

      //貼付先が展開するボックスのあるツリーの場合
      //貼付先のボックスを開く。
      toNode.openBox();

      //貼付先の子要素を表示する
      toNode.openDisplayNode();
      //コピーした貼付元のdomを追加する
      toNode.appendTree(fromNode.element);
      //貼付先の子要素のcss名を、domに反映させる
      toNode.resettingClassElement();
    }
  }

  //マイツリーへの登録
  //@param string toNodeDir マイツリーのディレクトリ
  //@param string toNodeId マイツリーのid
  //@param array fromNodeChain 上下関係のオブジェクトのデータの配列
  let pasteMyTreeNode = function pasteMyTreeNode(toNodeDir, toNodeId, fromNodeChain){
    //@var Object chain 上下関係のオブジェクトのデータ
    let chain = fromNodeChain[0];
    //@var Nodeクラス toNode マイツリーのNodeクラス
    let toNode = chainparser.searchNodeDirId(toNodeDir, toNodeId);
    //@var string appendDir マイツリーに登録するノードのディレクトリ
    let appendDir = '/' + Object.keys(chain)[0].split('.')[1] + '/' + Object.values(chain)[0].split('.')[1];
    //@var string appendId マイツリーに登録するノードのid
    let appendId = Object.values(chain)[0].split('.')[0];
    //@var Nodeクラス appendNode マイツリーに登録するノードクラス
    let appendNode = new Node(appendDir, appendId);
    //マイツリーの子要素のcss名はsecondtree
    appendNode.className = "secondtree";
    //登録するノードクラスのdomを作成し、マイツリーに挿入する
    toNode.appendTree(appendNode.createTree());
  }

  //ツリーの貼付機能メソッドを返す
  return {
    pasteNode: pasteNode,
    pasteMyTreeNode: pasteMyTreeNode
  }
}

//@param ChainParser チェインパーサーのクラスのインスタンス
//@param Nodeクラス tree ツリーのインスタンス
//@param nodePaste pasteFunction 貼付機能のクラス
//@param nodeDelete deleteFunction 削除機能のクラス
//移動機能のメソッド
TreeAction.move = function(chainparser, tree, pasteFunction, deleteFunction){
  //@param string toNodeDir 移動先のノードクラスのディレクトリ
  //@param string toNodeId 移動先のノードクラスのid
  //@param string fromNodeDir 移動元のノードクラスのディレクトリ
  //@param string fromNodeId 移動元のノードクラスのid
  //ツリーのプログラムの移動機能
  let moveNode = function moveNode(toNodeDir, toNodeId, fromNodeDir, fromNodeId) {

    //@var Nodeクラス toNode 移動先のノードクラス
    let toNode = chainparser.searchNodeDirId(toNodeDir, toNodeId, tree);
    //@var Nodeクラス formNode 移動元のノードクラス
    let fromNode = chainparser.searchNodeDirId(fromNodeDir, fromNodeId, tree);

    //@var array fromTwoPalent 移動元のノードクラスと親要素(0番目)と親要素の親要素(1番目)のクラスの配列
    let fromTwoPalent = chainparser.getTwoPalent(fromNode, tree);
    //@var Nodeクラス fromPalent 移動元の親要素のクラス
    let fromPalent = fromTwoPalent[0];

    //移動先の下に、貼り付けるので移動先のと移動元の親クラスが同じ場合
    //同じディレクトリに移動する事になるので、それはしない。
    if(chainparser.isEqual(fromPalent, toNode) === false){
      //移動先に貼り付けてから移動元を削除する
      pasteFunction.pasteNode(toNodeDir, toNodeId, fromNodeDir, fromNodeId, []);
      deleteFunction.deleteNode(fromNodeDir, fromNodeId);
    }
  }
  //移動機能のメソッドを返す
  return {
    moveNode: moveNode
  }
}

//@param ChainParser chainparser チェインパーサーのクラス
//@param Node tree ツリーのインスタンス
//投影機能
TreeAction.projection = function(chainparser, tree){
  //@param string toNodeDir 投影先のノードクラスのディレクトリ
  //@param string toNodeId 投影先のノードクラスのid
  //@param string fromNodeDir 投影元のノードクラスのディレクトリ
  //@param string fromNodeId 投影元のノードクラスのid
  //@param array fromNodeChain 投影元のidを新しく更新した上下関係のオブジェクトのデータ
  let projectionNode = function projectionNode(toNodeDir, toNodeId, fromNodeDir, fromNodeId, fromNodeChain) {
    //@var Nodeクラス toNode 投影先のノードクラス
    let toNode = chainparser.searchNodeDirId(toNodeDir, toNodeId, tree);
    
    //@var Nodeクラス copyFromNode 投影元のノードクラスをコピーしたノードクラス
    let fromNode = chainparser.searchNodeDirId(fromNodeDir, fromNodeId, tree).deepCopyNode();

    //コピーしたノードクラスのディレクトリを投影先のディレクトリで更新する
    chainparser.recreateDir(fromNode, toNodeDir);

    //idを更新する
    if(fromNodeChain !== []){
      chainparser.recreateId(fromNode, fromNodeChain);
    }

    //投影元とコピーしたノードのプロパティの投影先と投影元のデータを代入する
    chainparser.syncLinkNode(fromNodeId, fromNode, tree);
    
    //投影先に、コピーしたノードを追加する
    toNode.child.push(fromNode);

    //投影先に投影元のdomを追加する
    appendFromNode(toNode, fromNode);
  }


  //@param Nodeクラス toNode 投影先のノードクラス
  //@param Nodeクラス fromNode 投影元のノードクラスのコピー
  let appendFromNode = function appendFromNode(toNode, fromNode){
    //@var array twoPalent 投影先のノードクラスの親ノードと親の親ノード
    let twoPalent = chainparser.getTwoPalent(toNode, tree);

    //@var Nodeクラス palent 投影先の親ノード
    let palent = twoPalent[0];
    //@var Nodeクラス palentPalent 投影先の親の親ノード
    let palentPalent = twoPalent[1];

    //親からclassNameを決め直す
    chainparser.decisionChildClass(palent, palentPalent);

    //貼付先にdomを追加する

    //親要素のdom要素の削除と再構成
    //ノードクラスを追加した時に、投影先の子要素が1の場合
    //新しく子要素ができた事になるので、投影先を展開するボックスのあるツリーに変更する
    if(toNode.child.length === 1){
      //投影先を一度削除して、ツリーを作り直す
      //@var int deleteId 削除する貼付先のdomの位置
      let deleteId;
      //削除する位置を検索する
      for(let i = 0; i < palent.child.length; i++){
        if(chainparser.isEqual(palent.child[i], toNode)){
          deleteId = i;
        }
      }
      
      //domを削除する
      palent.deleteElement(deleteId);
      //投影先のdomを展開するボックスに作り直す
      toNode.createExpandTree();
      
      //投影先に、コピーした移動元のdomを追加する
      toNode.appendTree(fromNode.element);
      
      //投影先の子要素のcss名を、domに反映させる
      toNode.resettingClassElement();

      //新しく作り直した投影先のdomを、親ノードのdomに追加する
      palent.insertTree(toNode.element, deleteId);
      
      //親ノードに新しくdomを追加したので、
      //親ノードの子要素のcss名を、domに反映させる
      palent.resettingClassElement();
    }else{

      //投影先が展開するボックスのあるツリーの場合
      //投影先のボックスを開く。
      toNode.openBox();

      //投影先の子要素を表示する
      toNode.openDisplayNode();
      //コピーした投影元のdomを追加する
      toNode.appendTree(fromNode.element);
      //投影先の子要素のcss名を、domに反映させる
      toNode.resettingClassElement();
    }
  }

  //投影機能メソッドを返す
  return {
    projectionNode: projectionNode
  };
}

//@param Nodeクラス Node Nodeクラスのインスタンス
//@param ChainParser chainparser チェインパーサーのクラスのインスタンス
//@param Nodeクラス tree ツリーのインスタンス
//ツリーの追加機能
TreeAction.append = function(Node, chainparser, tree){

  //@param string toNodeDir 追加先のツリーのディレクトリ
  //@param string toNodeId 追加先のツリーのid
  //@param array fromNodeChain 上下関係のオブジェクトのデータ
  let nodeAppend = function nodeAppend(toNodeDir, toNodeId, fromNodeChain){

    //@var Nodeクラス toNode 追加先のツリーのクラス
    let toNode = chainparser.searchNodeDirId(toNodeDir, toNodeId, tree);
    //@var Object chain 上下関係のオブジェクトのデータ
    let chain = fromNodeChain[0];
    //@var Nodeクラス fromNode 追加するノードクラス
    let fromNode = new Node(toNodeDir + "/" + Object.values(chain)[0].split('.')[1], Object.values(chain)[0].split('.')[0]);
    //追加先のノードクラスに追加する
    toNode.child.push(fromNode);
    //ノードクラスのdomを作成する
    appendFromNode(toNode, fromNode);
  }

  //@param Nodeクラス toNode 追加先のノードクラス
  //@param Nodeクラス fromNode 追加するノードクラス
  let appendFromNode = function appendFromNode(toNode, fromNode){
    //@var array twoPalent 追加先のノードクラスの親ノードと親の親ノード
    let twoPalent = chainparser.getTwoPalent(toNode, tree);

    //@var Nodeクラス palent 追加先の親ノード
    let palent = twoPalent[0];
    //@var Nodeクラス palentPalent 追加先の親の親ノード
    let palentPalent = twoPalent[1];

    //親からclassNameを決め直す
    chainparser.decisionChildClass(palent, palentPalent);
    fromNode.createLeafTree();

    //追加先にdomを追加する

    //親要素のdom要素の削除と再構成
    //ノードクラスを追加した時に、投影先の子要素が1の場合
    //新しく子要素ができた事になるので、投影先を展開するボックスのあるツリーに変更する
    if(toNode.child.length === 1){
      //追加先を一度削除して、ツリーを作り直す
      //@var int deleteId 削除する貼付先のdomの位置
      let deleteId;
      //削除する位置を検索する
      for(let i = 0; i < palent.child.length; i++){
        if(chainparser.isEqual(palent.child[i], toNode)){
          deleteId = i;
        }
      }
      
      //domを削除する
      palent.deleteElement(deleteId);
      //追加先のdomを展開するボックスに作り直す
      toNode.createExpandTree();
      
      //追加先に、コピーした移動元のdomを追加する
      toNode.appendTree(fromNode.element);
      
      //追加先の子要素のcss名を、domに反映させる
      toNode.resettingClassElement();

      //新しく作り直した追加先のdomを、親ノードのdomに追加する
      palent.insertTree(toNode.element, deleteId);
      
      //親ノードに新しくdomを追加したので、
      //親ノードの子要素のcss名を、domに反映させる
      palent.resettingClassElement();
    }else{

      //追加先が展開するボックスのあるツリーの場合
      //追加先のボックスを開く。
      toNode.openBox();

      //追加先の子要素を表示する
      toNode.openDisplayNode();
      //コピーした投影元のdomを追加する
      toNode.appendTree(fromNode.element);
      //追加先の子要素のcss名を、domに反映させる
      toNode.resettingClassElement();
    }
  }

  //追加機能メソッドを返す
  return {
    nodeAppend: nodeAppend
  }
}

//@param array treesepalete 上下関係のオブジェクトのデータ
//@param array projectionChain 投影関係のメソッド
//ツリーアクションの機能クラス
TreeAction = ((treesepalete, projectionChain) => {
  //@var Nodeクラス Node ノードクラス
  let Node = TreeAction.node;
  //@var ChainParserクラス chainparser チェインパーサーのクラス
  let chainparser = TreeAction.chainparser;
  //@var Nodeクラス tree ツリーのインスタンス
  let tree = TreeAction.createTree(treesepalete, projectionChain, Node, chainparser);
  //@var deleteNode deleteFunction 削除機能のクラス
  let deleteFunction = TreeAction.delete(chainparser, tree);
  //@var pasteNode pasteFucntion 貼付機能のクラス
  let pasteFunction = TreeAction.paste(Node, chainparser, tree);
  //@var moveNode moveFuction 移動機能のメソッド
  let moveFunction = TreeAction.move(chainparser, tree, pasteFunction, deleteFunction);
  //@var projectionNode projectionFunction 投影機能のメソッド
  let projectionFunction = TreeAction.projection(chainparser, tree);
  //@var appendNode appendFunction 追加機能のメソッド
  let appendFunction = TreeAction.append(Node, chainparser, tree);

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

  //ノードを削除する
  let deleteNode = function deleteNode(){

    //選択したノードがマイツリーにあるかチェックする
    if(checkLineTree(clipboard.getSelectDir()) === true){

      //マイツリーのノードは、本来のデータが置いてあるノードとは違うので、本来のノードを検索する。
      //@var Nodeクラス node マイツリーに登録してあるノードの本来のノードクラス
      let node = searchNodeId(clipboard.getSelectId());
      //ノードを削除する
      deleteFunction.deleteNode(node.dir, node.id);
    }else{
      //マイツリーのノードではない場合
      deleteFunction.deleteNode(clipboard.getSelectDir(), clipboard.getSelectId());
    }
  }

  //ノードの貼付
  let pasteNode = function pasteNode(fromNodeChain){
    //@var string ディレクトリ
    let dir;
    //@var string selectNodeDir 選択したのノードのディレクトリ
    let selectNodeDir;
    //@var string selectNodeId 選択したノードのid
    let selectNodeId;
    //選択したノードがマイツリーにあるか判断する
    if(checkLineTree(clipboard.getSelectDir()) === true){
      //@var Nodeクラス node マイツリーにあるノードクラスとは違う本来のノードクラス
      let node = searchNodeId(clipboard.getSelectId());
      dir = node.dir.split('/').pop();
      selectNodeDir = node.dir;
      selectNodeId = node.id;
    }else{
      //選択したノードのidとディレクトリを代入する
      selectNodeDir = clipboard.getSelectDir();
      selectNodeId = clipboard.getSelectId();
    }
    //選択したノードがマイツリー本体の場合
    if(dir === "マイツリー"){
      //マイツリーに、ノードを登録する
      pasteFunction.pasteMyTreeNode(selectNodeDir, selectNodeId, fromNodeChain);
    }else{
      //同じツリーノードにあるか判断する。
      if(checkSameTree(selectNodeDir, clipboard.getCopyDir()) === true){
        //同じ場合本来のノードクラスに貼り付ける
        pasteFunction.pasteNode(selectNodeDir, selectNodeId, clipboard.getCopyDir(), clipboard.getCopyId(), fromNodeChain);
      }else{
        //作業定義から作業指示に、貼付するか判断する
        if(getTopDir(selectNodeDir).title.slice(0, 4) === "作業指示" && TreeAction.getTopDir(clipboard.getCopyDir()).title.slice(0, 4) === "作業定義"){
          //本来のノードクラスに貼り付ける
          pasteFunction.pasteNode(selectNodeDir, selectNodeId, clipboard.getCopyDir(), clipboard.getCopyId(), fromNodeChain);    
        }
      }
    }
  }

  let moveNode = function moveNode(){
    //@var string selectNodeDir 選択したのノードのディレクトリ
    let selectNodeDir;
    //@var string selectNodeId 選択したノードのid
    let selectNodeId;
    //マイツリーのノードを選択したか判断する
    if(checkLineTree(clipboard.getSelectDir()) === true){
      //@var Nodeクラス node マイツリーではない本来のノードクラス
      let node = searchNodeId(clipboard.getSelectId());
      selectNodeDir = node.dir;
      selectNodeId = node.id;
    }else{
      //選択したノードのidとディレクトリを代入する
      selectNodeDir = clipboard.getSelectDir();
      selectNodeId = clipboard.getSelectId();
    }
    //同じ第一階層下のノードにある場合に、移動する。
    if(checkSameTree(selectNodeDir, clipboard.getCopyDir()) === true){
      moveFunction.moveNode(selectNodeDir, selectNodeId, clipboard.getCopyDir(), clipboard.getCopyId());
    }
  }

  //ノードを投影する
  //@param array fromNodeChain 上下関係のオブジェクトのデータの配列
  let projectionNode = function projectionNode(fromNodeChain){
    //@var string selectNodeDir 選択したのノードのディレクトリ
    let selectNodeDir;
    //@var string selectNodeId 選択したノードのid
    let selectNodeId;
    //マイツリーにある判断する
    if(checkLineTree(clipboard.getSelectDir()) === true){
      //@var Nodeクラス node マイツリーではない本来のノードクラス
      let node = searchNodeId(clipboard.getSelectId());
      selectNodeDir = node.dir;
      selectNodeId = node.id;
    }else{
      //選択したノードのidとディレクトリを代入する
      selectNodeDir = clipboard.getSelectDir();
      selectNodeId = clipboard.getSelectId();
    }
    //同じ第一階層下のノードにある場合に、移動する。
    if(checkSameTree(selectNodeDir, clipboard.getCopyDir()) === true){
      projectionFunction.projectionNode(selectNodeDir, selectNodeId, clipboard.getCopyDir(), clipboard.getCopyId(), fromNodeChain);
    }
  }

  //@param array fromNodeChain 上下関係のオブジェクトのデータ
  //追加機能のメソッド
  let appendNode = function appendNode(fromNodeChain){
    appendFunction.nodeAppend(clipboard.getSelectDir(), clipboard.getSelectId(), fromNodeChain);
  }

  //ツリーのノードをidから検索する
  //@var string nodeId ノードクラスのid
  //@return Nodeクラス 検索したノードクラス
  let searchNodeId = function searchNodeId(nodeId){
    return chainparser.searchNodeId(nodeId, tree).deepCopyNode();
  }

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

  //公開するメソッドを返す
  return {
    copyNode: copyNode,
    deleteNode: deleteNode,
    pasteNode: pasteNode,
    moveNode: moveNode,
    projectionNode: projectionNode,
    appendNode: appendNode,
    searchNodeId: searchNodeId
  }

  //引数を与える。
})(treeSepalete, projectionChain);