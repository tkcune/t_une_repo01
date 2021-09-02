//上下関係だけのデータからツリーの階層を構築するクラス
  //Nodeクラスを階層化する。css名を決定する。
export class ChainParser {

    //データから第一階層のノード情報を除く
    //@param array sepalete 親子関係を連想配列にしたchainの配列
    //@return array list 第一階層のchainを除いたchainの配列
    static exceptSepalete(sepalete) {

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
    static decisionTreeClass(topNode) {
      
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
    static isEqual(node, compairNode) {
      
      if(node.dir !== compairNode.dir){
        return false;
      }

      return true;
    }

    //子要素のクラス名を決定していく。
    //@param Nodeクラス node 階層のノード
    //@param Nodeクラス palent nodeの親ノード
    static decisionChildClass(node, palent) {
      
      //linetreeの場合
      if(node.className === 'linetree') {

        //メソッドの入力のノードと親ノードの最後のノードが等しければ
        if(ChainParser.isEqual(node, palent.child[palent.child.length - 1]) === true){
          ChainParser.decisionLineTreeClass(node, true);
        }else{
          ChainParser.decisionLineTreeClass(node, false);
        }

        //expandtree系の場合
      }else if(node.className === 'expandtree' || node.className === 'lastexpandtree'){

        //親ノードがツリーの一番上chaintreeの場合
        if(palent.dir === ''){

          //メソッドの入力のノードと親ノードの最後のノードが等しければ、最後のノード。
          if(ChainParser.isEqual(node, palent.child[palent.child.length - 1]) === true){
            node.className = 'lastexpandtree';
          }else{
            node.className = 'expandtree';
          }
          //nodeのcssを決定していく。
          ChainParser.decisionExpandTreeClass(node);
        }else{
          
          //palentからcssを作り直し。
          ChainParser.decisionExpandTreeClass(palent)
        }
      }
    }

    //lientreeの子要素のクラスの名前を決める
    static decisionLineTreeClass(node, isLastFlag){
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
    static decisionExpandTreeClass(node) {
      
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
          ChainParser.decisionExpandTreeClass(node.child[i]);
        }
      }
    }

    //@param string nodeDir ノードクラスのディレクトリ
    //@param Nodeクラス node chaintreeのノードクラス
    //@return Nodeクラス search 検索したノードクラス
    //ノードのディレクトリからノードクラスを探索
    static searchNodeDir(nodeDir, node){
      
      //ツリーの一番上chaintreeの場合
      //本来のchaintree.dirは '' 空文字
      //探索する時に.join('/')しているので、'/'を追加
      if(nodeDir === '/' || nodeDir === ''){
        return node;
      }
      
      //ノードクラスをnodoと子要素ごとに一列配列に展開する
      //@var array nodeArray 展開したノードクラスを格納する配列
      let nodeArray = ChainParser.concatNode(node);

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
    static searchNodeId(nodeId, node){
      
      //ノードクラスをnodoと子要素ごとに一列配列に展開する
      //@var array nodeArray 展開したノードクラスを格納する配列
      let nodeArray = ChainParser.concatNode(node);

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
    static searchNodeDirId(nodeDir, nodeId, node){
      
      //@var array nodeArray 展開したノードクラスを格納した配列
      let nodeArray = ChainParser.concatNode(node);
      
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
    static concatNode(node){

      //var array array 返値 初期値はnode
      let array = [node];

      //子要素を取得する。
      //linetreeは除く。
      //子要素がないなら、returnする
      if(node.child.length > 0){
        node.child.forEach(child => {
          
          //concatNodeの返り値はarrayなので、変数のarrayと返り値concatする事で、一列の配列になる
          array = array.concat(ChainParser.concatNode(child));
        });
      }
      return array;      
    }

    //@param nodeクラス node ノードクラス
    //@return string nodeの親のタイトル
    //ノードクラスの親のタイトルを返す。
    static searchPalentTitle(node){

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
    static syncLinkNode(fromNodeDir, fromNodeId, toNode, tree){

      //@var Nodeクラス fromNode 投影元のノードクラス
      let fromNode = ChainParser.searchNodeDirId(fromNodeDir, fromNodeId, tree);

      //リンク元がない。投影先ではない
      if(fromNode.fromLink.length === 0){
        //@var array concatFromNode リンク元を一列に展開
        //@var array concatToNode リンク先を一列に展開
        let concatFromNode = ChainParser.concatNode(fromNode);
        let concatToNode = ChainParser.concatNode(toNode);
      
        for(let i = 0; i < concatFromNode.length; i++){

          //リンク元にリンク先のディレクトリを入れる
          //リンク先にリンク元のディレクトリを入れる
          concatFromNode[i].toLink.push([concatToNode[i].id, concatToNode[i].dir]);
          concatToNode[i].fromLink.push([concatFromNode[i].id, concatFromNode[i].dir]);
        }
      }else{
        //投影元を持っている。fromNodeは、投影先。
        fromNode.fromLink.forEach(fromLinkIdDir => {
          
          //@var Nodeクラス fromLinkNode 投影元のノードクラス
          let fromLinkNode = ChainParser.searchNodeDirId(fromLinkIdDir[1], fromLinkIdDir[0], tree);
          
          //@var array concatFromNode リンク元を一列に展開
          //@var array concatToNode リンク先を一列に展開
          let concatFromNode = ChainParser.concatNode(fromLinkNode);
          let concatToNode = ChainParser.concatNode(toNode);
          
          for(let i = 0; i < concatFromNode.length; i++){

            //リンク元にリンク先のディレクトリを入れる
            //リンク先にリンク元のディレクトリを入れる
            concatFromNode[i].toLink.push([concatToNode[i].id, concatToNode[i].dir]);
            concatToNode[i].fromLink.push([concatFromNode[i].id, concatFromNode[i].dir]);
          }
        });
      }
    }

    //ノードクラスのディレクトリを更新する
    //@param Nodeクラス node 更新するノード
    //@param string nodeDir 更新するノードの親ノードディレクトリ
    static recreateDir(node, nodeDir){

      //親ディレクトリとノードタイトルを結合してディレクトリを更新する
      node.dir = nodeDir + '/' + node.title;
      node.child.forEach(child => {

        //子要素について更新したディレクトリを引数にして再帰的に呼び出す。
        ChainParser.recreateDir(child, node.dir);
      });
    }

    //ツリーのidを更新する。
    //貼付や投影などで、idを更新した時
    //@param Nodeクラス node 更新するノード
    //@param array nodeChain 上下関係のデータのオブジェクト
    static recreateId(node, nodeChain){

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
        ChainParser.recreateId(child, nodeChain);
      })
    }

    //親要素、親要素の親要素を返す
    //@param Nodeクラス node ノードクラス
    //@param Nodeクラス tree グローバル変数のtree
    //@return array 親要素、親要素の親要素の配列
    static getTwoPalent(node, tree){

      //@var array splitDir ディレクトリを分けた配列
      let splitDir = node.dir.split('/');

      //最後の文字を削除することによって、親要素のディレクトリになる
      splitDir.pop();

      //@vaar Nodeクラス palent 親要素のディレクトリ
      let palent = ChainParser.searchNodeDir(splitDir.join('/'), tree);

      //親要素のディレクトリから最後の文字を削除することによって、親要素の親要素ディレクトリとなる
      splitDir.pop();

      //@var Nodeクラス palentPalent 親要素の親要素のディレクトリ
      let palentPalent = ChainParser.searchNodeDir(splitDir.join('/'), tree);
      return [palent, palentPalent];
    }

  }