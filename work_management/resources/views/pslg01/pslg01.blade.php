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
                        <p style="text-align: center">検索したい条件などを入力して「検索」ボタンを押してください</p>
                        <div class="col-4">


                            <p>顧客番号： <input type="text" name="name" style="width:100px;" placeholder="半角英数字で入力"></p>
                            <p>顧客名　： <a href="#" style="color:black;" title="クリックにより顧客詳細に遷移します">　(例)前川一号生</a></p>
                            <p class="box" title="入力に該当した顧客の候補を一覧に表示します。表示された人員を選択した場合、その番号が顧客番号に表示されます">顧客検索：　
                                <select class="box" style="width:100px;">

                                    <option selected name="kokyaku_id" value=""></option>

                                    @foreach($name_data as $parson)
                                    <option name="kokyaku_id" value="{{old($parson->client_id)}}"> {{$parson->client_id}}</option>
                                    @endforeach
                                </select>
                            </p>

                        </div>
                        <div class="col-4">
                            <p title="入力に該当した顧客の候補を一覧に表示します。表示された人員を選択した場合、その番号が顧客番号に表示されます。">部署人員番号：
                           
                            @isset($select_id)
                                   <input type="text" name="zinin" value="{{old($select_id->personnel_id)}}" style="width: 100px;">
                           @else
                           <input type="text" name="zinin" value="{{old('zinin')}}" style="width: 100px;">
                           @endif
                                </p>

                            <p>部署人員名　：　<a href="#" style="color:black;" title="クリックにより顧客詳細に遷移します">前川一号生</a></p>
                            <!-- ※　クリックにより、顧客詳細に遷移する。 顧客番号に該当する部署の名称 -->


                            <form method="POST" action="{{route('pslg01.select')}}" id="one_answer_form">
                                @csrf
                                <span title="入力に該当した人員の候補を一覧に表示します。表示された人員を選択した場合、その番号が部署番号に表示されます。"> 部署人員検索：</span>
                                　<select onchange="submit(this.form)" name="one_answer" id="one_answer" style="width: 100px;">
                                    <option selected></option> 　
                                    @foreach($name_data as $parson)
                                    <option name="personnel_id" value="{{old($parson->personnel_id)}}"> {{$parson->name}}</option>
                                    　　@endforeach
                                </select>

                            </form>


                        </div>
                        <div class="col-4">
                            <?php $date = new DateTime('now'); ?>
                            <p>開始年月日：<input type="date" name="startdate" style="width:120px;"></p>
                            <p>終了年月日：<input type="date" name="finishdate" style="width:120px;"></p>
                            <!-- <p title="開始年月日時刻を表示します"> 開始年月日時刻　： <?= $date->format('m月d日'); ?>　0時0分</p>
                <p title="終了年月日時刻を表示します"> 終了年月日時刻　： <?= $date->format('m月d日'); ?></p> -->
                        </div>
                    </div>

                    <div class="row justify-content-start">
                        <div class="col-4">
                            　<input type="checkbox" name="check" title="通常メッセージログを表示するかどうかを指定します"> 通常メッセージ
                        </div>
                        <div class="col-4">
                            　<input type="checkbox" name="check" title="警告メッセージログを表示するかどうかを指定します" checked> 警告メッセージ
                        </div>
                        <div class="col-4">
                            　<input type="checkbox" name="check" title="異常メッセージログを表示するかどうかを指定します" checked> 異常メッセージ
                        </div>
                    </div>

                    <div class="row justify-content-start">
                        <div class="col-4">
                            　<input type="checkbox" name="check" title="正常メッセージログを表示するかどうかを指定します" checked> 正常メッセージ
                        </div>
                        <div class="col-4">
                            <input type="checkbox" name="check" title="システム情報メッセージログを表示するかどうかを指定します"> システム情報メッセージ
                        </div>
                        <div class="col-4">
                            <input type="checkbox" name="check" title="システム異常メッセージログを表示するかどうかを指定します"> システム異常メッセージ
                        </div>
                    </div>

                    <div class="row mt-3">

                        <div class="col-10">　文字検索：<input type="text" name="kensaku" style="width:32rem;" placeholder="入力可能な文字数は３２  全角、半角英数字、一覧操作領域">

                        </div>
                        <div class="col-2">
                            <input type="submit" id="formGroupExampleInput17" value="表示する">
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
                        <div class="wrapper" style="overflow-y: scroll; background-color:white;">
                            <table class="table">

                                <thead>
                                    <tr>
                                        <th scope="col">開始日 </th>
                                        <th scope="col">終了日</th>
                                        <th scope="col">log</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                    @php
                                    $start_date = $item->created_at;
                                    $finish_date = $item->updated_at;
                                    @endphp

                                    <tr>
                                        <td> {{date('Y年m月d日', strtotime($start_date))}}0時0分</td>
                                        <td> {{date('Y年m月d日', strtotime($finish_date))}}</td>
                                        <td> {{$item->log}}</td>
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>

                        </div>

                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection