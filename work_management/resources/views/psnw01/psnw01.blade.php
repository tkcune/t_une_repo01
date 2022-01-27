@extends('pc0001.pc0001')

@section('content')
{{-- ネットワーク設定画面 --}}
{{-- フォーム画面表示 --}}
<div class="col border border-primary" style="padding:10px;">
    <div class="border border-dark bg-warning" style="padding:10px;" data-toggle="tooltip" title="メールに関するネットワーク設定を行います。">
        概要&nbsp;:&nbsp;メールアドレスの設定とメール送受信のテストを行います
    </div>

    <form method="post">
        @csrf
        
        <div class="border border-dark bg-warning" style="padding:10px;">
            <div class="row">
                <div class="col">
                    メールアカウント名&nbsp;:&nbsp;<input type="text" name="name" maxlength="256" data-toggle="tooltip" title="メールアカウント名を入力します" value="{{$client['name']}}">
                </div>
                <div class="col">
                    メールアドレス&nbsp;:&nbsp;<input type="text" name="email" maxlength="256" data-toggle="tooltip" title="メールアドレスを入力します" value="{{$client['email']}}">
                </div>
            </div>

            <div style="padding: 10px 0;">
                パスワード&nbsp;:&nbsp;<input type="password" id="password" name="password" maxlength="256" data-toggle="tooltip" title="パスワードを入力してください。" value="{{$client['password']}}">
                <input type="checkbox" onclick="passwordOn()" data-toggle="tooltip" title="チェック時にパスワードが見えるようになります">
            </div>
            <div class="row" style="padding: 15px 0;">
                <div class="col">
                    受信サーバ&nbsp;:&nbsp;<input type="text" id="recieving_server" name="recieving_server" maxlength="256" data-toggle="tooltip" title="受信サーバを入力してください。" value = "{{$client['recieving_server']}}">
                </div>
                <div class="col">
                    受信サーバ通信方式&nbsp;:&nbsp;
                    <select name = "recieving_server_way" id="receive_combobox"  data-toggle="tooltip" title="どちらかを選択してください。">
                        <option value="1" <?php if($client['recieving_server_way'] == 1 ){echo 'selected';}?> >POP</option>
                        <option value="2" <?php if($client['recieving_server_way'] == 2 ){echo 'selected';}?>>IMAP</option>
                    </select>
                </div>
            </div>
            <div>
                受信サーバポート番号&nbsp;:&nbsp;<input type="text" id="recieving_port_number" name="recieving_port_number" maxlength="6" data-toggle="tooltip" title="受信サーバのポート番号を入力してください。" value="{{$client['recieving_port_number']}}">
            </div>
            <div class="row"  style="padding: 15px 0;">
                <div class="col">
                    送信サーバ&nbsp;:&nbsp;<input type="text" id="sending_server" name="sending_server" maxlength="256" data-toggle="tooltip" title="送信サーバを入力してください。" value = "{{$client['sending_server']}}">
                </div>
                <div class="col" data-toggle="tooltip" title="送信サーバー通信方式はSMTPです。">
                    送信サーバ通信方式&nbsp;:&nbsp;SMTP
                </div>
            </div>
            <div>
                送信サーバポート番号&nbsp;:&nbsp;<input type="text" id="sending_port_number" name="sending_port_number" maxlength="6" data-toggle="tooltip" title="受信サーバポート番号は587です。" value="{{$client['sending_port_number']}}">
            </div>

            <div class="row" style="padding: 10px 0;">
                <div class="col-2">
                    <input type="submit" value="確定" data-toggle="tooltip" title="ボタンを押下すると上記の内容が確定されます。" formaction="{{ route('psnw01.create') }}">
                </div>
            </div>

            <div class="row" style="padding: 10px 0;">
                <div class="col-2">
                    <input type="submit" value="送信試験" data-toggle="tooltip" title="送信試験を行います" formaction="{{ route('psnw01.send') }}">
                </div>
                <div class="col">
                    送信試験用メールアドレス&nbsp;:&nbsp;<input type="text" name="test_email" maxlength="256" data-toggle="tooltip" title="試験用メールアドレスを入力してください。" value="{{$client['test_email']}}">
                </div>
            </div>

            <div class="row" style="padding: 10px 0;">
                <div class="col-2">
                    <input type="submit" value="受信試験" data-toggle="tooltip" title="受信試験を行います" formaction="{{ route('psnw01.receive') }}">
                </div>
                <div class="col-2" data-toggle="tooltip" title="受信メッセージです。">
                    受信メッセージ&nbsp;&darr;
            </div>
        </div>
    </form>
    @if(isset($client['mail']) && $client['mail'] != "")
        <div class="border border-dark bg-white" style="padding: 10px 0;">
            <div style="padding: 10px;">
                @foreach($client['mail'] as $line)
                    {!! $line !!}
                @endforeach    
            </div>
        </div>
    @endif
</div>

@endsection