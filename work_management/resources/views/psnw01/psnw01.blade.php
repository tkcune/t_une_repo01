@extends('pc0001.pc0001')

@section('content')
{{-- コメント　ネットワーク設定画面ここから --}}
{{-- フォーム画面表示--}}
<div class="col border border-primary" style="padding:10px;">

    @if(isset($top_department))
    <form action="{{route('psnw01.create')}}" method="post">
        @csrf
        
        <div class="details-area border border-dark bg-warning" style="padding:10px;" id="parent">
            <div class="row">
                <div class="col-4">
                    <p id="palent">メールアカウント名<input type="text" name="name" maxlength="32" data-toggle="tooltip" title="メールアカウント名を入力します" value="メールアカウント名"></p>
                </div>
                <div class="col-4">
                    <p id="palent">メールアドレス<input type="text" name="email" maxlength="32" data-toggle="tooltip" title="メールアドレスを入力します" value="mailmail@mail.com"></p>
                </div>
            </div>

            <div class="row" >
                <div class="col-4">
                    <p>パスワード：<input type="password" id="password" name="password" maxlength="10" data-toggle="tooltip" title="パスワードを入力してください。" value="11111111" style="width:100px;"></p>
                </div>
            </div>
            <br>
            <div class="row" >
                <div class="col-5" style="padding:0px">
                    <p>受信サーバ：<input type="text" id="recieving_server" name="recieving_server" maxlength="10" data-toggle="tooltip" title="受信サーバを入力してください。" value="受信サーバ" style="width:100px;"></p>
                </div>
                <div class="col" style="padding:0px">
                <p>受信サーバ通信方式</p>
                </div>
                <div class="col" style="padding:0px">
                <select name = "recieving_server_way">
                    <option value="1">選択肢A</option>
                    <option value="2">選択肢B</option>
                    <option value="3">選択肢C</option>
                </select>
                </div>
                <div class="col" style="padding:0px">
                    <p>受信サーバポート番号：
                        <input type="text" name="recieving_port_number" id="recieving_port_number" style="width:150px;" autocomplete="on" data-toggle="tooltip" title="受信サーバポート番号を入力してください。">
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-3" style="padding:0px">
                    <p>送信サーバ：<input type="text" id="sending_server" name="sending_server" maxlength="10" data-toggle="tooltip" title="送信サーバを入力してください。" value="送信サーバ" style="width:100px;"></p>
                </div>
                <div class="col" style="padding:0px">
                    <p>送信サーバ通信方式：<input type="text" id="sending_server_way" name="sending_server_way" maxlength="10" data-toggle="tooltip" title="送信サーバ通信方式はSMTP固定です。" value="1" style="width:70px;"></p>
                </div>
                <div class="col" style="padding:0px">
                    <p>送信サーバポート番号：
                        <input type="text" name = "sending_port_number" id ="sending_port_number" style="width:100px;" autocomplete="on" data-toggle="tooltip" title="送信サーバポート番号を入力してください。">
                    </p>
                </div>

            </div>
            <div class="row">
                <div class="col">
                    <input type="submit" value="確定" data-toggle="tooltip" title="クリックにより、登録、更新を確定します">
                </div>
            </div>
    </form>

    <form action="/" method="get">
        @csrf
        <input type="submit" value="送信試験" data-toggle="tooltip" title="送信試験を行います">
    </form>
    <div class="row">
        <div>
            受信メッセージ
        </div>
        <div>
            <textarea maxlength="512" style="width:800px; height: 100px;"></textarea>
        </div>
    </div>
    <form action="/" method="get">
        @csrf
        <input type="submit" value="受信試験" data-toggle="tooltip" title="受信試験を行います">
    </form>
    
</div>
@endif
{{-- 部署の詳細表示　ここまで--}}

@endsection