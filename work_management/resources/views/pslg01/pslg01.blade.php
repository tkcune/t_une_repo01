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
                        <p style="text-align: center">検索したい条件などを入力して「表示する」ボタンを押してください</p>
                        <div class="col-4">

                            <form action="{{route('pslg01.create')}}" method="post" id="create">
                                @csrf
                            </form>
                            <p>顧客番号： <input type="text" name="name" style="width:100px;" placeholder="半角英数字で入力"></p>
                            <p>顧客名　： <a href="#" style="color:black;" title="クリックにより顧客詳細に遷移します">　(例)前川一号生</a></p>

                            <p class="box" title="入力に該当した顧客の候補を一覧に表示します。表示された人員を選択した場合、その番号が顧客番号に表示されます">顧客検索：　
                                <select class="box" style="width:100px;">

                                    <option selected name="kokyaku_id" value=""></option>
                                    @foreach($session_names as $parson)

                                    <option name="client_id" value="{{$parson->client_id}}"> {{$parson->client_id}}</option>
                                    @endforeach
                                </select>
                            </p>

                        </div>
                        <div class="col-4">
                            <p title="入力に該当した顧客の候補を一覧に表示します。表示された人員を選択した場合、その番号が顧客番号に表示されます。">部署人員番号：
                                <?php if (isset($select_id)) : ?>
                                    <input type="text" name="personnel_id" value="{{$select_id}}" style="width: 100px;" form="create">

                                <?php else : ?>
                                    <input type="text"  name="personnel_id" value="{{old('personnel_id')}}" style="width: 100px;" form="create">
                                <?php endif ?>

                                <?php if (isset($select_id)) : ?>
                            <p>部署人員名　：　<a href= "show/aa00000001/{{$select_id}}"; style="color:black;" title="クリックにより顧客詳細に遷移します">{{$select_name}}</a></p>
                            <?php else :?>
                            <p>部署人員名　：　</p>
                            <?php endif ?>


                            <form method="POST" action="{{route('pslg01.select')}}" id="one_answer_form" style="display:inline-flex">
                                @csrf
                                <p class="box" title="入力に該当した人員の候補を一覧に表示します。表示された人員を選択した場合、その番号が部署番号に表示されます。"> 部署人員検索：</p>
                                　<select onchange="submit(this.form)" name="personnel_id" id="one_answer" class="box" style="width:100px; height:25px;" form="one_answer_form"　>
                                　   <option name="personnel_id" value="0" selected> </option>
                                    @foreach($session_names as $session_name)
                                    <option name="personnel_id" value="{{$session_name->personnel_id}}"> {{$session_name->name}}</option>
                                    　　@endforeach
                                    @isset($select_name)
                                    <option name="personnel_id" value="{{$session_name->personnel_id}}" selected>{{$select_name}}</option>
                                    @endif
                                </select>

                            </form>
                            @isset($select_name)
                            　<input type="hidden" name="select_name" value="{{$select_name}}"  form="create" >
                            @endif
                        </div>
                        <div class="col-4">
                            <p>開始日時： {{ \Carbon\Carbon::today() }}</p>
                            <input type="hidden" name="startdate" value="{{ date('Y-m-d', strtotime('today')) }}" form="create">
                            <p>終了日時： {{ \Carbon\Carbon::now() }}</p>
                                <input type="hidden" name="finishdate" style="width:120px;" form="create"></p>
                        </div>
                    </div>

                    <div class="row justify-content-start">
                        <div class="col-4">
                            　<input type="checkbox" name="check[]" value="nm" title="通常メッセージログを表示するかどうかを指定します" form="create"> 通常メッセージ
                        </div>
                        <div class="col-4">
                            　<input type="checkbox" name="check[]" value="wn" title="警告メッセージログを表示するかどうかを指定します" form="create" checked> 警告メッセージ
                        </div>
                        <div class="col-4">
                            　<input type="checkbox" name="check[]" value="er" title="異常メッセージログを表示するかどうかを指定します" form="create" checked> 異常メッセージ
                        </div>
                    </div>

                    <div class="row justify-content-start">
                        <div class="col-4">
                            <input type="checkbox" name="check[]" value="ok" title="正常メッセージログを表示するかどうかを指定します" checked form="create"> 正常メッセージ
                        </div>
                        <div class="col-4">
                            <input type="checkbox" name="check[]" value="si" title="システム情報メッセージログを表示するかどうかを指定します" form="create"> システム情報メッセージ
                        </div>
                        <div class="col-4">
                            <input type="checkbox" name="check[]" value="sy" title="システム異常メッセージログを表示するかどうかを指定します" form="create"> システム異常メッセージ
                        </div>
                    </div>

                    <div class="row mt-3">

                        <div class="col-10">　文字検索：<input type="text" name="kensaku" style="width:32rem;" placeholder="入力可能な文字数は３２  全角、半角英数字、一覧操作領域" form="create">

                        </div>
                        <div class="col-2">
                            <input type="submit" value="表示する" form="create">
                        </div>

                    </div>


                    <div class="row mt-3">
                        <div class="col-10">
                        </div>
                        <div class="col-2">
                            @isset($count)
                            件数　：{{$count}} 件
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
                                    @php
                                    $start_date = $item->created_at;
                                    $finish_date = $item->updated_at;
                                    @endphp

                                    <tr >
                                        <td> {{date('Y年m月d日', strtotime($start_date))}}0時0分</td>
                                        <td> {{date('Y年m月d日', strtotime($finish_date))}}</td>
                                        <td> {{$item->type}}</td>
                                        <td> {{$item->name}}</td>
                                        <td> {{$item->function}}</td>
                                        <td> {{$item->program_pass}}</td>
                                        <td> {{$item->log}}</td>
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>

                        </div>
                       
                        <form  action="{{route('pslg01.download')}}" method=post>
                        @csrf
                        <input type="submit" value="ダウンロードする">

                        </form>
                        @endif

                     
 

                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection