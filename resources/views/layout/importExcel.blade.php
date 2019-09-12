<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 1/5/18
 * Time: 16:48 AM
 */

$data = isset($data) ? $data : null;
$type = isset($type) ? $type : null;

$id = 'btn-import-excel';
if(isset($type)) {
    $id .= $type;
}
?>
<div class="form-actions">
    <a href="javascript:void(0)" class="btn btn-primary add-image upload-file">
        @if(isset($type))
            @if($type == 'produceParam')
                导入生产数据Excel
            @elseif($type == 'taoBaoOrder')
                导入淘宝订单报表
            @elseif($type == 'taoBaoOrderProduct')
                导入淘宝宝贝报表
            @else
                导入Excel
            @endif
        @else
            导入Excel
        @endif
        <input id="{{$id}}" type="file">
    </a>
</div>

<div class="control-group">
    <label class="control-label"><span class="label label-important">注:</span></label>
    <div class="controls">
        <div class="content">1. 支持的文件格式有
            <span class="label label-success">*.xls</span>,
            <span class="label label-success">*.xlsx</span>
        </div>
        <div class="content">2. Excel中内容部分可有背景色，<span class="label label-info">无内容部分请取消背景色</span><br>
            取消背景色法一：更改主题颜色，若无任何变化，请选择法二 <br>
            取消背景色法二： 全部选中，点击菜单“开始”->“条件格式”->“管理规则”/“清除规则”，删除全部规则即可
        </div>
        <div class="content">3.Excel表内标题如下：
            @if(isset($data))
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        @foreach($data as $item)
                            <th>{{ $item }}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        @foreach($data as $item)
                            <td>{{ $item }}</td>
                        @endforeach
                    </tr>
                    </tbody>
                </table>
            @else
                暂无数据
            @endif
        </div>
    </div>
</div>
<script>
    $(function () {
        var type = '{{ $type }}';
        if(isNull(type)) {
            typeError(type);
        }
        $('#{{$id}}').on('change', function () {
            loadingShow();
            if(isNull(type)) {
                typeError(type);
            } else {
                var path = originPath();
                var url = '';
                if(type == 'publication') {
                    url = 'publications';
                } else if(type == 'author') {
                    url = 'authors';
                } else if(type == 'museum') {
                    url = 'museums';
                } else if(type == 'produceParam') {
                    url = 'produceParams';
                }else if(type == 'taoBaoOrder') {
                    url = 'taoBaoOrder';
                }else if(type == 'taoBaoOrderProduct') {
                    url = 'taoBaoOrderProduct';
                }
                if(isNull(url)) {
                    typeError(type);
                } else {
                    url = path + url + '/import';
                    var formData = filePostData(this.files[0]);
                    var params = {
                        type: type,
                        input: this
                    };
                    ajaxImageData(formData, url, handleUploadFile, params, errorUploadFile);
                }
            }
        });
        function handleUploadFile(result, params) {
//            console.log(result);
            var type = params.type;
            var data =result.data;
            if(!isNull(params) && !isNull(params.input)) {
                params.input.value = '';
            }
            var html = '';
            if(!isNull(data)) {
                var trans = '{!! json_encode($data) !!}';
                trans = JSON.parse(trans);
                html = '<table class="table table-bordered">';
                for(var i in data) {
                    var item = data[i];
                    if(i == 0) {
                        html += '<thead>' +
                            '<tr>';
                        if(!isNull(item.line)) {
                            html += '<th style="min-width: 25px">行号</th>';
                        }
                        for(var j in trans) {
                            html += '<th>'+ trans[j] +'</th>';
                        }
                        html += '</tr>' +
                            '</thead><tbody>';
                    }
                    html += '<tr>';
                    if(!isNull(item.line)) {
                        html += '<td>' + item.line + '</td>';
                    }
                    for(var j in trans) {
                        var value = item[j];
                        if(type == 'publication') {
                            if(!isNull(item.museum)) {
                                if (j == 'museum_name') {
                                    value = item.museum.name;
                                } else if(j == 'museum_lang') {
                                    value = item.museum.lang;
                                }
                            }
                        }
                        if(isNull(value)) {
                            value = ' ';
                        }
                        html += '<td>'+ value +'</td>';
                    }
                    html += '</tr>';
                }
                html += '</tbody></table>';
            }
            if(isOk(result)) {
                if(!isNull(data)) {
                    var length = data.length;
                    bootstrapQ.alert({
                        title: '成功，以下数据已全部导入，共导入 '+ length +' 条数据',
                        msg: html,
                        className: 'modal-lg'
                    });
                } else {
                    bootstrapQ.alert({
                        title: '成功',
                        msg: '数据已全部导入'
                    });
                }
            } else if(result.statusCode == ERRORS.EXIST['code']) {
                if(!isNull(data)) {
                    var title = '导入文件失败，以下数据已存在，请不要重复导入';
                    bootstrapQ.alert({
                        title: title,
                        msg: html,
                        className: 'modal-lg'
                    });
                } else {
                    bootstrapQ.alert('导入文件失败，请不要重复导入');
                }
            } else {
                bootstrapQ.alert({
                    title: '导入文件失败',
                    msg: result.message
                });
            }
            loadingHide();
        }
        function errorUploadFile() {
            bootstrapQ.alert('导入文件失败');
            loadingHide();
        }
        /*$('#test').on('change', function () {
                var formData = filePostData(this.files[0], 'excelTest.xls');
                var path = originPath();
                ajaxImageData(formData, path + 'importPublication', function (result) {
                    console.log(result);
                    bootstrapQ.alert(result);
                }, null, null);
            })*/
        function typeError(type) {
            if(isNull(type)) {
                bootstrapQ.alert('参数错误 Type is null');
            } else {
                bootstrapQ.alert('参数错误 Type is ' + type);
            }
            loadingHide();
        }
    })
</script>