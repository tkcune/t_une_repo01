import {clipboard} from "./Clipboard.js";
import { Node } from "./Node.js";
import { ChainParser } from "./ChainParser.js";
import {tree} from "./Tree.js";

export class TreeAction {
  //ノードを削除する
  static deleteNode(){
    if(TreeAction.checkLineTree(clipboard.getSelectDir()) === true){
      let node = TreeAction.searchNodeId(clipboard.getSelectId());
      NodeDelete.deleteNode(node.dir, node.id);
    }else{
      NodeDelete.deleteNode(clipboard.getSelectDir(), clipboard.getSelectId());
    }
  }

  //ノードを貼付する
  //@param array fromNodeChain 上下関係のオブジェクトのデータの配列
  static pasteNode(fromNodeChain){
    if(TreeAction.checkLineTree(clipboard.getSelectDir()) === true){
      let node = TreeAction.searchNodeId(clipboard.getSelectId());
      if(node.dir.split('/').pop() === "マイツリー"){
        NodePaste.pasteMyTreeNode(node.dir, node.id, fromNodeChain);
      }else{
        NodePaste.pasteNode(node.dir, node.id, clipboard.getCopyDir(), clipboard.getCopyId(), fromNodeChain);
      }
    }else{
      NodePaste.pasteNode(clipboard.getSelectDir(), clipboard.getSelectId(), clipboard.getCopyDir(), clipboard.getCopyId(), fromNodeChain);
    }
  }

  //ノードを投影する
  //@param array fromNodeChain 上下関係のオブジェクトのデータの配列
  static projectionNode(fromNodeChain){
    if(TreeAction.checkLineTree(clipboard.getSelectDir()) === true){
      let node = TreeAction.searchNodeId(clipboard.getSelectId());
      NodeProjection.projectionNode(node.dir, node.id, clipboard.getCopyDir(), clipboard.getCopyId(), fromNodeChain);
    }else{
      NodeProjection.projectionNode(clipboard.getSelectDir(), clipboard.getSelectId(), clipboard.getCopyDir(), clipboard.getCopyId(), fromNodeChain);
    }
  }

  //ツリーのノードを移動する。
  static moveNode(){
    if(TreeAction.checkLineTree(clipboard.getSelectDir()) === true){
      let node = TreeAction.searchNodeId(clipboard.getSelectId());
      NodeMove.moveNode(node.dir, node.id, clipboard.getCopyDir(), clipboard.getCopyId(), []);
    }else{
      NodeMove.moveNode(clipboard.getSelectDir(), clipboard.getSelectId(), clipboard.getCopyDir(), clipboard.getCopyId(), []);
    }
  }

  //クリップボードのデータを保存する
  static copyNode(){
    if(TreeAction.checkLineTree(clipboard.getSelectDir()) === true){
      let node = TreeAction.searchNodeId(clipboard.getSelectId());
      clipboard.copyNode(node.dir, node.id);
    }else{
      clipboard.copyNode(clipboard.getSelectDir(), clipboard.getSelectId());
    }
  }

  //ツリーのノードをディレクトリとidから検索する
  static searchNodeDirId(nodeDir, nodeId){
    return ChainParser.searchNodeDirId(nodeDir, nodeId, tree);
  }

  //ツリーのノードをディレクトリから検索する
  static searchNodeDir(nodeDir){
    return ChainParser.searchNodeDir(nodeDir, tree);
  }

  //ツリーのノードをidから検索する
  static searchNodeId(nodeId){
    return ChainParser.searchNodeId(nodeId, tree);
  }

  //linetreeにあるか判断する
  static checkLineTree(nodeDir){
    let splitDir = nodeDir.split('/');
    splitDir.shift();
    let topDir = splitDir.shift();
    if(topDir === "マイツリー" || topDir === "notitle"){
      return true;
    }else{
      return false;
    }
  }

