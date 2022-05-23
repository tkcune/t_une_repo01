<!-- 一覧画面　人員ベース -->
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
            <tr>
                <td width="102"></td>
                <td width="100"><s><a href="#"></a> </s> </td>
                <td width="130"><a href="#" data-toggle="tooltip"></a>
                <td width="80"></td>
                <td width="60"></td>
                <td width="100"></td>
                <td width="80"></td>
                <td width="160">【<a href="#">複写</a>】
                【<p id="" name="" style="pointer-events: none; display:inline-block; text-decoration:underline; margin:0px;" onclick="event.preventDefault(); document.getElementById('').submit();">削除</p>】
                    <form id="" action="#" method="post" style="display: none;">
                        @csrf
                    </form>
                </td>
            </tr>
        </tbody>
    </table>
</div>
