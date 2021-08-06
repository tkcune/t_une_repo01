@extends('pc0001.pc0001')

    
@section('content')
<div class="col border border-primary" style="padding:10px;">
    <div class="row">
            <form action="{{ route('psbs01.create') }}" method="post">
            <div class="col-6">
            名前<input type="text">
            </div>
            <div class="col-2">
            状態
            <select name="state">
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