  //一番上位が同じか、判断する
  static checkSameTree(toNodeDir, fromNodeDir){
    let splitToDir = toNodeDir;
    splitToDir.shift();
    let toTopDir = splitToDir.shift();
    let splitFromDir = fromNodeDir;
    splitFromDir.shift();
    let fromTopDir = splitFromDir.shift();
    if(toTopDir === fromTopDir){
      return true;
    }else{
      return false;
    }
  }
}

//移動クラス
class NodeMove {

  //@param string toNodeDir 移動先のノードクラスのディレクトリ
  //@param string toNodeId 移動先のノードクラスのid
  //@param string fromNodeDir 移動元のノードクラスのディレクトリ
  //@param string fromNodeId 移動元のノードクラスのid
  //ツリーのプログラムの移動機能
  static moveNode(toNodeDir, toNodeId, fromNodeDir, fromNodeId) {

    //@var Nodeクラス toNode 移動先のノードクラス
    let toNode = ChainParser.searchNodeDirId(toNodeDir, toNodeId, tree);
    //@var Nodeクラス formNode 移動元のノードクラス
    let fromNode = ChainParser.searchNodeDirId(fromNodeDir, fromNodeId, tree);

    //@var array fromTwoPalent 移動元のノードクラスと親要素(0番目)と親要素の親要素(1番目)のクラスの配列
    let fromTwoPalent = ChainParser.getTwoPalent(fromNode, tree);
    //@var Nodeクラス fromPalent 移動元の親要素のクラス
    let fromPalent = fromTwoPalent[0];

    //移動先の下に、貼り付けるので移動先のと移動元の親クラスが同じ場合
    //同じディレクトリに移動する事になるので、それはしない。
    if(ChainParser.isEqual(fromPalent, toNode) === false){
      //移動先に貼り付けてから移動元を削除する
      NodePaste.pasteNode(toNodeDir, toNodeId, fromNodeDir, fromNodeId, []);
      NodeDelete.deleteNode(fromNodeDir, fromNodeId);
    }
  }
}

//削除クラス
class NodeDelete {

