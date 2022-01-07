@extends('pc0001.pc0001')

@section('content')
{{-- コメント　ネットワーク設定画面ここから --}}
{{-- フォーム画面表示--}}
<div class="col border border-primary" style="padding:10px;">

    <form method="post">
        @csrf
        
        <div class="border border-dark bg-warning" style="padding:10px;">
            <div class="row">
                <div class="col">
                    メールアカウント名&nbsp;:&nbsp;<input type="text" name="name" maxlength="256" data-toggle="tooltip" title="メールアカウント名を入力します" value="sagyotest@b-forme.net">                </div>
                <div class="col">
                    メールアドレス&nbsp;:&nbsp;<input type="text" name="email" maxlength="256" data-toggle="tooltip" title="メールアドレスを入力します" value="sagyotest@b-forme.net">
                </div>
            </div>

            <div style="padding: 10px 0;">
                パスワード&nbsp;:&nbsp;<input type="password" id="password" name="password" maxlength="256" data-toggle="tooltip" title="パスワードを入力してください。" value="sagyopass">
            </div>
            <div class="row" style="padding: 15px 0;">
                <div class="col">
                    受信サーバ&nbsp;:&nbsp;<input type="text" id="recieving_server" name="recieving_server" maxlength="256" value = "pop3.muumuu-mail.com" data-toggle="tooltip" title="受信サーバを入力してください。">
                </div>
                <div class="col">
                    受信サーバ通信方式&nbsp;:&nbsp;
                    <select name = "recieving_server_way" id="receive_combobox"  data-toggle="tooltip" title="受信プロトコルを選択してください">
                        <option value="1" selected>POP</option>
                        <option value="2">IMAP</option>
                    </select>
                </div>
            </div>
            <div>
                受信サーバポート番号&nbsp;:&nbsp;<input type="text" id="recieving_port_number" name="recieving_port_number" maxlength="6" data-toggle="tooltip" title="受信サーバのポート番号を入力してください。" value="995">
            </div>
            <div class="row"  style="padding: 15px 0;">
                <div class="col">
                    送信サーバ&nbsp;:&nbsp;<input type="text" id="sending_server" name="sending_server" maxlength="256" data-toggle="tooltip" title="送信サーバを入力してください。" value = "smtp.muumuu-mail.com">
                </div>
                <div class="col">
                    送信サーバ通信方式&nbsp;:&nbsp;SMTP
                </div>
            </div>
            <div>
                送信サーバポート番号&nbsp;:&nbsp;<input type="text" id="sending_port_number" name="sending_port_number" maxlength="6" data-toggle="tooltip" title="送信サーバのポート番号を入力してください。" value="587">
            </div>

            <div class="row" style="padding: 10px 0;">
                <div class="col-2">
                    <input type="submit" value="確定" data-toggle="tooltip" title="クリックにより、登録、更新を確定します" formaction="{{ route('psnw01.create') }}">
                </div>
                <div class="col-2">
                    <input type="submit" value="送信試験" data-toggle="tooltip" title="送信試験を行います" formaction="{{ route('psnw01.send') }}">
                </div>
            </div>
    </form>
    <div class="row" style="padding: 10px 0;">
        <div class="col-2">
        <input type="submit" value="受信試験" data-toggle="tooltip" title="受信試験を行います" formaction="{{ route('psnw01.receive') }}">
        </div>
        <div class="col-2">
            受信メッセージ
        </div>
    </div>
    <div class="border border-dark" style="padding: 10px 0;">
        <div style="padding: 10px;">
            @if(isset($mail))
                @foreach($mail as $line)
                    {!! $line !!}<br />
                @endforeach
            @endif
        </div>
    </div>
</div>

@endsection