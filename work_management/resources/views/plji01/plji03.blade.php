<!-- 人員一覧のblade -->
<div class="border border-dark">
    <table id="ji-table" class="ji-table table_sticky table table-striped" style=" margin-bottom:0px;margin-top:0px;">
        <thead>
            <tr>
                <th width="102">人員番号</th>
                <th width="100">氏名</th>
                <th width="130">所属部署</th>
                <th width="80">状態</th>
                <th width="60">ID</th>
                <th width="100">PW更新</th>
                <th width="80">権限</th>
                <th width="160">操作</th>
            </tr>
        </thead>
        <tbody>
            @foreach($names as $name)
            <tr>
                <td width="100">{{ $name->personnel_id}}</td>
                @if($name->operation_start_date > \Carbon\Carbon::today()->format('Y-m-d') || (!(null == $name->operation_end_date) && \Carbon\Carbon::today()->format('Y-m-d') > $name->operation_end_date))
                <td width="100"><s><a href="{{ route('plbs01.show',[session('client_id'),$name->personnel_id])}}">{{$name->name}}</a> </s> </td>
                @else
                <td width="100"><a href="{{ route('plbs01.show',[session('client_id'),$name->personnel_id])}}">{{$name->name}}</a></td>
                @endif
                <td width="130"><a href="{{ route('plbs01.show',[session('client_id'),$name->high_id])}}" data-toggle="tooltip" title="クリックにより、所属部署に遷移します">{{$name->high_name}}</a>
                <td width="80">
                    @switch($name->status)
                    @case(10)
                    応募
                    @break
                    @case(11)
                    審査中
                    @break
                    @case(12)
                    入社待
                    @break
                    @case(13)
                    在職
                    @break
                    @case(14)
                    休職
                    @break
                    @case(18)
                    退職
                    @break
                    @endswitch
                </td>
                <td width="60">aaa02</td>
                <td width="100">{{$name->password_update_day}}</td>
                <td width="80">---------</td>
                <td width="162">【<a href="{{ route('pa0001.clipboard',$name->personnel_id)}}">複写</a>】
                    【<p id="list_delete{{$loop->index}}" name="bs_delete" style="pointer-events: none; display:inline-block; text-decoration:underline; margin:0px;" onclick="event.preventDefault(); document.getElementById('delete{{$loop->index}}').submit();">削除</p>】
                    <form id="delete{{$loop->index}}" action="{{ route('psji01.destroy',[session('client_id'),$name->personnel_id])}}" method="post" style="display: none;">
                        @csrf
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