  //@param string nodeDir 削除されるノードクラスのディレクトリ
  //@param string nodeId 削除されるノードクラスのid
  //削除機能
  static deleteNode(nodeDir, nodeId) {

    //@var Nodeクラス node 削除されるノードクラス
    let node = ChainParser.searchNodeDirId(nodeDir, nodeId, tree);

    //var array concatDeleteNode 削除されるノードクラスとそのノードクラスの子要素を一列に並べた配列
    let concatDeleteNode = ChainParser.concatNode(node);
    
    //削除されるノードクラスに投影元がある場合について
    //投影元のノードクラスのプロパティtoLink(投影先のディレクトリ)を削除する
    for(let i = 0; i < concatDeleteNode.length; i++){

      //@var array fromLink 投影元のディレクトリ
      let fromLink = concatDeleteNode[i].fromLink[0];
      if(fromLink){

        //@var Nodeクラス fromLinkNode 投影元のノードクラス
        let fromLinkNode = ChainParser.searchNodeDirId(fromLink[1], fromLink[0], tree);
        
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
          let toLinkNode = ChainParser.searchNodeDirId(link[1], link[0], tree);
          if(toLinkNode){
            //投影先のノードクラスを削除する
            NodeDelete.deleteTreeNode(toLinkNode);
          }
        });
      }
    }
    //選択先のノードクラスを削除する。投影元のデータを持つ子要素のノードクラスも削除する
    NodeDelete.deleteTreeNode(node);
  }

  //@param Nodeクラス node 削除するノードクラス
  //ノードクラスを削除するコード
  static deleteTreeNode(node){

    //@var array twoPalent 削除するノードクラスの親要素と親要素の親要素
    let twoPalent = ChainParser.getTwoPalent(node, tree);

    //@var Nodeクラス palent 削除するノードクラスの親要素
    let palent = twoPalent[0];
    //@var Nodeクラス palentPalent 削除するノードクラスの親要素の親要素
    let palentPalent = twoPalent[1];

    //@var int deleteId domの中で削除するノードクラスの番号
    let deleteId;
    //親要素のdomから、削除するノードクラスの位置を検索する
    for(let i = 0; i < palent.child.length; i++){
      if(ChainParser.isEqual(palent.child[i], node)){
        deleteId = i;
      }
    }
    
    //削除する要素を親要素のchildから取り除く。
    //親ノードから削除するノードクラスのデータを削除する
    palent.child = palent.child.filter(child => ((child.dir === node.dir) && (child.id === node.id)) === false);
    
    //ノードクラスのデータを削除した後に、ツリーの構造が変わるので、css名を変更する
    ChainParser.decisionChildClass(palent, palentPalent);
    
    //削除するノードクラスのdomを削除する
    //palent.child.lengthが0ならば、子要素がない。
    //子要素がないので、親要素を新しく作り直す
    //子要素がないなら、expandtree,lastexpandtree以外のツリーになる。
    if(palent.child.length === 0){

      //作り直すツリーを一度削除するので、削除するidを探索する。
      for(let i = 0; i < palentPalent.child.length; i++){
        if(ChainParser.isEqual(palentPalent.child[i], palent)){
          deleteId = i;
        }
      }
      //親要素の親要素から、親要素を削除する
      Node.deleteElement(palentPalent, deleteId);

      //親要素のdomを作成する
      palent.element = Node.createElement(palentPalent.child[deleteId]);

      //@var dom insertElement 新しく作成した親要素のdomを挿入する基準となるdom要素
      let insertElement;
      //expandtree系の場合
      if(palentPalent.className === 'expandtree' || palentPalent.className === 'lastexpandtree'){

        //親の親ノードクラスの子要素がひとつの場合、子要素のdomを削除する。挿入する基準がないので、追加となる
        if(palentPalent.child.length === 1){
          Node.appendTree(palentPalent, palent.element);
        }else{
          //親の親ノードクラスの子要素がふたつ以上の場合、挿入する基準があるので、挿入。
          Node.insertTree(palentPalent, palent.element, deleteId);
        }
        
      }else{
        //chaintreeの場合
        //ひとつ前のdomを取得する
        insertElement = palentPalent.element.children[deleteId - 1];
        
        //ひとつ前のdomから後ろに挿入する
        insertElement.after(palent.element);
        Node.ChangeBox(palentPalent.child[deleteId]);
      }

    }else{
      //削除されるノードクラスの親要素の子要素がふたつ以上の場合
      //ノードクラスをdomから削除する
      Node.deleteElement(palent, deleteId);
      //親ノードの子要素のcssを変更している部分をdomに反映させる。
      Node.resettingClassElement(palent);
    }
  }
}

//貼り付けクラス
class NodePaste {

