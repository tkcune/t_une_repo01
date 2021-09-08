//クリップボードのクラス
class ClipBoard {
  constructor() {

    //@var string selectNodeDir 選択しているノードディレクトリ
    //@var string nodeDir コピーしているノードディレクトリ
    //@var int selectNodeId 選択しているノードのid
    //@var int nodeId コピーしているノードのid
    this.selectNodeDir = null;
    this.nodeDir = null;
    this.selectNodeId = null;
    this.nodeId = null;
  }

  //@param Nodeくらす node クリックボードに保存される選択したノードクラス
  //詳細行に表示する
  select(node){
    
    //クリップボードのプロパティに代入する
    this.selectNodeDir = node.dir;
    this.selectNodeId = node.id;
  }

  //選択しているノードクラスのコピー
  copyNode(nodeDir, nodeId) {
    this.nodeDir = nodeDir;
    this.nodeId = nodeId;
  }

  getSelectDir(){
    return this.selectNodeDir;
  }

  getSelectId(){
    return this.selectNodeId;
  }

  getCopyDir(){
    return this.nodeDir;
  }

  getCopyId(){
    return this.nodeId;
  }
}

let clipboard = new ClipBoard();
export {clipboard}