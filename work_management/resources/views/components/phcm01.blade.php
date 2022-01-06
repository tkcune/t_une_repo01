{{-- コメント　ヘッダ画面ここから --}}
    <header>
    <div class="row border border-dark">
        <div class="col-2">
            <p>{{Config::get('config.system_name')}}</p>
        </div>

        @if(session('message'))
        <div class="col-5">
            <p @if(!empty(session('handle_message'))) data-toggle="tooltip" title="{{session('handle_message')}}" @endif>{{ session('message') }}</p>
            @php
                session()->forget('message');
                session()->forget('handle_message');
            @endphp
        </div>
        @else
        <div class="col-5">
            <p>メッセージを表示</p>
        </div>
        @endif

        <div class="col-2">
            <p>ABCさん作業中</p>
        </div>

        <div class="col-3">
            <p>ログイン日時 2021/07/01 11:45:00</p>
        </div>
        
    </div>
    </header>
    {{-- コメント　ヘッダ画面ここまで --}}
    </div>