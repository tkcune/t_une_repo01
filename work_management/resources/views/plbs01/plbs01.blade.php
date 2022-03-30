
        <div class="border border-dark" style="max-height: 100px; overflow-y:scroll;">
            <table id="bs-table" class="tablesorter hasStickyHeaders" style="margin-bottom:0px;margin-top:0px;">
                <thead>
                    <tr>
                        <th>部署番号</th>
                        <th>部署名</th>
                        <th>上位部署</th>
                        <th>状態</th>
                        <th>責任者</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($departments as $department)
                        <tr>
                            <td>{{$department->department_id}}</td>
                            @if($department->operation_start_date > \Carbon\Carbon::today()->format('Y-m-d') || (!(null == $department->operation_end_date) && \Carbon\Carbon::today()->format('Y-m-d') > $department->operation_end_date))
                            <td><s><a href="{{ route('plbs01.show',[session('client_id'),$department->department_id])}}" data-toggle="tooltip" title="運用中ではありません">{{$department->name}}</a></s></td>
                            @else
                            <td><a href="{{ route('plbs01.show',[session('client_id'),$department->department_id])}}" data-toggle="tooltip" title="クリックにより、当該部署に遷移します">{{$department->name}}</a></td>
                            @endif
                            <td>@if(!(empty($department_high)))<a href="{{ route('plbs01.show',[session('client_id'),$department_high[$loop->index]->department_id])}}" data-toggle="tooltip" title="クリックにより、上位部署に遷移します">{{$department_high[$loop->index]->name}}</a>@endif</td>
                            <td>
                            @switch($department->status)
                                    @case(10)
                                    開設提案
                                    @break
                                    @case(11)
                                    審査
                                    @break
                                    @case(12)
                                    開設待
                                    @break
                                    @case(13)
                                    稼働中
                                    @break
                                    @case(14)
                                    休止
                                    @break
                                    @case(18)
                                    廃止
                                    @break
                                @endswitch
                            </td>
                            <td><a href="{{ route('plbs01.show',[session('client_id'),$department->responsible_person_id])}}" data-toggle="tooltip" title="クリックにより、責任者の人員詳細に遷移します">{{ $responsible_lists[$loop->index] }}</a></td>
                                <td width="190">
                                    【<a href="{{ route('pa0001.clipboard',$department->department_id)}}">複写</a>】
                                    【<p id="bs_list_delete{{$loop->index}}" name="bs_delete" style="pointer-events: none; display:inline-block; text-decoration:underline; margin:0px;" onclick="event.preventDefault(); document.getElementById('bs_delete{{$loop->index}}').submit();">削除</p>】
                                <form id="bs_delete{{$loop->index}}" action="{{ route('psbs01.delete',[session('client_id'),$department->department_id])}}" method="post" style="display: none;">
                                    @csrf
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>