    {{-- コメント　ツリー画面ここから --}}
        <div class="col-3 bg-white border p-2" id="tree">
            <div id="chaintree"></div>
            <div class="twobutton">
                <img src="/image/lg.png" class="btndisplay" id="openTree">
                <div class="btnclose" onclick="display()"><<</div>
            </div>
        </div>
        <script>
            let treeChain = @json($tree_chain);
            let projectionChain = @json($projection_chain);
        </script>
        @if(isset($treeaction_chain))
            <script>let treeaction_chain = @json($treeaction_chain);</script>
        @else
            <script>let treeaction_chain = null;</script>
        @endif
    {{-- コメント　ツリー画面ここまで --}}