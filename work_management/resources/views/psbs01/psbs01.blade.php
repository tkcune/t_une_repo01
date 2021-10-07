
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
            <div class="col-6">
            名前<input type="text" name="name">
            </div>
            <div class="col-2">
            状態
            <select name="status">
                <option value="10">開設提案</option>
                <option value="11">審査</option>
                <option value="12">開設待</option>
                <option value="13">稼働中</option>
                <option value="14">休止</option>
                <option value="18">廃止</option>
            </select>
            </div>
            <div class="col">
                <input type="submit" value="登録">
            </div>
            </form>
    </div>     
</div>

@endsection