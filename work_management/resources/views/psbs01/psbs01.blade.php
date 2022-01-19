
@extends('pc0001.pc0001')

    
@section('content')
<div class="col border border-primary" style="padding:10px;">
    <div class="row">
            <form action="{{ route('psbs01.store') }}" method="post">
            @csrf
            @method('post')
            {{-- hiddenのvalueはダミーデータ　--}}

            <input type="hidden" name="client_id" value="{{ session('client_id') }}">

            {{-- ダミーデータここまで　--}}
            <input type="hidden" name="high" value="{{ $_GET["high"] }}">

            <div class="details-area border border-dark bg-warning" style="padding:10px;" id="parent">
                <div class="row">
                    <div class="col-4" style="margin-top:-5px; margin-right:-12px">
                        <h2>部署登録</2>
                    </div>
                    <div class="col-4">
                        <p id="palent">部署名<input type="text" name="name" value="{{ old('name') }}" data-toggle="tooltip" title="部署の名称を入力します"></p>
                    </div>
                </div>

                <div class="row margin-reset">
                    <div class="col-4">
                        <p>管理者番号：<input type="text" id="management_number" name="management_number" maxlength="10" data-toggle="tooltip" 
                        title="部署情報を修正、抹消できる管理者を変更する場合、ここを修正します 管理者自身とシステム管理者だけが修正できます"
                        value="ji00000001" style="width:100px;" readonly></p>
                    </div>
                    <div class="col-3" style="padding:0px">
                        <p>管理者名：</p>
                    </div>
                    <div class="col" style="padding:0px">
 
                    <p>管理者検索：
                        <input type="text" list="keywords" style="width:150px;"autocomplete="on" maxlength="32"
                        data-toggle="tooltip" title="入力に該当した人員の候補を一覧に表示します。表示された人員を選択した場合、その番号が管理者人員番号に表示されます。">
                    </p>
                    </div>
                    <div id="output_message"></div>
                </div>

                <div class="row margin-reset">
                    <div class="col">
                        <p>状態:
                        <select name="status" data-toggle="tooltip" title="部署の状態を選択します">
                        <option value="10" @if(old("status") == 10) selected @endif>開設提案</option>
                        <option value="11" @if(old("status") == 11) selected @endif>審査</option>
                        <option value="12" @if(old("status") == 12) selected @endif>開設待</option>
                        <option value="13" @if(old("status") == 13) selected @endif>稼働中</option>
                        <option value="14" @if(old("status") == 14) selected @endif>休止</option>
                        <option value="18" @if(old("status") == 18) selected @endif>廃止</option>
                        </select>
                        責任者:
                        <select name="responsible_person_id" style="width:90px; margin:0px;" data-toggle="tooltip" title="部署の責任者を選択します" readonly>
                            <option value="ji00000001">山田一郎</option>
                        </select>

                        運用開始日<input name="start_day" type="date" style="width:140px; margin:0px; value="{{date('Y-m-d')}}" readonly>
                        運用終了日<input name="finish_day" type="date" style="width:140px; margin:0px; value="" readonly>

                        &emsp;&emsp;
                        
                        <button class="main_button_style" type="button" id="remarks_change_display" onclick="remarksOn()" data-toggle="tooltip" title="クリックにより、備考及び登録日などの情報を開きます">
                            <img class="remarks_button" src="../../image/updown.png" alt="開閉" >
                        </button>

                        </p>
                    </div>
                </div>

                <input type="hidden" id="remarks" name="remarks" value="">

                <div class="row margin-reset" id="remarks-field" style="display:none"">
                    <div>
                        備考
                    </div>
                    <div>
                        <textarea id="remarks_set" onchange = "remarks(this value)" maxlength="512" style="width:800px; height: 60px;"></textarea>
                    </div>
                </div>

                <div class="row" id="little-information-field" style="display:none;">

                </div>

                <div class="row">
                <div class="col">
                    <p>
                    <div style="display:inline-flex">
                        <input class="main_button_img" type="image" src="../../image/ok.png" alt="確定" onclick="submit();" data-toggle="tooltip" title="クリックにより、登録、更新を確定します">
                    </div>
            </form>
                    <button class="main_button_style" type="button" id="tree_change_display" data-toggle="tooltip" title="ツリーを表示します" onclick="displayOn()">
                        <img class="main_button_img" src="../../image/tree.png" alt="開く" >
                    </button>
                </div>
            </div>
    </div>     
</div>

@endsection