  //@param string toNodeDir 貼付先のノードクラスのディレクトリ
  //@param string toNodeId 貼付先のノードクラスのid
  //@param string fromNodeDir 貼付元のノードクラスのディレクトリ
  //@param string fromNodeId 貼付元のノードクラスのid
  //@param array fromNodeChain 貼付元のidを新しく更新した上下関係のオブジェクトのデータ
  static pasteNode(toNodeDir, toNodeId, fromNodeDir, fromNodeId, fromNodeChain) {
    
    //@var Nodeクラス toNode 貼付先のノードクラス
    let toNode = ChainParser.searchNodeDirId(toNodeDir, toNodeId, tree);

    //@var Nodeクラス fromNode 貼付元のノードクラスのコピー
    let fromNode = Node.deepCopyNode(ChainParser.searchNodeDirId(fromNodeDir, fromNodeId, tree));

    //コピーしたクラスのディレクトリを更新する
    ChainParser.recreateDir(fromNode, toNodeDir);

    //上下関係のオブジェクトのデータがある場合について、idを更新する
    //移動の機能時は、idを更新しない
    //idを更新する
    if(fromNodeChain !== []){
      ChainParser.recreateId(fromNode, fromNodeChain);
    }

    //投影元がある場合は、投影元のノードクラスに、貼付先のデータを追加する
    //@var array concarFromNode コピーしたノードとそのノードの子要素を一列に並べた配列
    let concatFromNode = ChainParser.concatNode(fromNode);
    for(let i = 0; i < concatFromNode.length; i++){

      //@var array fromLink 投影元のディレクトリデータ
      let fromLink = concatFromNode[i].fromLink[0];
      
      if(fromLink){

        //@var Nodeクラス formLinkNode 投影元のノードクラス
        let fromLinkNode = ChainParser.searchNodeDirId(fromLink[1], fromLink[0], tree);
        //投影元のノードクラスに、貼付先のデータを追加する
        fromLinkNode.toLink.push([concatFromNode[i].id, concatFromNode[i].dir]);
        
      }
    }
    //貼付先に、コピーした貼付元のデータを追加する
    toNode.child.push(fromNode);

    NodePaste.appendFromNode(toNode, fromNode);
  }

  //@param Nodeクラス toNode 貼付先のノードクラス
  //@param Nodeクラス fromNode 貼付元のノードクラスのコピー
  static appendFromNode(toNode, fromNode){
    //@var array twoPalent 貼付先の親ノードと親の親ノードの配列
    let twoPalent = ChainParser.getTwoPalent(toNode, tree);

    //@var Nodeクラス 貼付先の親ノード
    let palent = twoPalent[0];
    //@var Nodeクラス 貼付先の親の親ノード
    let palentPalent = twoPalent[1];

    //親からclassNameを決め直す
    ChainParser.decisionChildClass(palent, palentPalent);


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
        if(ChainParser.isEqual(palent.child[i], toNode)){
          deleteId = i;
        }
      }
      
      //domを削除する
      Node.deleteElement(palent, deleteId);
      //貼付先のdomを展開するボックスに作り直す
      toNode.element = Node.createExpandTree(toNode);
      
      //貼付先に、コピーした移動元のdomを追加する
      Node.appendTree(toNode, fromNode.element);
      
      //貼付先の子要素のcss名を、domに反映させる
      Node.resettingClassElement(toNode);

      //新しく作り直した貼付先のdomを、親ノードのdomに追加する
      Node.insertTree(palent, toNode.element, deleteId);
      
      //親ノードに新しくdomを追加したので、
      //親ノードの子要素のcss名を、domに反映させる
      Node.resettingClassElement(palent);
    }else{

      //貼付先が展開するボックスのあるツリーの場合
      //貼付先のボックスを開く。
      Node.openBox(toNode);

      //貼付先の子要素を表示する
      //NodePaste.openDispaly(toNode);
      Node.openDisplayNode(toNode);
      //コピーした貼付元のdomを追加する
      Node.appendTree(toNode, fromNode.element);
      //貼付先の子要素のcss名を、domに反映させる
      Node.resettingClassElement(toNode);
    }
  }

  //マイツリーへの登録
  //@param string toNodeDir マイツリーのディレクトリ
  //@param string toNodeId マイツリーのid
  //@param array fromNodeChain 上下関係のオブジェクトのデータの配列
  static pasteMyTreeNode(toNodeDir, toNodeId, fromNodeChain){
    //@var Object chain 上下関係のオブジェクトのデータ
    let chain = fromNodeChain[0];
    //@var Nodeクラス toNode マイツリーのNodeクラス
    let toNode = TreeAction.searchNodeDirId(toNodeDir, toNodeId);
    //@var string appendDir マイツリーに登録するノードのディレクトリ
    let appendDir = '/' + Object.keys(chain)[0].split('.')[1] + '/' + Object.values(chain)[0].split('.')[1];
    //@var string appendId マイツリーに登録するノードのid
    let appendId = Object.values(chain)[0].split('.')[0];
    //@var Nodeクラス appendNode マイツリーに登録するノードクラス
    let appendNode = new Node(appendDir, appendId);
    //マイツリーの子要素のcss名はsecondtree
    appendNode.className = "secondtree";
    //登録するノードクラスのdomを作成し、マイツリーに挿入する
    Node.appendTree(toNode, Node.createTree(appendNode));
  }
}

