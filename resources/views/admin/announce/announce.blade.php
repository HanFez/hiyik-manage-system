<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 11/12/16
 * Time: 10:11 AM
 */
use App\IekModel\Version1_0\IekModel;

$operator = null;
if($announce->statusCode == 0 && !is_null($announce->data)) {
    if(!is_null($announce->data->operator)) {
        $operator = $announce->data->operator;
    }
}
//dd(json_decode(json_encode($announce)));
?>

@if($announce->statusCode == 10014)
    <script>
        bootstrapQ.alert('{{$announce->message}}');
    </script>
@elseif($announce-> statusCode == 0)
    <div class="dialog-title">
        公告标题: {{$announce->data->title}}
    </div>
    <div class="dialog-count">
        <span>热度: {{$announce->data->hits}}</span><br/>
    </div>
    <div class="dialog-content" announceId="{{$announce->data->id}}" style="">
        <div class="group margin-bottom">
            <div class="group-left">
                开始日期:
            </div>
            <div class="group-right">
                {{$announce->data->begin_at}}
            </div>
        </div>
        <div class="group margin-bottom">
            <div class="group-left">
                结束日期:
            </div>
            <div class="group-right">
                {{$announce->data->end_at}}
            </div>
        </div>
        <div class="group margin-bottom">
            <div class="group-left">
                当前日期:
            </div>
            <div class="group-right">
                {{date('Y-m-d')}}
            </div>
        </div>
        <div class="group margin-bottom">
            <div class="group-left" date-id="{{ $operator == null ? 'null' : $announce->data->operator->id }}">
                创建人:
            </div>
            <div class="group-right">
                {{ $operator == null ? 'null' : $announce->data->operator->name }}
            </div>
        </div>
        <div class="group margin-bottom">
            <div class="group-left">
                备注:
            </div>
            <div class="group-right">
                {{ $announce->data->memo or '无' }}
            </div>
        </div>
        <div class="group margin-bottom">
            <div class="group-left">
                审核状态:
            </div>
            <div class="group-right">
                @foreach($announce->status as $key => $status)
                    @if($status == $announce->data->announceReview->status)
                        <span>{{ IekModel::strTrans(strtolower($key), 'announceStatus') }}</span><br/>
                    @endif
                @endforeach
            </div>
        </div>
        <div class="group margin-bottom">
            <div class="group-left" >
                审核人:
            </div>
            <div class="group-right">
                {{$announce->data->announceReview->operator_id or '无' }}
            </div>
        </div>
        <div class="group margin-bottom">
            <div class="group-left">
                审核备注:
            </div>
            <div class="group-right">
                {{$announce->data->announceReview->memo or '无' }}
            </div>
        </div>
        <div class="group margin-bottom">
            <div class="group-left">
                公告内容:
            </div>
            <div class="group-right">
                <div class="announce-content">
                    <?php echo $announce->data->content ?>
                </div>
            </div>
        </div>
    </div>

    <div class="dialog-footer">
        @if($announce->data->is_active)
            <button class="btn btn-info" type="announce" data="{{$announce->data->id}}" data-type="audit">修改审核状态</button>
        @endif
        @if($announce->data->is_active)
            <button class="btn btn-danger" type="announce" data="{{$announce->data->id}}" data-type="delete">删除</button>
        @endif
            <a id="modifyAnnounce" class="btn btn-primary" type="announce" data="{{$announce->data->id}}" >编辑</a>
    </div>
    <div id="form-reason" class="hide">
        <form class="form-horizontal">
            <div class="control-group">
                <label class="control-label">
                    状态 :
                </label>
                <div class="controls">
                    @foreach($announce->status as $key => $status)
                        <label class="hide">
                            <input type="radio" name="reason-id" style="opacity: 0;" value="{{$status}}" data-type="audit" {{ $status == $announce->data->announceReview->status ? 'checked="checked"' : '' }}>
                            {{ IekModel::strTrans(strtolower($key), 'announceStatus') }}
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">
                    备注 :
                </label>
                <div class="controls">
                    <textarea placeholder="请输入备注" name="memo"></textarea>
                </div>
            </div>
        </form>
    </div>
@endif
<script>
    $('#modifyAnnounce').on('click', bindEventToShowAnnounceEdit)
</script>

