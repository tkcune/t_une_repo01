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

  //@param string dir ノードディレクトリ
  //@param string id ノードのid
  //選択したノードのデータを保存する
  select(dir, id){
    
    //クリップボードのプロパティに代入する
    this.selectNodeDir = dir;
    this.selectNodeId = id;
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

export let clipboard = new ClipBoard();