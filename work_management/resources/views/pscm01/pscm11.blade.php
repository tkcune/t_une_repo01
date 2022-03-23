<div class="row margin-reset">
    <div class="row">
        <div class="row">
            <div>メールアドレス</div>
            <div>
                <input type="email" name="email" maxlength="64" value="{{$click_personnel_data->email}}"></p>
            </div>
        </div>
        <div class="row" style="padding:0">
            <div id="login">パスワード</div>
            <div>
            <input id="password" type="password" maxlength="32" name="password">
            @if($click_personnel_data->login_authority == "1") 
                <input type="checkbox" onclick="passwordOn()">
            @endif
            </div>
        </div>
    </div>
    <div class="col">
        <button type="submit" formaction="{{route('psji01.send')}}" formmethod="post">メール送信</button>
    </div>
</div>