//投影
class NodeProjection {

  //@param string toNodeDir 投影先のノードクラスのディレクトリ
  //@param string toNodeId 投影先のノードクラスのid
  //@param string fromNodeDir 投影元のノードクラスのディレクトリ
  //@param string fromNodeId 投影元のノードクラスのid
  //@param array fromNodeChain 投影元のidを新しく更新した上下関係のオブジェクトのデータ
  static projectionNode(toNodeDir, toNodeId, fromNodeDir, fromNodeId, fromNodeChain) {
    //@var Nodeクラス toNode 投影先のノードクラス
    let toNode = ChainParser.searchNodeDirId(toNodeDir, toNodeId, tree);
    
    //@var Nodeクラス copyFromNode 投影元のノードクラスをコピーしたノードクラス
    let fromNode = Node.deepCopyNode(ChainParser.searchNodeDirId(fromNodeDir, fromNodeId, tree));

    //コピーしたノードクラスのディレクトリを投影先のディレクトリで更新する
    ChainParser.recreateDir(fromNode, toNodeDir);

    //idを更新する
    if(fromNodeChain !== []){
      ChainParser.recreateId(fromNode, fromNodeChain);
    }

    //投影元とコピーしたノードのプロパティの投影先と投影元のデータを代入する
    ChainParser.syncLinkNode(fromNodeDir, fromNodeId, fromNode, tree);
    
    //投影先に、コピーしたノードを追加する
    toNode.child.push(fromNode);

    //投影先に投影元のdomを追加する
    NodeProjection.appendFromNode(toNode, fromNode);
  }


  //@param Nodeクラス toNode 投影先のノードクラス
  //@param Nodeクラス fromNode 投影元のノードクラスのコピー
  static appendFromNode(toNode, fromNode){
    //@var array twoPalent 投影先のノードクラスの親ノードと親の親ノード
    let twoPalent = ChainParser.getTwoPalent(toNode, tree);

    //@var Nodeクラス palent 投影先の親ノード
    let palent = twoPalent[0];
    //@var Nodeクラス palentPalent 投影先の親の親ノード
    let palentPalent = twoPalent[1];

    //親からclassNameを決め直す
    ChainParser.decisionChildClass(palent, palentPalent);

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
        if(ChainParser.isEqual(palent.child[i], toNode)){
          deleteId = i;
        }
      }
      
      //domを削除する
      Node.deleteElement(palent, deleteId);
      //投影先のdomを展開するボックスに作り直す
      toNode.element = Node.createExpandTree(toNode);
      
      //投影先に、コピーした移動元のdomを追加する
      Node.appendTree(toNode, fromNode.element);
      
      //投影先の子要素のcss名を、domに反映させる
      Node.resettingClassElement(toNode);

      //新しく作り直した投影先のdomを、親ノードのdomに追加する
      Node.insertTree(palent, toNode.element, deleteId);
      
      //親ノードに新しくdomを追加したので、
      //親ノードの子要素のcss名を、domに反映させる
      Node.resettingClassElement(palent);
    }else{

      //投影先が展開するボックスのあるツリーの場合
      //投影先のボックスを開く。
      Node.openBox(toNode);

      //投影先の子要素を表示する
      Node.openDisplayNode(toNode);
      //コピーした投影元のdomを追加する
      Node.appendTree(toNode, fromNode.element);
      //投影先の子要素のcss名を、domに反映させる
      Node.resettingClassElement(toNode);
    }
  }

}