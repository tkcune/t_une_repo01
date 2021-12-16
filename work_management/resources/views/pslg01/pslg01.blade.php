@extends('pc0001.pc0001')

@section('content')
<div class="col border border-primary" style="padding:10px;">
    <div class="container-fluid">
        <div class="row">
            <div class="details-area border border-dark bg-warning" style="padding:10px;" id="parent">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-3"></div>
                        <div class="col-6"></div>
                        <div class="col-3" style="text-align: right">―　ログページ　―</div>
                    </div>

                    <div class="row">
                        <p style="text-align: center">検索したい条件などを入力して「表示する」ボタンを押してください<br>
                            【* 部署人員番号が空白の場合、全ての人員名が表示されます。】</p>
                        <div class="col-4">
                            <form action="{{route('pslg01.create')}}" method="post" id="create">
                                @csrf
                                <!-- form id = "create"  下記のname属性を入れ子で対応　-->
                                <!--   name = [ management_number, management_name, 
                                                 startdate, finishdate, 
                                                 check[], search, "表示する"  ]  -->
                            </form>
                            <p>顧客番号&nbsp;&nbsp;: <input type="text" name="name" style="width:100px;" placeholder="半角英数字で入力"></p>
                            <p>顧客名&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <a href="#" style="color:black;" title="クリックにより顧客詳細に遷移します">(例)前川一号生</a></p>

                            <p class="box" title="入力に該当した顧客の候補を一覧に表示します。表示された人員を選択した場合、その番号が顧客番号に表示されます">顧客検索&nbsp;&nbsp;:
                                <select class="box" style="width:100px; margin-left:5px;">
                                    <option selected name="client_id" value=""></option>
                                    @foreach($personnel_data as $parson)
                                    <option name="client_id" value="{{$parson->client_id}}"> {{$parson->client_id}}</option>
                                    @endforeach
                                </select>
                            </p>
                        </div>

                        <div class="col-4">
                            <p>部署人員番号&nbsp;&nbsp;:&nbsp;&nbsp;<input type="text" id="management_number" name="management_number" form="create" maxlength="10" data-toggle="tooltip" title="部署情報を修正、抹消できる管理者を変更する場合、ここを修正します 管理者自身とシステム管理者だけが修正できます" value="" style="width:100px;"></p>
                            <p>部署人員名&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:<span id="management_name" name="management_name" style="text-decoration:underline; margin-left: 10px;" data-toggle="tooltip" title="クリックにより顧客詳細に遷移します"></span></p>

                            <p>部署人員検索&nbsp;&nbsp;:
                                <input type="text" id="search-list" list="keywords" style="width:150px;" autocomplete="on" name="management_name" form="create" data-toggle="tooltip" title="入力に該当した人員の候補を一覧に表示します。表示された人員を選択した場合、その番号が管理者人員番号に表示されます。">
                                <datalist id="keywords">
                                    @for($j = 0; $j < count($personnel_data);$j++) <option value="{{$personnel_data[$j]->name}}" label="{{$personnel_data[$j]->personnel_id}}" managment="{{$personnel_data[$j]->system_management}}">
                                        <input type="hidden" name="system_management" value="{{$personnel_data[$j]->system_management}}" form="create">
                                        </option>
                                    @endfor
                                </datalist>
                            </p>
                        </div>

                        <div class="col-4">
                            <p>開始日時: <input type="datetime-local" name="startdate" value="{{date('Y-m-d', strtotime('today'))}}T00:00" form="create"></p>
                            <p>終了日時: <input type="datetime-local" name="finishdate" value="{{date('Y-m-d\TH:i',strtotime('now'))}}" form="create"></p>
                        </div>
                    </div>
                    <div class="container">
                        <div class="row justify-content-start">
                            <div class="col-3">
                                <input type="checkbox" name="check[]" value="nm" title="通常メッセージログを表示するかどうかを指定します" form="create"> 通常メッセージ
                            </div>
                            <div class="col-3">
                                <input type="checkbox" name="check[]" value="wn" title="警告メッセージログを表示するかどうかを指定します" form="create" checked> 警告メッセージ
                            </div>
                            <div class="col-3">
                                <input type="checkbox" name="check[]" value="er" title="異常メッセージログを表示するかどうかを指定します" form="create" checked> 異常メッセージ
                            </div>
                            <div class="col-3">
                                <input type="checkbox" name="check[]" value="ok" title="正常メッセージログを表示するかどうかを指定します" checked form="create"> 正常メッセージ
                            </div>
                        </div>

                        <div class="row justify-content-start">
                            <div class="col-6" id="okSystem">

                            </div>
                            <div class="col-6" id="errSystem">

                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-10">文字検索&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="search" style="width:32rem;" placeholder="入力可能な文字数は３２  全角、半角英数字、一覧操作領域" form="create">
                        </div>
                        <div class="col-2">
                            <input type="submit" value="表示する" form="create">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-10"></div>
                        <div class="col-2">
                            @isset($count)
                            件数:&nbsp;&nbsp;{{$count}} 件
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        @isset($items)


                        <div class="wrapper" style="overflow-y: scroll; background-color:white; height:20rem">
                            <table class="table" style="font-size:12px; word-break : break-all;">
                                <thead>
                                    <tr>
                                        <th scope="col">開始日 </th>
                                        <th scope="col">終了日</th>
                                        <th scope="col">類別</th>
                                        <th scope="col">アクセスユーザー</th>
                                        <th scope="col">機能</th>
                                        <th scope="col">プログラムパス</th>
                                        <th scope="col">ログ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                    <tr>
                                        <td> {{$item->created_at}}</td>
                                        <td> {{$item->updated_at}}</td>
                                        <td> {{$item->type}}</td>
                                        <td> {{$item->name}}</td>
                                        <td> {{$item->function}}</td>
                                        @if($item->system_management)
                                        <td>{{$item->program_pass}}</td>
                                        @else
                                        <td> </td>
                                        @endif
                                        <td> {{$item->log}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <form action="{{route('pslg01.download')}}" method=post>
                            @csrf
                            <input type="submit" value="ダウンロードする" style="margin-top:30px">
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection