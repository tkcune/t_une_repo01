@extends('pc0001.pc0001')

@section('content')
{{-- コメント　ネットワーク設定画面ここから --}}
{{-- フォーム画面表示--}}
<div class="col border border-primary" style="padding:10px;">

<form action="{{route('psnw01.create')}}" method="post">
        @csrf
        
        <div class="details-area border border-dark bg-warning" style="padding:10px;" id="parent">
            <div class="row">
                <div class="col-4">
                    <p>メールアカウント名<input type="text" name="name" maxlength="256" data-toggle="tooltip" title="メールアカウント名を入力します"></p>
                </div>
                <div class="col-4">
                    <p>メールアドレス<input type="text" name="email" maxlength="256" data-toggle="tooltip" title="メールアドレスを入力します"></p>
                </div>
            </div>

            <div class="row" >
                <div class="col-4">
                    <p>パスワード：<input type="password" id="password" name="password" maxlength="256" data-toggle="tooltip" title="パスワードを入力してください。"></p>
                </div>
            </div>
            <br>
            <div class="row" >
                <div class="col-3" style="padding:0px">
                    <p>受信サーバ：<input type="text" id="recieving_server" name="recieving_server" maxlength="256" data-toggle="tooltip" title="受信サーバを入力してください。"></p>
                </div>
                <div class="col" style="padding:0px">
                <p>受信サーバ通信方式</p>
                </div>
                <div class="col" style="padding:0px" data-toggle="tooltip" title="受信プロトコルを選択してください">
                <select name = "recieving_server_way" id="receive_combobox">
                    <option value="1" selected>POP</option>
                    <option value="2">IMAP</option>
                </select>
                </div>
                <div class="col" style="padding:0px">
                    <p>受信サーバポート番号：<input type="text" id="recieving_port_number" name="recieving_port_number" maxlength="6" data-toggle="tooltip" title="受信サーバのポート番号を入力してください。" value="995"></p>
                </div>
            </div>
            <div class="row">
                <div class="col-3" style="padding:0px">
                    <p>送信サーバ：<input type="text" id="sending_server" name="sending_server" maxlength="256" data-toggle="tooltip" title="送信サーバを入力してください。"></p>
                </div>
                <div class="col" style="padding:0px">
                    <p>送信サーバ通信方式：SMTP</p>
                </div>
                <div class="col" style="padding:0px">
                    <p>送信サーバポート番号：<input type="text" id="sending_port_number" name="sending_port_number" maxlength="6" data-toggle="tooltip" title="送信サーバのポート番号を入力してください。" value="587"></p>
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
    <form action="/" method="get">
        @csrf
        <input type="submit" value="受信試験" data-toggle="tooltip" title="受信試験を行います">
    </form>
    <div class="row">
        <div>
            受信メッセージ
        </div>
        <div>
            <textarea maxlength="512" style="width:800px; height: 100px;"></textarea>
        </div>
    </div>
    
</div>

@endsection