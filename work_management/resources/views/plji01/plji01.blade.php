
        <div class="border border-dark" style="max-height: 100px; overflow-y:scroll;">
            <table id="ji-table" class="tablesorter hasStickyHeaders" style="margin: 0 0">
                <thead>
                    <tr>
                        <th>人員番号</th>
                        <th>氏名</th>
                        <th>所属部署</th>
                        <th>状態</th>
                        <th>ID</th>
                        <th>PW更新</th>
                        <th>権限</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($names as $name)
                    <tr>
                        <td>{{ $name->personnel_id}}</td>
                        @if($name->operation_start_date > \Carbon\Carbon::today()->format('Y-m-d') || (!(null == $name->operation_end_date) && \Carbon\Carbon::today()->format('Y-m-d') > $name->operation_end_date))
                        <td><s><a href="{{ route('plbs01.show',[session('client_id'),$name->personnel_id])}}" data-toggle="tooltip" title="運用中ではありません">{{$name->name}}</a> </s> </td>
                        @else
                        <td><a href="{{ route('plbs01.show',[session('client_id'),$name->personnel_id])}}" data-toggle="tooltip" title="クリックにより、当該人員に遷移します">{{$name->name}}</a></td>
                        @endif 
                        <td><a href="{{ route('plbs01.show',[session('client_id'),$personnel_high[$loop->index]->department_id])}}" data-toggle="tooltip" title="クリックにより、所属部署に遷移します">{{$personnel_high[$loop->index]->name}}</a>
                        <td>
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
                        <td>aaa02</td>
                        <td>{{$name->password_update_day}}</td>
                        <td>---------</td>
                        <td>【<a href="{{ route('pa0001.clipboard',$name->personnel_id)}}">複写</a>】
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