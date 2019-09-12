<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2016/12/22
 * Time: 11:03
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
        @foreach($data->manageLog as $log)
            <?php $contents= json_decode($log->content); ?>
            <tr>
                <td>{{$data->name}}</td>
                @if(!is_null($contents))
                    @foreach($contents as $content)
                        <td>{{$log->reason['reason']}}</td>
                        <td>{{$content->is_forbidden == true ? '禁止': '取消禁止'}}</td>
                    @endforeach
                @else
                    <td>{{$log->reason['reason']}}</td>
                    <td>测试数据</td>
                @endif
                <td data-time="utc">{{$log->created_at}}</td>
                <td>{{$log->operator_id."-".$log->operator['name']}}</td>
            </tr>
        @endforeach
    @endif
</table>
<script>
    $(document).ready(function(){
        convertUtcTimeToLocalTime('data-reason');
    })
</script>