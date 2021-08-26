@extends('pc0001.pc0001')

    
@section('content')
<div class="col border border-primary" style="padding:10px;">
    <div class="row">
            <form action="{{ route('psji01.store') }}" method="post">
            @csrf
            @method('post')
            {{-- hiddenのvalueはダミーデータ　--}}
            <input type="hidden" name="client_id" value="aa00000001">
            <input type="hidden" name="high" value="{{ $_GET["high"] }}">
            <div class="col-6">
            名前<input type="text" name="name">
            </div>
            <div class="col-6">
            メールアドレス<input type="email" name="email">
            </div>
            <div class="col-6">
            パスワード<input type="password" name="password">
            </div>
            <div class="col-2">
            状態
            <select name="status">
                <option value="10">応募</option>
                <option value="11">審査中</option>
                <option value="12">入社待</option>
                <option value="13">在職</option>
                <option value="14">休職</option>
                <option value="18">退職</option>
            </select>
                <div>
                ログイン権限<br>
                <input type="radio" name="login_authority" value="0">無
                <input type="radio" name="login_authority" value="1">有
                </div>
                <div>
                システム権限<br>
                <input type="radio" name="system_management" value="0">無
                <input type="radio" name="system_management" value="1">有
                </div>
            </div>
            <div class="col">
                <input type="submit" value="登録">
            </div>
            </form>
    </div>     
</div>

@endsection