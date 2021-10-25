    {{-- コメント　ツリー画面ここから --}}
        <div class="col-3 bg-white border p-2" id="tree">
            <div id="chaintree"></div>
            <div class="twobutton">
                <img src="/image/lg.png" class="btndisplay" id="openTree">
                <div class="btnclose" onclick="display()"><<</div>
            </div>
            <script>
            let treeChain = @json($tree_chain);
            let projectionChain = @json($projection_chain);
            </script>
            @if(isset($treeaction_chain))
                <script>let treeactionChain = @json($treeaction_chain);</script>
            @else
                <script>let treeactionChain = null;</script>
            @endif
            <input type="hidden" id="hidden_client_id" value="{{ session('client_id') }}">
        </div>
    {{-- コメント　ツリー画面ここまで --}}