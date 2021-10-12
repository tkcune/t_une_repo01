    {{-- コメント　ツリー画面ここから --}}
        <div class="col-3 bg-white border p-2" id="tree">
            <div class="row">
                <div id="chaintree" class="col-10"></div>
                <div class="col">
                    <button type="button" class="btn-close" aria-label="Close" onclick="display()"></button>
                    <button id='openTree'>露出</button>
                </div>
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