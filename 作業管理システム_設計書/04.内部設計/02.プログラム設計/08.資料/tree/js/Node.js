import { ChainParser } from "./ChainParser.js";
import {clipboard} from "./Clipboard.js";

//ツリーのノードクラス
  //ノードのdom要素を操作する
export class Node {
    
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
      this.element;

      //@var array toLink 投影先のリンク、idとディレクトリ
      this.toLink = [];

      //@var array fromLink 投影元のリンク、idとディレクトリ
      this.fromLink = [];

    }

    //ツリーノードのdomを作成する
    //@param Nodeクラス node ツリーのdomを作成するノード
    //@return dom node.element 作成したツリーのdom
    static createTree(node){

      //domを作成して、node.elementに代入する。
      node.element = Node.createElement(node);

      if(node.child !== []){

        //子要素がある子要素のdomを追加する
        node.child.forEach(child => {

          //Node.createTree(child)によって子要素のdomを作成しつつ、
          //返り値の子要素のdomを追加する
          Node.appendTree(node, Node.createTree(child));
        });
      }
      return node.element;
    }

    //ノードのdom要素を生成する。
    //@param Nodeクラス node domを追加されるNodeクラス
    //return dom element sdom要素
    static createElement(node) {

      //@param dom element 返り値のdom
      let element;

      //展開されるツリーのdom生成
      if(node.className === 'expandtree' || node.className === 'lastexpandtree'){

        element = Node.createExpandTree(node);

      }else if(node.className === 'linetree'){
        //linetreeのdom生成

        element = Node.createLineTree(node);

      }else{
        //expandtreeとlinetree以外のdom生成
        //secondtree,endtreeはlinetreeの単要素
        //first,normaltree,lastnormaltree,lastreeはexpandtreeの単要素

        element = Node.createLeafTree(node);

      }
      return element;
    }

    //expandtree系を作成
    static createExpandTree(node){
      //div要素を生成する。
      let div = Node.createFirstdiv(node.className);
        
      //展開するボタンのあるツリー生成、挿入する
      div.appendChild(Node.createTreeBox(node));
    
      //展開されるツリーを作成する
      //@var dom ul ul要素
      let ul = document.createElement('ul');
    
      //@var dom li li要素
      let li = document.createElement('li');

      //li要素をulに追加
      ul.appendChild(li);
    
      //クラスのdom要素のthis.elementフィールドに、データのdom要素を保存する。
      div.appendChild(ul);
      return div;
    }

    //linetreeを生成する。
    static createLineTree(node){
      //linetreeのdom生成

      //div要素の生成する
      let div = Node.createFirstdiv(node.className);

      //一番上に表示するタイトルがあれば、タイトルを挿入する
      //notitleでなければ
      if(node.title === 'マイツリー'){
        div.innerText = node.title;
        //クリック処理。テスト用のクリック処理
        //クリックした要素を表示する
        div.node = node;
        div.clipboard = clipboard;
        
        div.addEventListener('click', Node.displayDetail);
        //
      }
      
      return div;
    }

    //@param Nodeクラス
    //@return dom ul
    //expandtree系やlinetree以外のdomを生成する
    static createLeafTree(node){
      //expandtreeとlinetree以外のdom生成
      //secondtree,endtreeはlinetreeの単要素
      //first,normaltree,lastnormaltree,lastreeはexpandtreeの単要素

      //@var dom ul ul要素
      let ul = document.createElement('ul');
        
      //classの追加により、cssを適用させる
      ul.classList.add(node.className);
        
      //@var dom li li要素
      let li = document.createElement('li');
        
      //li要素に、タイトルを挿入する
      li.innerText = node.title;
        
      //クリック処理。テスト用のクリック処理
      //クリックした要素を表示する
      li.node = node;
      li.clipboard = clipboard;
        
      li.addEventListener('click', Node.displayDetail);
      //
        
      ul.appendChild(li);

      return ul;
    }

    //ノードクラスにdomを最後に追加
    //@param Nodeクラス node 追加対象のノードクラス
    //@param dom element 追加するdom要素
    static appendTree(node, element){
      
      //展開されるツリーのdom挿入
      if(node.className === 'expandtree' || node.className === 'lastexpandtree'){

        //@var dom li expandtree系の子要素のdomの最初親要素はli
        let li = node.element.children[1].children[0];
        li.appendChild(element);

      }else if(node.className === 'linetree'){
        //linetreeのdom挿入

        //linetree系は、div要素の並びなのでそのままdomを追加する
        node.element.appendChild(element);

      }
    }

    //treeの特定位置に挿入
    //@param Nodeクラス node 挿入されるノードクラスクラス
    //@param dom element 挿入するdom
    //@param int deleteId 挿入の基準となる子要素のid
    static insertTree(node, element, deleteId){

      //@var 挿入する基準となるdom
      let insertElement;
      
      //基準のidがゼロ以上(最初以外)は、その前のdomを取得して、後ろにdomを挿入する
      if(deleteId > 0){
        insertElement = node.child[deleteId - 1].element;
        insertElement.after(element);
      }else{

        //基準がゼロの場合(最初のdom場合)は、後ろのdomを取得して、前に挿入する
        insertElement = node.child[deleteId + 1].element;
        insertElement.before(element);
      }
    }

    //ツリーの子要素を削除
    //@param Nodeクラス node
    //node.childのelementのdom要素を削除
    static deleteElement(node, deleteId) {
      if(node.className === 'expandtree' || node.className === 'lastexpandtree'){
        //expandtree系の場合li要素から、nodeのchildのdom要素を取得している。
        //@var dom li 削除されるdom要素の起点
        let li = node.element.children[1].children[0];
        
        li.children[deleteId].remove();
      }else{

        //treeTopの場合
        node.element.children[deleteId].remove();
      }
    }

    //削除などのツリーの再構成 nodeのchildのelement
    //@param Node node domを再構成するNodeクラス
    static resettingClassElement(node) {

      //@var dom li nodeのelementのli要素、node.childのelement 
      let li = node.element.children[1].children[0];
        
      for(let i = 0; i < node.child.length; i++){

        //unexpand以外のcssの要素名を削除して、新しいcss名を付ける
        li.children[i].classList.remove('expandtree', 'lastexpandtree', 'normaltree', 'lastnormaltree', 'lasttree', 'firsttree', 'expandbox');
        li.children[i].classList.add(node.child[i].className);
      }
    }

    //ボックスのボタンとタイトルのdom要素を生成する。
    //@param Nodeクラス node expandtree系のNodeクラス
    //@return dom divTreeBox ボックスのボタンとタイトルがあるdom要素
    static createTreeBox(node) {

      //@var dom divTreeBox 返すdom要素
      //div要素の生成
      let divTreeBox = document.createElement('div');
      
      //class名を付ける
      divTreeBox.classList.add('treebox');
      
      //二つのdom要素を生成して、挿入する
      //expandboxは、プラス、マイナスのボタンがあるボックス
      //titleboxは、ボタンの横にあるタイトル
      divTreeBox.appendChild(Node.createExpandBox(node));
      divTreeBox.appendChild(Node.createTitleBox(node));

      return divTreeBox;
    }

    //expandboxの展開されるボックスの生成
    static createExpandBox(node){
      //@var dom div divTreeBoxに追加するdom
      let div = document.createElement('div');
      //boxValueの0番目に、クラス名
      div.classList.add('expandbox');
        
      //boxValueの1番目に、表示する文字。
      div.innerText = '-';
      
      //expandboxに、ツリーを展開するクリック処理をつける。
      //クリックメソッドにnodeの引数を渡す
      div.node = node;

      //ボックスを展開するクリックイベントを付ける
      Node.addExpandEvent(div);

      return div;
    }

    //@param dom div クリックイベントを付けるdom
    //クリックイベントを付ける
    static addExpandEvent(div){
      //@param イベント　event
      div.addEventListener('click',(event) => {

        //@var Nodeクラス node クリック処理のメソッドに渡す
        let node = event.target.node;

        //+-文字の変化
        Node.ChangeBox(node);

        //ツリーのhtmlの表示、非表示を切り替える
        Node.changeChildDispaly(node);
      });
    }

    //titleboxの生成
    static createTitleBox(node){
      //@var dom div divTreeBoxに追加するdom
      let div = document.createElement('div');
      //boxValueの0番目に、クラス名
      div.classList.add('titlebox');
        
      //boxValueの1番目に、表示する文字。
      div.innerText = node.title;

      //クリックメソッドにnodeの引数を渡す
      div.node = node;
      div.clipboard = clipboard;
      div.addEventListener('click', Node.displayDetail);

      return div;
    }

    //詳細行に表示
    static displayDetail(event) {
      //@var dom palent 表示する親のdom
      //@var dom child 表示する子のdom
      let palent = document.getElementById('palent');
      let child = document.getElementById('child');
    
      //文字の表示
      //@var array dirArray ディレクトリの文字を分割した配列
      let dirArray = event.target.node.dir.split('/');
      dirArray.pop();
      let topDir = dirArray.pop();
      palent.innerText = topDir;
      child.innerText = event.target.node.title;
      if(topDir === "マイツリー"){
        let id = event.target.node.id;
        let selectNode = ChainParser.searchNodeId(id, document.children[0].tree);
        Node.openBottomUpTree(selectNode, document.children[0].tree);
      }
      event.target.clipboard.select(event.target.node);
    }

    static openBottomUpTree(node, tree){
      let splitDir = node.dir.split('/');
      splitDir.pop();
      if(splitDir.length !== 1){
        let palent = ChainParser.searchNodeDir(splitDir.join('/'), tree);
        Node.openDisplayNode(palent);
        Node.openBottomUpTree(palent, tree);
      }
    }

    //@param Nodeクラス node 非表示にするノードクラス
    //子要素全体を非表示にする
    static noneDisplayTree(node) {

      //expandtree系の場合
      if(node.className === 'expandtree' || node.className === 'lastexpandtree'){
        Node.noneDisplayNode(node);
        Node.ChangeBox(node);
      }
      node.child.forEach(child => {
        Node.noneDisplayTree(child);
      });
    }

    //palentのchildのelementを非表示にする。
    //@param Nodeクラス palent 非表示にするNodeクラス
    static noneDisplayNode(palent){

      //@var HTMLCollection node expandtree系のul要素のli要素の子要素の配列 
      let node = palent.element.children[1].children[0].children;
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

    //@param Nodeクラス palent 展開する子要素の親
    //ツリーのボックスを展開する
    static openDisplayNode(palent){

      //@var array node expandtreeの子要素のdomの配列
      let node = palent.element.children[1].children[0].children;
      
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

    //@param Nodeクラス Node expandtree系のツリー
    //ボックスのプラスマイナスの文字を変化させる
    static ChangeBox(node) {
      
      //@var dom box treeBoxのexpandboxのdom
      let box = node.element.children[0].children[0];
      
      //プラスマイナス文字を反転する。
      if(box.innerText === '+'){
        box.innerText = '-';
      }else if(box.innerText === '-'){
        box.innerText = '+';
      }
    }

    //@param Nodeクラス node ボックスのプラス文字を反転するノード
    static openBox(node){
      //@var dom box treeBoxのexpandboxのdom
      let box = node.element.children[0].children[0];
    
      //プラス文字を反転する。
      if(box.innerText === '+'){
        box.innerText = '-';
      }
    }

    //子要素の表示、非表示を切り替える。
    //@param Nodeクラス palent 子要素の親ノードクラス
    static changeChildDispaly(palent) {

      palent.child.forEach(child => {
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

    //@param string className divのcss名
    //@return dom div classNameがcss名のdiv
    static createFirstdiv(className) {
      
      //var dom div 返すdiv
      let div = document.createElement('div');
      div.classList.add(className);
      return div;
    }

    //@param Nodeクラス node コピーするノードクラス
    //@return Nodeクラス copyNode コピーしたノードクラス
    //オブジェクトの深い値コピーをする
    static deepCopyNode(node){

      //@var Nodeクラス copyNode コピーノードクラス
      //一からノードクラスを作成していく
      let copyNode = new Node(node.dir, node.id);
      copyNode.className = node.className;

      //dom要素は参照なので、cloneNodeで値コピーする
      copyNode.element = node.element.cloneNode(true);

      //投影元と投影先のディレクトリをコピーする。
      copyNode.toLink = [];
      node.toLink.forEach(link => {

        //linkの配列は参照なので、concatで値コピーしている
        copyNode.toLink.push(link.concat());
      });
      copyNode.fromLink = [];
      node.fromLink.forEach(link => {

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
        Node.addExpandEvent(expandboxDiv);

        //@var dom titleboxDiv 展開するボックスのタイトル
        let titleboxDiv = copyNode.element.children[0].children[1];
        //タイトルの表示イベントを付ける
        titleboxDiv.node = copyNode;
        titleboxDiv.clipboard = clipboard;
        titleboxDiv.addEventListener('click', Node.displayDetail);

      }else{
        //展開するボックスではなく線だけのツリーの場合

        //@var dom li 線だけのツリーのli要素
        let li = copyNode.element.children[0];
        //クリック処理。テスト用のクリック処理
        //クリックした要素を表示する
        li.node = copyNode;
        li.clipboard = clipboard;
        
        li.addEventListener('click', Node.displayDetail);
        //
      }
      
      //子要素のコピー
      node.child.forEach(child => {

        //子要素のコピーを作成して、追加
        copyNode.child.push(Node.deepCopyNode(child));
        
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