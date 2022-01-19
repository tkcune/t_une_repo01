    {{-- コメント　ツリー画面ここから --}}
        <div class="col-3 bg-white border p-2" id="tree">
            <div id="chaintree"></div>
            <div class="twobutton">
                <img src="/image/lo.png" class="btndisplay" id="openTree">
                <div class="btnclose" onclick="display()"><<</div>
            </div>

            <!-- ツリー生成データ -->
            <script>
            let treeChain = @json($tree_chain);
            let projectionChain = @json($projection_chain);
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