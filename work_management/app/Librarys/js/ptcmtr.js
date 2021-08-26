  import {Node} from "./ptcmnd.js";
import {ChainParser} from "./ptcmcp.js";

let treeChain = [
  {'マイツリー': '部署A'},
  {'マイツリー': '作業場所A'},
  {'マイツリー': '作業場所B'},
  {'マイツリー': '作業B'},
  {'部署': '部署A'},
  {'部署': '部署B'},
  {'部署A': '部署A-1'},
  {'部署A': '部署A-2'},
  {'部署A-1': '部署A-1-1'},
  {'部署A-1': '部署A-1-2'},
  {'作業': '作業A'},
  {'作業': '作業B'},
  {'作業B': '作業C'},
  {'notitle': 'ユーザー情報'},
  {'notitle': 'ログアウト'}
];

//プログラムで、使用するデータ
//chaintreeを第一階層のdivのclassの名前
//ツリーのまとまりごとに、配列が分かれている。
let treeSepalete = [
  [
    {'1.chaintree': '2.マイツリー'},
    {'2.マイツリー': '16.部署AA'},
    {'2.マイツリー': '6.部署B'},
    {'2.マイツリー': '36.所属'},
    {'2.マイツリー': '14.作業場所A'},
    {'2.マイツリー': '15.作業場所B'},
    {'2.マイツリー': '20.作業B'}
  ],
  [
    {'1.chaintree': '3.部署'},
    {'3.部署': '5.部署A'},
    {'3.部署': '6.部署B'},
    {'5.部署A': '7.部署A-1'},
    {'5.部署A': '8.部署A-2'},
    {'7.部署A-1': '9.部署A-1-1'},
    {'7.部署A-1': '10.部署A-1-2'},
    {'6.部署B': '16.部署AA'},
    {'16.部署A': '17.部署A-1'},
    {'16.部署A': '18.部署A-2'}
  ],
  [
    {'1.chaintree': '36.所属'},
    {'36.所属': '37.所属A'},
    {'36.所属': '38.所属B'},
    {'36.所属': '39.所属C'},
    {'37.所属A': '40.人員A'},
    {'37.所属A': '41.人員B'},
    {'38.所属B': '42.人員C'},
    {'38.所属B': '43.人員D'},
  ],
  [
    {'1.chaintree': '4.作業'},
    {'4.作業': '19.作業A'},
    {'4.作業': '20.作業B'},
    {'20.作業B': '21.作業C'},
  ],
  [
    {'1.chaintree': '22.作業場所'},
    {'22.作業場所': '14.作業場所A'},
    {'22.作業場所': '15.作業場所B'},
    {'14.作業場所A': '23.作業場所C'}
  ],
  [
    {'1.chaintree': '24.作業定義'},
    {'24.作業定義': '25.作業定義A'},
    {'24.作業定義': '26.作業定義B'},
    {'25.作業定義A': '27.作業定義C'},
    {'25.作業定義A': '28.作業定義D'},
    {'26.作業定義B': '29.作業定義E'}
  ],
  [
    {'1.chaintree': '30.作業指示'},
    {'30.作業指示': '32.作業指示A'},
    {'30.作業指示': '33.作業指示B'},
    {'30.作業指示': '34.作業指示C'},
    {'32.作業指示A': '35.作業指示D'}
  ],
  [
    {'1.chaintree': '44.掲示板'},
    {'44.掲示板': '45.スレッドA'},
    {'44.掲示板': '46.スレッドB'},
    {'45.スレッドA': '47.スレッドC'},
    {'45.スレッドA': '48.スレッドD'},
    {'46.スレッドB': '49.スレッドE'}
  ],
  [
    {'1.chaintree': '0.notitle'},
    {'0.notitle': '12.ユーザー情報'},
    {'0.notitle': '13.ログアウト'}
  ]
];

//全体のツリーの階層を構築
//<div class="chaintree"></div>からツリーを生成する。
//Nodeクラスの構築、CSS名の決定、クリック処理の設定、htmlの描画
//Nodeクラスを使用する
class Tree {
  
