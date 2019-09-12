<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/9/1
 * Time: 10:13
 */
use App\IekModel\Version1_0\Constants\Path;
$path = Path::FILE_PATH;
$data = $result->data;
?>
<div class="widget-content form-horizontal">
    @foreach($data as $k => $da)
        <div class="control-group">
            <div class="control-label">
                <label>产品图：</label>
            </div>
            <div class="controls">
                @if(!is_null($da->products))
                    <img src="{{$path.$da->products->productThumb->thumb->norm[4]->uri}}" alt="">
                @else
                    <code>无</code>
                @endif
            </div>
        </div>
        <div class="control-group">
            <div class="control-label">
                <label>用户上传实物照片：</label>
            </div>
            <div class="controls">
                @if(!$da->rejectRequestImages->isEmpty())
                    @foreach($da->rejectRequestImages as $img)
                        <img src="{{$path.$img->images[4]->uri}}" alt="">
                    @endforeach
                @else
                    <code>用户未上传图片</code>
                @endif
            </div>
        </div>
        <div class="c-data">
            <div class="control-group">
                <div class="control-label">
                    <label>退换产品是否回收：</label>
                </div>
                <div class="controls">
                    <label>
                        <input type="radio" class="recycle" name="is_recycling{{$k+1}}" value="1" />
                        需要回收</label>
                    <label>
                        <input type="radio" class="recycle" name="is_recycling{{$k+1}}" value="0" />
                        不需回收</label>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <label>退换审核状态：</label>
                </div>
                <div class="controls">
                    <label>
                        <input type="radio" class="c-status" name="status{{$k+1}}" value="0" />
                        通过</label>
                    <label>
                        <input type="radio" class="c-status" name="status{{$k+1}}" value="1" />
                        不通过</label>
                </div>
            </div>
            <div class="control-group">
                <div class="control-label">
                    <label>退换审核理由：</label>
                </div>
                <div class="controls">
                    <input name="reason" class="span4 reas" />
                </div>
            </div>
            <input class="reject-product-id" type="hidden" data="{{$da->id}}">
        </div>
        <hr>
    @endforeach
    <input id="reject-request-id" type="hidden" data="{{$da->reject_request_id}}">
    <a href="javascript:void (0);" class="btn btn-success" id="save-reject">保存</a>
</div>
<script>
    $('#save-reject').on('click',function(){
        var rejectRequestId = $('#reject-request-id').attr('data');
        var arr = [];
        $('.c-data').each(function(){
            var recycle = $(this).find('.recycle:checked').val();
            var status = $(this).find('.c-status:checked').val();
            var reason = $(this).find('[name="reason"]').val();
            var rpid = $(this).find('.reject-product-id').attr('data');
            arr.push({
                status : status,
                recycle : recycle,
                reason : reason,
                id : rpid
            });
        });
        var param = {};
        param.data = {};
        param.data.id = rejectRequestId;
        param.data.arr = arr;
        //console.log(param);
        ajaxData('post', 'reject', function (result) {
            if(!isNull(result)) {
                $('#container').append(result);
            }
        }, [], param);
    })
</script>