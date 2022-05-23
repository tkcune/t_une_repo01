<!-- 作業場所一覧のblade -->
<div class="border border-dark">
    <table id="sb-table" class="sb-table table_sticky-info table table-striped" style="margin-bottom:0px;margin-top:0px;">
        <thead>
            <tr>
                <th width="100">番号</th>
                <th width="300">名称</th>
                <th width="300">上位</>
                <th width="300">操作</th>
        </thead>
        <tbody>
            @foreach($space_details as $space)
            <tr>
                <td width="100">{{$space->space_id}}</>
                <td width="300"><a href="{{ route('pssb01.show',[session('client_id'),$space->space_id])}}" data-toggle="tooltip" title="">{{$space->name}}</a></td>
                <td width="300"><a @if(isset($space->high_id))<a href="{{ route('pssb01.show',[session('client_id'),$space->high_id])}}" data-toggle="tooltip" title="">{{$space->high_name}}</a>@endif</td>
                <td width="300">【<a href="{{ route('pa0001.clipboard',$space->space_id)}}">複写</a>】
                    【<p id="sb_list_delete{{$loop->index}}" name="sb_delete" style="pointer-events: none; display:inline-block; text-decoration:underline; margin:0px;" onclick="event.preventDefault(); document.getElementById('delete{{$loop->index}}').submit();">削除</p>】
                    <form id="delete{{$loop->index}}" action="{{ route('pssb01.destroy',[session('client_id'),$space->space_id])}}" method="post" style="display: none;">
                        @csrf
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
