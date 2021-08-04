@extends('pc0001.pc0001')


@section('header')
    <header>
    <div class="row border border-dark">
        <div class="col-2">
            <p>総合システム</p>
        </div>

        <div class="col-5">
            <p>メッセージを表示</p>
        </div>

        <div class="col-2">
            <p>ABCさん作業中</p>
        </div>

        <div class="col-3">
            <p>ログイン日時 2021/07/01 11:45:00</p>
        </div>
        
    </div>
    </header>
@endsection


@section('content')
    <div class="row">
        <div class="col-3 border border-primary" id="tree" style="padding:10px;">
            <div class="row">
                <div class="col-9">  
                <ul>
                    <li><a href="#">ログイン画面</a></li>
                        <ul>
                            <li><a href="#">ログアウト</a></li>
                            <li><a href="#">作業管理</a></li>
                        </ul>
                    <li><a href="#">組織要員定義</a>
                    <li><a href="#">作業場所定義</a>
                </ul>
                </div>
                
                <div class="col">
                    <button type="button" class="btn-close" aria-label="Close" onclick="display()"></button>
                </div>
            </div>
        </div>

        <div class="col border border-primary" style="padding:10px;">
            <div class="details-area border border-dark bg-warning" style="padding:10px;">
                <div class="row">
                    <div class="col-4">
                        <p>部署名<input type="text"></p>
                    </div>
                    <div class="col">
                        <p>番号:210000002</p>
                    </div>
                    <div class="col">
                        <p>上位:<a href="#">部署8</a></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <p>状態:
                        <select name="state">
                        <option>稼働中</option>
                        </select>
                        責任者:
                        <select name="state">
                        <option>社員01</option>
                        </select>
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                    <p>
                    <button type="button" >確定</button>
                    <button type="button" >削除</button>
                    【<a href="#">コピー</a>】 
                    【<a href="#">部署コピー取り消し</a>】
                    【<a href="#">人員コピー取り消し</a>】
                    登録日:140809 登録者:<a href="#">社員08</a></p>
                    </div>
                </div>
            </div>

            <div class="department-area">
                <div class="row">
                    <div class="col">
                    <p>配下部署 <button>新規</button>
                    【<a href="#">移動</a>】
                    【<a href="#">貼付</a>】
                    <button type="button" >&lt;&lt;</button>
                    <button type="button" >&lt;</button>
                    1/1(4)
                    <button type="button" >&gt;</button>
                    <button type="button" >&gt;&gt;</button>
                    <button type="button" >検索</button>
                    部署<input type="text">
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                    <table class="table table-bordered border-dark">
                        <thead>
                            <tr>
                            <th>部署番号</th>
                            <th>部署名</th>
                            <th>上位部署</th>
                            <th>状態</th>
                            <th>責任者</th>
                            <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <td>21000000</td>
                            <td><a href="#">部署01</a></td>
                            <td><a href="#">部署00</a></td>
                            <td>稼働中</td>
                            <td><a href="#">社員01</a></td>
                            <td>【<a href="#">コピー</a>】</td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
            <div class="">
                <div class="row">
                    <div class="col">
                    <p>所属人員 <button>新規</button>
                    【<a href="#">移動</a>】
                    【<a href="#">貼付</a>】
                    <button type="button" >&lt;&lt;</button>
                    <button type="button" >&lt;</button>
                    1/1(14)
                    <button type="button" >&gt;</button>
                    <button type="button" >&gt;&gt;</button>
                    <button type="button" >検索</button>
                    氏名<input type="text">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col">
                    <table class="table table-bordered border-dark">
                        <thead>
                            <tr>
                            <th>人員番号</th>
                            <th>氏名</th>
                            <th>所属部署</th>
                            <th>状態</th>
                            <th>ログインID</th>
                            <th>PW更新</th>
                            <th>権限</th>
                            <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <td>100000000</td>
                            <td><a href="#">社員01</a></td>
                            <td><a href="#">部署09</a></td>
                            <td>在職</td>
                            <td>aaa02</td>
                            <td>140801</td>
                            <td>---------</td>
                            <td>【<a href="#">コピー</a>】</td>
                            </tr>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
            

        </div>
    </div>
@endsection
