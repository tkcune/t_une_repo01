@extends('pc0001.pc0001')

@section('content')
<div class="col border border-primary" style="padding:10px;">
    <div class="container-fluid">
        <div class="row">
            <div class="details-area border border-dark bg-warning" style="padding:10px;" id="parent">
                <div class="container-fluid">
                    <div class="row">
                        <!-- <P style="text-align: center">―　ログ確認　―</P> -->
                        <div class="col-3"></div>
                        <div class="col-6"></div>
                        <div class="col-3" style="text-align: right">―　ログページ　―</div>
                    </div>

                    <div class="row">
                        <p style="text-align: center">検索したい条件などを入力して「検索」ボタンを押してください</p>
                        <div class="col-4">
                            <form action="{{ route('pslg01.create') }}" method="post">
                                @csrf

                                <p>顧客番号： <input type="text" style="width:100px;" placeholder="半角英数字で入力"></p>
                                <p>顧客名　： <a href="#" style="color:black;" title="クリックにより顧客詳細に遷移します">　(例)前川一号生</a></p>
                                <p class="box" title="入力に該当した顧客の候補を一覧に表示します。表示された人員を選択した場合、その番号が顧客番号に表示されます">顧客検索：　
                                    <select class="box" style="width:100px;">
                                      
                                        <option selected value=""></option>
                                      
                                        @foreach( $name_data as $name)
                                          <option value="{{$name->personnel_id}}"> {{$name->name}}</option>
                                        @endforeach
                                    </select>
                                </p>
                        </div>
                        <div class="col-4">
                            <p title="入力に該当した顧客の候補を一覧に表示します。表示された人員を選択した場合、その番号が顧客番号に表示されます。">部署人員番号：
                                <input type="text" style="width: 100px;">
                                <!-- <input type="text" style="width: 100px;" placeholder="半角英数字以外はNG出力対象とする部署番号　未入力で全ての部署人員"> -->
                            </p>



                            <p>部署人員名　：　<a href="#" style="color:black;" title="クリックにより顧客詳細に遷移します">前川一号生</a></p>
                            <!-- ※　クリックにより、顧客詳細に遷移する。 顧客番号に該当する部署の名称 -->
                            <!-- <label for="formGroupExampleInput6" class="form-label"> 6 部署人員名 </label> -->

                            <p title="入力に該当した人員の候補を一覧に表示します。表示された人員を選択した場合、その番号が部署番号に表示されます。"> 部署人員検索：
                                <input list="lists" id="myList" name="myList" style="width:100px" />
                                <datalist id="lists">
                                    <option value="全角、半角英数字">
                                    <option value="入力に該当した部署の候補を一覧表示し、選択とその番号が部署番号に表示される">
                                </datalist>
                            </p>
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
                            　<input type="checkbox" title="通常メッセージログを表示するかどうかを指定します"> 通常メッセージ
                        </div>
                        <div class="col-4">
                            　<input type="checkbox" title="警告メッセージログを表示するかどうかを指定します" checked> 警告メッセージ
                        </div>
                        <div class="col-4">
                            　<input type="checkbox" title="異常メッセージログを表示するかどうかを指定します" checked> 異常メッセージ
                        </div>
                    </div>

                    <div class="row justify-content-start">
                        <div class="col-4">
                            　<input type="checkbox" title="正常メッセージログを表示するかどうかを指定します" checked> 正常メッセージ
                        </div>
                        <div class="col-4">
                            <input type="checkbox" title="システム情報メッセージログを表示するかどうかを指定します"> システム情報メッセージ
                        </div>
                        <div class="col-4">
                            <input type="checkbox" title="システム異常メッセージログを表示するかどうかを指定します"> システム異常メッセージ
                        </div>
                    </div>

                    <div class="row mt-3">
                        <!-- <div class="col-1"></div> -->
                        <div class="col-10">　文字検索：<input type="text" style="width:32rem;" placeholder="入力可能な文字数は３２  全角、半角英数字、一覧操作領域">

                        </div>
                        <div class="col-2">
                            <input type="submit" id="formGroupExampleInput17" value="表示する">
                        </div>
                    </div>
                    </form>
                    <div class="row mt-3">
                        <div class="col-10">
                            @isset($items)
                            <div class="wrapper" style="overflow-y: scroll; background-color:white;">
                                @foreach($items as $item)
                                <div class="contents">
                                    {{$item->program_pass}}
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        <div class="col-2">
                            @isset($count)
                            件数　：{{$count}} 件
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection