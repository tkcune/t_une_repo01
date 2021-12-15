//@var Clipboardクラス クリップボードクラス

let clipboard = (() => {
  
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
    if(typeof(dir) === 'string' && typeof(id) === 'string'){
      //クリップボードのプロパティに代入する
      selectNodeDir = dir;
      selectNodeId = id;
    }
  };

  //カレント
  //@param string dir カレントのディレクトリ
  //@param string id カレントのid
  let current = (dir, id) => {
    if(typeof(dir) === 'string' && typeof(id) === 'string'){
      //カレントのデータを代入する
      currentDir = dir;
      currentId = id;
    }
  }

  //選択したノードのディレクトリを返す
  let getSelectDir = () => {
    return selectNodeDir;
  };

  //選択したノードのidを返す
  let getSelectId = () => {
    return selectNodeId;
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
    getSelectDir: getSelectDir,
    getSelectId: getSelectId,
    getCurrentId: getCurrentId,
    getCurrentDir: getCurrentDir
  };
})();
export {clipboard}