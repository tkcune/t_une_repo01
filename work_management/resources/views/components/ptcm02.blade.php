    {{-- コメント　ツリー画面ここから --}}
        <div class="col-3 border p-2" id="tree">
            <div id="chaintree"></div>
            <div class="twobutton">
                <!-- <img src="/image/lo.png" class="btndisplay" id="openTree"> -->
                <img src="data:image/png;base64,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" class="btndisplay" id="openTree">
                <div class="btnclose" onclick="display()"><<</div>
            </div>

            <!-- ツリー生成データ -->
            <script>
            let treeChain = @json($tree_chain);
            let projectionChain = @json($projection_chain);
            let base64ImgList = @json(Config::get('base64'));
            </script>
            <!-- ツリー生成データ -->

            <!-- ツリー機能データ -->
            @if(isset($treeaction_chain))
                <script>let treeactionChain = @json($treeaction_chain);</script>
            @else
                <script>let treeactionChain = null;</script>
            @endif
            <!-- ツリー機能データ -->
            
            <!-- セッション情報 -->
            <input type="hidden" id="hidden_client_id" value="{{ session('client_id') }}">
            <input type="hidden" id="action_node_id" value="{{ session('action_node_id') }}">
            <input type="hidden" id="back_treeaction" value="{{ session('back_treeaction') }}">
            <!-- セッション情報 -->

        </div>

    {{-- コメント　ツリー画面ここまで --}}