  //@param array sepalete 上下関係だけのツリーのデータ構造
  //@return Node treeTop ツリーの全体クラス
  //全体のツリーの作成
  static createTreeClass(treesepalete) {

    //@var nodeクラス this.treeTop ツリーの一番上のノード
    let treeTop = new Node('', 1);

    //@var dom this.element dom要素を格納 ツリーのdom要素を加えていく一番上のツリー<div id="chaintree"></div>
    treeTop.element = document.getElementById('chaintree');
    
    //@var string this.className ツリーのcssのクラス名
    treeTop.className = 'chaintree';
    
    //トップツリーの子要素のクラスを作成して、追加。
    treesepalete.forEach(sepalete => {

      //treeTopのchildに、かたまりごとのノードを追加していく。
      treeTop.child.push(Tree.createTopNode(sepalete, treeTop));
    });

    //ツリーの全体のcssのクラス名を決める
    Tree.decisionClass(treeTop);
    //domを生成して、ツリーを描画する
    Tree.createElement(treeTop);

    //ツリーのdom要素を生成した時は、ツリーは表示しているので、非表示にする。
    treeTop.child.forEach(child => {
      Node.noneDisplayTree(child);
    });

    return treeTop;
  }

  //ツリーの子要素のクラスを決定する。
  static decisionClass(treeTop){
    treeTop.child.forEach(node => {
      ChainParser.decisionChildClass(node, treeTop);
    });
  }

  //ツリーの子要素のdom要素を構築、追加していく。
  static createElement(treeTop) {
    treeTop.child.forEach(node => {
      //<div id="chaintree"></div>にdomを追加
      treeTop.element.append(Node.createTree(node));
      
    });
  }

  //@param array sepalete 上下関係だけのツリーのデータ構造
  //@param Nodeクラス treeTop　ツリーの一番上の親ノードのクラス
  //@return Nodeクラス topNode 親ノードクラス
  static createTopNode(sepalete ,treeTop) {
    
    //@var Nodeクラス this.topNode ひとかたまりのツリーの一番上のNodeクラス
    let topNode;
    
    //親要素のchain(例{'1.chaintree', '4.作業'})を取り除いた配列
    let exceptSepalete = ChainParser.exceptSepalete(sepalete);
    
    //topNodeを作成する
    //{'1.chaintree': '5.aa'}sepaleteのデータの中で、keyがchaintreeの値が一番上の親
    sepalete.forEach(chain => {
      if(Object.keys(chain)[0] === '1.chaintree'){
        topNode = new Node(treeTop.dir + '/' + Object.values(chain)[0].split('.')[1], Object.values(chain)[0].split('.')[0]);
      }
    });
    //データからクラスの階層構造を作成する。
    Tree.parse(topNode, exceptSepalete);

    //Nodeクラスの第一階層のcss名を決定する。
    ChainParser.decisionTreeClass(topNode);

    return topNode;
  }

  //nodeを再帰的に追加していく。
  //@param Nodeクラス node ノードの子要素を追加
  //@return Nodeクラス node 再起メソッドの返り値
  static parse(node, sepalete) {
    
    //nodeの子要素があるか、探索する
    sepalete.forEach(chain => {
      
      //chain変数のkeyに、親要素のidがある。
      //node.idが親要素のid
      if(node.id === Object.keys(chain)[0].split('.')[0]){

        //@var Nodeクラス child 子ノードクラス。parseで子ノードの子要素を追加する
        let child = new Node(node.dir + '/' + Object.values(chain)[0].split('.')[1], Object.values(chain)[0].split('.')[0]);
        
        //子要素がある場合は、parseをもう一度呼び出す。
        //子要素の子要素を構築していく。
        node.child.push(Tree.parse(child, sepalete));
      }
    });
    return node;
  }

}

let tree = Tree.createTreeClass(treeSepalete);

export {tree}