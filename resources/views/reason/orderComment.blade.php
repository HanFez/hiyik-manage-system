<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2019/6/11
 * Time: 16:26
 */
?>
<table class="table table-bordered table-striped" id="data-reason">
    <tr>
        <th>操作内容</th>
        <th>操作原因</th>
        <th>操作类型</th>
        <th>操作时间</th>
        <th>操 作 人</th>
    </tr>
    @if(!$data->manageLog->isEmpty())
        @foreach($data->manageLog as $item)
            <?php $contents= json_decode($item->content); ?>
            <tr>
                <td>{{$data['content']}}</td>
                @if(!is_null($contents))
                    @foreach($contents as $content)
                        <td>{{$item->reason['reason']}}</td>
                        <td>{{$content->is_forbidden == true ? '禁止': '取消禁止'}}</td>
                    @endforeach
                @else
                    <td>{{$item->reason['reason']}}</td>
                    <td>测试数据</td>
                @endif
                <td data-time="utc">{{$item['created_at']}}</td>
                <td>{{$item->operator_id."-".$item->operator['name']}}</td>
            </tr>
        @endforeach
    @endif
</table>
<script>
    $(document).ready(function(){
        convertUtcTimeToLocalTime('data-reason');
    })
</script>
