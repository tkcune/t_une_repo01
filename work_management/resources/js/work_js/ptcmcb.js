//@var Clipboardクラス クリップボードクラス
//プロパティ
//@var string nodeId コピーしているノードのid
//@var string nodeDir コピーしているノードディレクトリ
//@var string selectNodeId 選択しているノードのid
//@var string selectNodeDir 選択しているノードディレクトリ
//@var string カレントノードのid
//@var string カレントノードのディレクトリ

let clipboard = (() => {
  
  //@var string nodeId コピーしているノードのid
  let nodeId = null;
  //@var string nodeDir コピーしているノードディレクトリ
  let nodeDir = null;
  //@var string selectNodeId 選択しているノードのid
  let selectNodeId = null;
  //@var string selectNodeDir 選択しているノードディレクトリ
  let selectNodeDir = null;
  //@var string カレントノードのid
  let currentId = null;
  //@var string カレントノードのディレクトリ
  let currentDir = null;

  //選択する
  //@param string dir ノードのディレクトリ
  //@param string id ノードのid
  let select = (dir, id) => {
    //クリップボードのプロパティに代入する
    selectNodeDir = dir;
    selectNodeId = id;
  };

  //カレント
  //@param string dir カレントのディレクトリ
  //@param string id カレントのid
  let current = (dir, id) => {
    //カレントのデータを代入する
    currentDir = dir;
    currentId = id;
  }

  //選択しているノードクラスのコピー
  //@param string dir コピーノードのディレクトリ
  //@param string id コピーノードのid
  let copyNode = (dir, id) => {
    //コピーデータを代入する
    nodeDir = dir;
    nodeId = id;
  };

  //選択したノードのディレクトリを返す
  let getSelectDir = () => {
    return selectNodeDir;
  };

  //選択したノードのidを返す
  let getSelectId = () => {
    return selectNodeId;
  };

  //コピーノードのディレクトリを返す
  let getCopyDir = () => {
    return nodeDir;
  };

  //コピーノードのidを返す
  let getCopyId = () => {
    return nodeId;
  };

  //カレントのディレクトリを返す
  let getCurrentDir = () => {
    return currentDir;
  }

  //カレントのidを返す
  let getCurrentId = () => {
    return currentId;
  }

  return {
    select: select,
    current: current,
    copyNode: copyNode,
    getCopyDir: getCopyDir,
    getCopyId: getCopyId,
    getSelectDir: getSelectDir,
    getSelectId: getSelectId,
    getCurrentId: getCurrentId,
    getCurrentDir: getCurrentDir
  };
})();
export {clipboard}