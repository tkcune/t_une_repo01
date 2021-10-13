
@extends('pc0001.pc0001')

    
@section('content')
<div class="col border border-primary" style="padding:10px;">
    <div class="row">
            <form action="{{ route('psbs01.store') }}" method="post">
            @csrf
            @method('post')
            {{-- hiddenのvalueはダミーデータ　--}}

            <input type="hidden" name="client_id" value="{{ session('client_id') }}">
            <input type="hidden" name="responsible_person_id" value="ji00000001">
            <input type="hidden" name="management_personnel_id" value="ji00000001">

            {{-- ダミーデータここまで　--}}
            <input type="hidden" name="high" value="{{ $_GET["high"] }}">

            <div class="details-area border border-dark bg-warning" style="padding:10px;" id="parent">
                <div class="row">
                    <div class="col-4">
                        <p id="palent">部署名<input type="text" name="name" value="" data-toggle="tooltip" title="部署の名称を入力します"></p>
                    </div>
                    <div class="col">

                    </div>
                    <div class="col">

                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <p>状態:
                        <select name="status" data-toggle="tooltip" title="部署の状態を選択します">
                        <option value="10">開設提案</option>
                        <option value="11">審査</option>
                        <option value="12">開設待</option>
                        <option value="13">稼働中</option>
                        <option value="14">休止</option>
                        <option value="18">廃止</option>
                        </select>
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div style="display:inline-flex">
                            <input type="submit" value="確定" data-toggle="tooltip" title="クリックにより、登録、更新を確定します">
                        </div>
                    </div>
                </div>
            </form>
            </div>
    </div>     
</div>

@endsection