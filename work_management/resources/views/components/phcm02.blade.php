{{-- コメント　ヘッダ画面ここから --}}
    <header>
    <div class="border border-dark">
        <div>
            {{Config::get('config.system_name')}}
        </div>

        @if(session('message'))
        <div>
            <p @if(!empty(session('handle_message'))) data-toggle="tooltip" title="{{session('handle_message')}}" @endif>{{ session('message') }}</p>
            @php
                session()->forget('message');
                session()->forget('handle_message');
            @endphp
        </div>
        @else
        <div>
            <p>メッセージを表示</p>
        </div>
        @endif
        <div>
            ABCさん作業中
        </div>

        <div>
            <div>ログイン日時</div>
            <div>2021/07/01 11:45:00</div>
        </div>
    </div>
    </header>
    {{-- コメント　ヘッダ画面ここまで --}}