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
            <input type="hidden" name="management_number" value="dummy">

            <div class="details-area border border-dark bg-warning" style="padding:10px;" id="parent">
                    <div class="row">
                        <div class="col-4">
                            <p id="palent">名前<input type="text" name="name" value="" data-toggle="tooltip" title="人員の名称を入力します"></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-5">
                            <p>メールアドレス<input type="email" name="email" value=""></p>
                        </div>
                        <div class="col-4" style="padding:0px">
                            <p id="login">パスワード<input id="password" type="password" name="password">
                            <input type="checkbox" data-toggle="tooltip" title="チェック時にパスワードを見えるようにする" onclick="passwordOn()"></p>
                        </div>
                        <div class="col">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <p>状態:
                            <select name="status" data-toggle="tooltip" title="人員の状態を選択します">
                            <option value="10">応募</option>
                            <option value="11">審査</option>
                            <option value="12">入社待</option>
                            <option value="13">在職</option>
                            <option value="14">休職</option>
                            <option value="18">退職</option>
                            </select>
                            システム管理者:
                            <input type="hidden" name="system_management" value="0">
                            <input type="checkbox" name="system_management" value="1" data-toggle="tooltip" title="人員がシステム管理者かどうかを設定します">
                            ログイン：
                            <input type="hidden" name="login_authority" value="0">
                            <input type="checkbox" name="login_authority" value="1">
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div style="display:inline-flex">
                            <input type="submit" value="確定"
                            data-toggle="tooltip" title="クリックにより、登録、更新を確定します">
                            </div>
                        </div>
                    </div>
            </div>
            </form>
    </div>     
</div>

@endsection