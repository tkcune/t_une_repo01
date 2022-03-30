<div class="row margin-reset">
    <div class="col-5">
        <p>メールアドレス<input type="email" name="email" maxlength="64" value="{{$click_personnel_data->email}}"></p>
    </div>
    <div class="col-4" style="padding:0px;margin-left:-68px">
        <p id="login">パスワード<input id="password" type="password" maxlength="32" name="password">
        @if($click_personnel_data->login_authority == "1") 
            <input type="checkbox" onclick="passwordOn()">
        @endif
        </p>
    </div>
    <div class="col">
        <button type="submit" formaction="{{route('psji01.send')}}" formmethod="post">メール送信</button>
    </div>
</div>