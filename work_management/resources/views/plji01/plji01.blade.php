<div class="row margin-reset">
    <div class="col">
        <div>
            <table id="ji-table" class="ji-table table_sticky table table-striped" style="margin: 0 0">
                <thead>
                    <tr>
                        <th style="white-space: nowrap; font-size: 8px;">人員番号</th>
                        <th style="white-space: nowrap; font-size: 8px;">氏名</th>
                        <th style="white-space: nowrap; font-size: 8px;">所属部署</th>
                        <th style="white-space: nowrap; font-size: 8px;">状態</th>
                        <th style="white-space: nowrap; font-size: 8px;">ID</th>
                        <th style="white-space: nowrap; font-size: 8px;">PW更新</th>
                        <th style="white-space: nowrap; font-size: 8px;">権限</th>
                        <th style="white-space: nowrap; font-size: 8px;">操作</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($names as $name)
                    <tr>
                        <td style="font-size: 8px;">{{ $name->personnel_id}}</td>
                        @if($name->operation_start_date > \Carbon\Carbon::today()->format('Y-m-d') || (!(null == $name->operation_end_date) && \Carbon\Carbon::today()->format('Y-m-d') > $name->operation_end_date))
                        <td style="font-size: 8px;"><s><a href="{{ route('plbs01.show',[session('client_id'),$name->personnel_id])}}" data-toggle="tooltip" title="運用中ではありません">{{$name->name}}</a> </s> </td>
                        @else
                        <td style="font-size: 8px;"><a href="{{ route('plbs01.show',[session('client_id'),$name->personnel_id])}}" data-toggle="tooltip" title="クリックにより、当該人員に遷移します">{{$name->name}}</a></td>
                        @endif 
                        <td style="font-size: 8px;"><a href="{{ route('plbs01.show',[session('client_id'),$personnel_high[$loop->index]->department_id])}}" data-toggle="tooltip" title="クリックにより、所属部署に遷移します">{{$personnel_high[$loop->index]->name}}</a>
                        <td style="font-size: 8px;">
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
                        <td style="font-size: 8px;">aaa02</td>
                        <td style="font-size: 8px;">{{$name->password_update_day}}</td>
                        <td style="font-size: 8px;">---------</td>
                        <td style="font-size: 8px;">【<a href="{{ route('pa0001.clipboard',$name->personnel_id)}}">複写</a>】
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
    </div>
</div>