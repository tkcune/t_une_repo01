@extends('pc0001.pc0001')

    
@section('content')
    <div class="col border border-primary" style="padding:10px;">
        <div class="row">
            <form action="{{ route('pskb01.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('post')

            <input type="hidden" name="high" value="{{ $_GET["high"] }}">

            <div class="details-area border border-dark bg-warning" style="padding:10px;" id="parent">
                <div class="row">
                    <div class="col-4" style="margin-top:-5px; margin-right:-12px">
                        <h2>掲示板登録</h2>
                    </div>
                    <div class="col-4" style="margin-right:-10px">
                        <p id="palent">
                            <span data-toggle="tooltip" id="id_number" title="">タイトル</span>
                            <input type="text" name="name" value="{{ old('name') }}">
                        </p>
                    </div>
                    
                    <div class="col-3">
                        @if(isset($board_details[0]->high_id))
                        <p>上位:<a href="" data-toggle="tooltip" title="クリックにより、上位部署に遷移します"></a></p>
                        @endif
                    </div>
                <div>

                <div class="row">
                    <div class="col-4">
                        <p>管理者番号：<input type="text" id="management_number" name="management_number" maxlength="10" value="" style="width:100px;"
                        data-toggle="tooltip" title=""></p>
                    </div>
                    <div class="col-3" style="padding:0px">
                        <p>管理者名：</p>
                    </div>
                    <div class="col" style="padding:0px">
                    <p>管理者検索：
                        <input type="search" id="search-list" list="keywords" style="width:150px;" autocomplete="on" maxlength="32"
                        
                        data-toggle="tooltip" title="入力に該当した人員の候補を一覧に表示します。表示された人員を選択した場合、その番号が管理者人員番号に表示されます。">
                        <datalist id="keywords">
                        </datalist>
                    </p>
                    </div>
                </div>


                <div class="row">
                    <div class="col">
                        <p>状態:
                        <select name="status" data-toggle="tooltip" title="人員の状態を選択します">
                        <option value="10" @if(old("status") == 10) selected @endif>応募</option>
                        <option value="11" @if(old("status") == 11) selected @endif>審査</option>
                        <option value="12" @if(old("status") == 12) selected @endif>入社待</option>
                        <option value="13" @if(old("status") == 13) selected @endif>在職</option>
                        <option value="14" @if(old("status") == 14) selected @endif>休職</option>
                        <option value="18" @if(old("status") == 18) selected @endif>退職</option>
                        </select>

                        参照ファイル
                        <input type="file" name="file_name" value="" style="width:254px;" data-toggle="tooltip" title="ファイルをアップロード" >
                        URL
                        <input type="text" id="url" name="url" maxlength="10" value="" style="width:200px;"
                        data-toggle="tooltip" title="URLを送信">

                        <button class="main_button_style" type="button" id="remarks_change_display" onclick="remarksOn()" data-toggle="tooltip" title="クリックにより、備考及び登録日などの情報を開きます">
                            <img class="remarks_button" src="data:image/png;base64,{{Config::get('base64.updown')}}" alt="開閉" >
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
                            <textarea id="remarks_set" onchange = "remarks(this value)" maxlength="512" style="width:800px; height: 60px;">{{ old('remarks') }}</textarea>
                        </div>
                    </div>

                    <div class="row" id="little-information-field" style="display:none;">

                    </div>

                    <div class="row margin-reset">
                        <div class="col">
                            <p>
                            <div style="display:inline-flex">
                                <input class="main_button_img" type="image" src="data:image/png;base64,{{Config::get('base64.ok')}}" alt="確定" onclick="submit();" data-toggle="tooltip" title="クリックにより、登録、更新を確定します">
                            </div>
            </form>

                            <button class="main_button_style" type="button" id="tree_change_display" data-toggle="tooltip" title="ツリーを表示します" onclick="displayOn()">
                                <img class="main_button_img" src="data:image/png;base64,{{Config::get('base64.tree')}}" alt="開く" >
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    </div> 
</div>

@endsection