'use strict';
import { clipboard } from "./Clipboard.js";
import {ChainParser} from "./ChainParser.js";
import {Node} from "./Node.js";
import {tree} from "./Tree.js";
import {TreeAction} from "./TreeAction.js";

  //コピーボタンのクリック処理
  let copyelement = document.getElementById('copy');
  let clickCopy = () => {
    TreeAction.copyNode();
    
    let pastepalent = document.getElementById('pastepalent');
    let pastechild = document.getElementById('pastechild');
    pastepalent.innerText = ChainParser.searchPalentTitle(ChainParser.searchNodeId(clipboard.nodeId, tree));
    pastechild.innerText = ChainParser.searchNodeId(clipboard.nodeId, tree).title;
  };
  copyelement.addEventListener('click', clickCopy);
  
  //削除ボタンのクリック処理を設定。テストコード
  let deleteelement = document.getElementById('delete');
  let clickDelete = () => {
    TreeAction.deleteNode();
  };
  deleteelement.addEventListener('click', clickDelete);
  
  //貼付ボタンのクリック処理を設定。テストコード
  let pasteelement = document.getElementById('paste');
  let clickPaste = () => {
    //上下関係のオブジェクトのデータを作成している。テストコード
    let fromNode = Node.deepCopyNode(ChainParser.searchNodeDirId(clipboard.nodeDir, clipboard.nodeId, tree));
    let toNode = ChainParser.searchNodeId(clipboard.selectNodeId, tree);
    let concatFromNode = ChainParser.concatNode(fromNode);
    let fromNodeChain = [];
    let topDirTitle = toNode.dir.split('/').pop();
    if(topDirTitle === "マイツリー"){
      fromNodeChain.push({[toNode.id + '.' + toNode.title]: concatFromNode[0].id + '.' + concatFromNode[0].title});
    }else{
      incrementNodeId(fromNode);
      fromNodeChain.push({[toNode.id + '.' + toNode.title]: concatFromNode[0].id + '.' + concatFromNode[0].title});
      for(let i = 0; i < concatFromNode.length; i++){
        if(concatFromNode[i].child !== []){
          concatFromNode[i].child.forEach(child => {
            fromNodeChain.push({[concatFromNode[i].id + '.' + concatFromNode[i].title]: child.id + '.' + child.title});
          });
        }
      }
    }
    
    TreeAction.pasteNode(fromNodeChain);
  };
  pasteelement.addEventListener('click', clickPaste);

  //移動ボタンのクリック処理を設定。テストコード
  let moveelement = document.getElementById('move');
  let clickMove = () => {
    TreeAction.moveNode();
    let pastepalent = document.getElementById('pastepalent');
    let pastechild = document.getElementById('pastechild');
    pastepalent.innerText = '';
    pastechild.innerText = '';
  };
  moveelement.addEventListener('click', clickMove);

  //投影ボタンのクリック処理を設定。テストコード
  let projectionelement = document.getElementById('projection');
  let clikcProjection = () => {
    //上下関係のオブジェクトのデータを作成している。テストコード
    let fromNode = Node.deepCopyNode(ChainParser.searchNodeDirId(clipboard.nodeDir, clipboard.nodeId, tree));
    incrementNodeId(fromNode);
    let toNode = ChainParser.searchNodeId(clipboard.selectNodeId, tree);
    
    let concatFromNode = ChainParser.concatNode(fromNode);
    
    let fromNodeChain = [];
    fromNodeChain.push({[toNode.id + '.' + toNode.title]: concatFromNode[0].id + '.' + concatFromNode[0].title});
    for(let i = 0; i < concatFromNode.length; i++){
      if(concatFromNode[i].child !== []){
        concatFromNode[i].child.forEach(child => {
          fromNodeChain.push({[concatFromNode[i].id + '.' + concatFromNode[i].title]: child.id + '.' + child.title});
        });
      }
    }
    TreeAction.projectionNode(fromNodeChain);
  }
  projectionelement.addEventListener('click', clikcProjection);
  
  let incrementId = {id: 50};
  //これは、テスト用の貼付のidの更新メソッド。ノードのidを増やす。
  //実際はノードのidは、別で更新する。
  function incrementNodeId(node){
    incrementId.id++;
    node.id = incrementId.id;
    node.child.forEach(child => {
      incrementNodeId(child);
    });
  }
  
  console.log(tree);
  document.children[0].tree = tree;