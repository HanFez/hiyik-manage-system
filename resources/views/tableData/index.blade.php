<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 11/9/16
 * Time: 11:20 AM
 */
use App\IekModel\Version1_0\Constants\RealProductStatus;
use App\IekModel\Version1_0\Constants\IntroductionType;
use App\IekModel\Version1_0\IekModel;

$request = request();
$url = $request->getPathInfo();

$originPath = \App\IekModel\Version1_0\Constants\Path::ORIGIN_PATH;

$urls = explode('/', $url);
$dataType = null;
$hasEdit = true;
if(strpos($url, $originPath) === false) {
    $isOrigin = false;
    if(isset($urls[1])) {
        $dataType = $urls[1];
        if($dataType == 'privilege' || $dataType == 'getAll' || $dataType == 'patternParams') {
            $hasEdit = false;
        }
        if(strpos($url, '/relation/') != false) {
            $hasEdit = false;
        }
    }
} else {
    $isOrigin = true;
    if(isset($urls[2])) {
        $dataType = $urls[2];
        if($dataType == 'realProducts') {
            $hasEdit = false;
        }
    }
}
if($isOrigin == true) {
    if($dataType == 'realProducts') {
        $r = new ReflectionClass(RealProductStatus::class);
        $statues = $r -> getConstants();
        $status = $request->input('status');
        $isProduced = $request->input('isProduced');
        if(isset($status)) {
            $url .= 'status='.$status;
        }
        if(isset($isProduced)) {
            if(isset($status)) {
                $url .= '&';
            }
            $url .= 'isProduced='.$isProduced;
        }
    } else if($dataType == 'introductions') {
        $r = new ReflectionClass(IntroductionType::class);
        $introTypes = $r -> getConstants();
        $introType = $request->input('type');
        if(isset($introType)) {
            $url .= 'type='.$introType;
        }
    }
}

//dd($dataType, $isOrigin, strpos($url, $originPath));
$type       = isset($params->type) ? $params->type : null;
//$url        = isset($params->url) ? $params->url : null;
$orderCol = 3;
$nameIndex = null;
$birthdayIndex = null;
$index = array();
foreach ($field as $key => $value) {
    $transValue               = clone $value;
    $columnName               = $value->column_name;
    $transColumnName          = IekModel::doTrans($transValue, 'column_name', 'table');
    $value->column_name_trans = $transColumnName->column_name;
//    $index[] = $transColumnName->column_name;
}
//array_multisort($index, SORT_ASC, $field);
foreach ($field as $key => $value) {
    $columnName = $value->column_name;

    if ($columnName == 'id') {
        $orderCol = (int)($key) + 2;
    }
    if($columnName == 'birthday') {
        $birthdayIndex = (int)($key) + 2;
    }
    if($columnName == 'name' && strpos($url, '/relation/') === false) {
        if($dataType == 'role' || $dataType == 'manager' || $dataType == 'pattern') {
            $nameIndex = (int)($key) + 2;
        }
    }
    /*if($type === 'role' && $columnName == 'name') {
        $nameIndex = (int)($key) + 2;
    }
    if($type === 'manager' && $columnName == 'name') {
        $nameIndex = (int)($key) + 2;
    }
    if($type === 'core' && $columnName == 'name'){
        $nameIndex = (int)($key) + 2;
    }
    if($type === 'pattern' && $columnName == 'name'){
        $nameIndex = (int)($key) + 2;
    }*/
}
//dd($times);
$transTable = trans('dataTable');

$transAdmin  = trans('admin');

$transIsRemoved = IekModel::strTrans('false', 'table');
?>
@extends('layout/list')

@section('title')
    {{ $transAdmin['list'] or 'list' }}
@stop

@section('content-header')
    @if($dataType !== 'getAll')
    <a id="add" onclick="jumpToAddData(this)" class="btn btn-success" type="button" data-type="{{ $type }}">{{ $transAdmin['add'] or 'add' }}</a>
    @endif
    <a id="bulk-delete" onclick="bulkOperation(this)" class="btn btn-danger" type="button" data-url="{{ $url }}" data-type="delete">{{ $transAdmin['bulkDelete'] or 'bulkDelete' }}</a>
    <a id="bulk-recover" onclick="bulkOperation(this)" class="btn btn-warning" type="button" data-url="{{ $url }}" data-type="recover">{{ $transAdmin['bulkRecover'] or 'bulkRecover' }}</a>
    @if($isOrigin == true)
        @if($dataType == 'realProducts')
            <div>
                <select name="" id="real-product-status" style="width: 150px">
                    <option value="all" {{ $status ? '' : 'selected' }}>全部</option>
                    @if(isset($statues))
                        @foreach($statues as $item)
                            <option value="{{ $item }}" {{ $item == $status ? 'selected' : '' }}>
                                {{ \App\IekModel\Version1_0\IekModel::strTrans($item, 'RealProductStatus') }}
                            </option>
                        @endforeach
                    @endif
                </select>
                <a id="bulk-export" download="QR" onclick="bulkExport(this)" class="btn btn-primary" type="button" data-url="{{ $url }}" style="display: none">批量导出二维码</a>
                {{--<select name="" id="real-product-fail" style="width: 150px; display: none">
                    <option value="all" {{ $isProduced ? '' : 'selected' }}>全部</option>
                    <option value="true" {{ $isProduced === 'true' ? 'selected' : '' }}>已经重新生产</option>
                    <option value="false" {{ $isProduced === 'false' ? 'selected' : '' }}>未重新生产</option>
                </select>--}}
                {{--<div id="real-product-fail" style="display: none; margin-top: 5px">
                    <label class="label-inline"><input type="radio" name="isProduced" value="false" {{ $isProduced === 'false' ? 'checked' : '' }}>未重新生产</label>
                    <label class="label-inline"><input type="radio" name="isProduced" value="true" {{ $isProduced === 'true' ? 'checked' : '' }}>已经重新生产</label>
                </div>--}}
                <div id="real-product-fail" style="display: none;" data-toggle="buttons-radio" class="btn-group">
                    <button class="btn btn-primary {{ $isProduced ? '' : 'active' }}" type="button" value="all">全部</button>
                    <button class="btn btn-primary {{ $isProduced === 'false' ? 'active' : '' }}" type="button" value="false">未重新生产</button>
                    <button class="btn btn-primary {{ $isProduced === 'true' ? 'active' : '' }}" type="button" value="true">已重新生产</button>
                </div>
                <a id="create-real-product" download="QR" onclick="createAgainRealProduct(this)" class="btn btn-success" type="button" data-url="{{ $url }}" style="display: none">重新添加生产并导出二维码</a>
            </div>
        @elseif($dataType == 'introductions')
            <div>
                <select name="" id="introduction-type" style="width: 150px">
                    <option value="all" {{ $introType ? '' : 'selected' }}>全部</option>
                    @if(isset($introTypes))
                        @foreach($introTypes as $item)
                            <option value="{{ $item }}" {{ $item == $introType ? 'selected' : '' }}>
                                {{ \App\IekModel\Version1_0\IekModel::strTrans($item, 'IntroductionType') }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
        @endif
    @endif
@stop

@section('thead')
    <tr>
        <th></th>
        <th></th>
        @for($i=0; $i<count($field); $i++)
            <th>{{--{{trans('employee.'.$field[$i])}}--}}</th>
        @endfor
        <th></th>
    </tr>
@stop

@section('footer')
    <script>
//        $.fn.dataTable.ext.errMode = 'throw';
        $.fn.dataTable.ext.errMode = function( settings, tn, msg){
            //打印msg，和tn来判断，进了这个方法都是ajax走了error才会到这里来
            console.log(msg);
            bootstrapQ.alert({
                title: '错误',
                msg: msg
            });
//            console.log(msg, tn, settings);
        };
        var _myTable = $('#myTable');
        var oTable = _myTable.DataTable({
            "processing": true, //DataTables载入数据时，是否显示‘进度’提示
            "serverSide": true, //是否启动服务器端数据导入
            "autoWidth": false, //自动计算宽度
            "info": true,//页脚信息
            "jQueryUI": true,
            "searching": false,
            "lengthMenu": [10, 20, 40, 60, 80, 100], //更改显示记录数选项
            "pagingType": "full_numbers",
            "pageLength": 10, //默认显示的记录数
            "lengthChange": true,// 每行显示记录数
            "ordering": true, //是否启动各个字段的排序功能
            "orderClasses": false,
            "order": [parseInt('{{ $orderCol }}'), "asc"],
            "language": {//国际化配置
                "loadingRecords": "{{ $transTable['loadingRecords'] or 'loadingRecords' }}",
                "processing": "{{ $transTable['processing'] or 'processing' }}",
                "lengthMenu": "{{ $transTable['lengthMenu'] or 'lengthMenu' }}",
                "zeroRecords": "{{ $transTable['zeroRecords'] or 'zeroRecords' }}",
                "info": "{{ $transTable['info'] or 'info' }}",
                "infoEmpty": "{{ $transTable['infoEmpty'] or 'infoEmpty' }}",
                "infoFiltered": "{{ $transTable['infoFiltered'] or 'infoFiltered' }}",
                "infoPostFix": "",
                "search": "{{ $transTable['search'] or 'search' }}",
                "url": "",
                "paginate": {
                    "first": "{{ $transTable['first'] or 'first' }}",
                    "previous": "{{ $transTable['previous'] or 'previous' }}",
                    "next": "{{ $transTable['next'] or 'next' }}",
                    "last": "{{ $transTable['last'] or 'last' }}"
                },
                "decimal": ".",
                "thousands": ","
            },
            "columns": [ //定义列数据来源
                {'title': "<input type='checkbox' value='all' name='all'>", 'data': null, 'sortable': false, 'class': 'center'},
                {'title': "行号", 'data': null, 'sortable': false, 'class': "center"},
                    @foreach($field as $column)
                {
                    'title': '{{ $column -> column_name_trans }}', 'data': '{{ $column -> column_name }}'
                    @if(stripos($column -> data_type, 'time') !== false && $column -> column_name !== 'birthday')
                    , 'class': 'time-utc'
                    @endif
                    @if($url == $originPath.'authors')
                        @if($column -> column_name == 'description' ||
                            $column -> column_name == 'feature' ||
                            $column -> column_name == 'nationality' ||
                            $column -> column_name == 'saying' ||
                            $column -> column_name == 'introduction')
                    , "visible": false
                        @endif
                    @endif
                },
                    @endforeach
                {
                    'title': '{{ IekModel::strTrans('action', 'table') }}', 'data': null, 'sortable': false, 'class': 'center button-groups'
                } // 自定义列
            ],
            "columnDefs": [ //自定义列
                {
                    "targets": 0,
                    "data": null,
                    "render": function (data, type, row) {
                        var html = "<input type='checkbox' value='" + row.id + "'>";
                        return html;
                    }
                },
                {
                    "targets": 1,
                    "data": null
                },
                    @if(!is_null($nameIndex))
                {
                    "targets": parseInt('{{ $nameIndex or 1 }}'),
                    "data": 'name',
                    "render": function (data, type, row) {
                        return '<a href="javascript:void(0)" class="btn-show-relation">'+row.name+'</a>';/* onclick="showRelation(this,\''+ ttype +'\')"*/
                    }
                },
                    @endif
                    @if(!is_null($birthdayIndex))
                {
                    "targets": parseInt('{{ $birthdayIndex or 1 }}'),
                    "data": 'birthday',
                    "render": function (data, type, row) {
                        return row.birthday.split(" ")[0];
                    }
                },
                    @endif
                {
                    "targets": -1, //改写哪一列
                    "data": null,
                    "render": function (data, type, row) {
                        var html = '';
                        html += '<button class="btn btn-success btn-show-info">详情</button>';
                        if(isUndefined(row.is_modify) || row.is_modify == false) {
                            @if($hasEdit == true)
                                    {{--html += '<a href="#" class="tip-top" onclick="editData(this)" data="'+row.id+'" data-type="{{ $type }}" data-url = {{ $url }} data-original-title="{{ $transAdmin['edit'] or 'edit' }}"><i class="icon-pencil"></i></a>';--}}
                                    html += '<button class="btn btn-info btn-modify">{{ $transAdmin['edit'] or 'edit' }}</button>';
                            {{--@endif--}}
                            @endif
                            if (row.is_removed == false) {
                                {{--html += '<a href="#" class="tip-top" onclick="delData(this)" data="'+row.id+'" data-url = {{ $url }} data-original-title="{{ $transAdmin['delete'] or 'delete' }}"><i class="icon-remove"></i></a>';--}}
                                html += '<button class="btn btn-danger btn-delete">{{ $transAdmin['delete'] or 'delete' }}</button>';
                            } else {
                                {{--html += '<a href="#" class="tip-top" onclick="recoverData(this)" data="'+row.id+'" data-url = {{ $url }} data-original-title="{{ $transAdmin['recover'] or 'recover' }}"><i class="icon-undo"></i></a>';--}}
                                html += '<button class="btn btn-warning btn-recover">{{ $transAdmin['recover'] or 'recover' }}</button>';
                            }
                        }
                        return html;
                    }
                }
            ],
//            "dom": '<""l>t<"F"fip>',
            "buttons": [
                {
                    "extend": 'copy',
                    "text": 'Copy current page',
                    "exportOptions": {
                        "modifier": {
                            "page": 'current'
                        }
                    }
                }
            ],
            "ajax": {
                "url": "{{ $url }}",
                // "type": "post",
                "dataSrc": function (result) {
                    var data = result.data;
                    for(var i in data) {
                        var uri = data[i].uri;
                        if(!isNull(uri) && '{{ json_encode($isOrigin) }}' == 'true' &&
                            ('{{ $dataType }}' == 'realProducts' || '{{ $dataType }}' == 'shops')) {
                            data[i].uri = '<a href="' + uri + '" target="_blank">' + uri + '</a>';
                        }
                        if('{{ json_encode($isOrigin) }}' == 'true' && '{{ $dataType }}' == 'realProducts') {
                            if(!isNull(data[i].detail)) {
                                var detail = data[i].detail;
                                var content = [];
                                for(var j in data[i].detail) {
                                    if(!isNull(data[i].detail[j]) && data[i].detail[j].length > 0) {
                                        for(var k in data[i].detail[j]) {
                                            content.push(data[i].detail[j][k]);
                                        }
                                    }
                                }
                                data[i].detail = content;
                                data[i].detail['detail'] = detail;
                            }
//                            console.log(data[i].detail);
                        }
                    }
                    var total = result.total;
                    if(!isNull(total)) {
                        result.recordsTotal = total;
                        result.recordsFiltered = total;
                    }
                    return result.data;
                }
            }
        });


//        var info = oTable.page.info();
//        console.log(info);

        //Bind click event to buttons
        _myTable.find('tbody').on('click', '.btn', function (event) {
            eventUtil.preventDefault(event);
            var $this = $(this);
            var data = oTable.row($(this).parent()).data();
            if($this.hasClass('btn-show-info')) {
                showRowInfo($this, data, '{{ $url }}', oTable);
            } else if($this.hasClass('btn-modify')) {
                showRowModifyView($this, data, '{{ $url }}', oTable);
            } else if($this.hasClass('btn-delete')) {
                deleteRowEvent($this, data, '{{ $url }}', oTable);
            } else if($this.hasClass('btn-recover')) {
                recoverRowEvent($this, data, '{{ $url }}', oTable);
            }
        });
        _myTable.find('tbody').on('click', '.btn-show-relation', function (event) {
            eventUtil.preventDefault(event);
            var $this = $(this);
            var data = oTable.row($(this).parent()).data();
            showRowRelation($this, data, '{{ $url }}', oTable);
        });

        //Ajax Response status.
        oTable.on('xhr.dt',function (e, settings, json, xhr) {
//            console.log(xhr)
            if (xhr.status == 200) {
                /*messageAlert({
                    message: json.data.length +' 行加载成功',
                    type: 'success'
                })*/
            } else {
                messageAlert({
                    message: 'Ajax请求错误',
                    type: 'error'
                })
            }
        });


        //添加序号
        //不管是排序，还是分页，还是搜索最后都会重画，这里监听draw事件即可
        oTable.on('draw.dt', function () {
            oTable.column(1, {
                search: 'applied',
                order: 'applied'
            }).nodes().each(function (cell, i) {
                //i 从0开始，所以这里先加1
                i = i + 1;
                //服务器模式下获取分页信息，使用 DT 提供的 API 直接获取分页信息
                var page = oTable.page.info();
                //当前第几页，从0开始
                var pageNo = page.page;
                //每页数据
                var length = page.length;
                //行号等于 页数*每页数据长度+行号
                var columnIndex = (i + pageNo * length);
                cell.innerHTML = columnIndex;
            });
            // === Tooltips === //
            $('tbody .tip').tooltip();
            $('tbody .tip-left').tooltip({placement: 'left'});
            $('tbody .tip-right').tooltip({placement: 'right'});
            $('tbody .tip-top').tooltip({placement: 'top'});
            $('tbody .tip-bottom').tooltip({placement: 'bottom'});
            $('tbody input[type="checkbox"],tbody input[type="radio"],tbody input[type="file"]').uniform();
            $('tbody input:checkbox').on('click', function () {
                var checkedStatus = this.checked;
                var tr = $(this).parent().parent().parent().parent();
                var thCheckbox = $(this).parents('.widget-box').find('tr th:first-child input:checkbox');
                if(checkedStatus == true) {
                    tr.addClass('selected');
                } else {
                    tr.removeClass('selected');
                }
                var checkbox = $(this).parents('.widget-box').find('tr td:first-child input:checkbox');
                var flag = true;
                checkbox.each(function() {
                    if (!this.checked) {
                        flag = false;
                    }
                });
                if(!flag) {
                    thCheckbox.parent().removeClass('checked');
                    thCheckbox.prop('checked', false);
                } else {
                    thCheckbox.parent().addClass('checked');
                    thCheckbox.prop('checked', 'checked');
                }
            })
            $('thead .checker').on('click', function () {
                if($(this).find('span').hasClass('checked')) {
                    $('tbody tr').addClass('selected');
                } else {
                    $('tbody tr').removeClass('selected');
                }
            })
            convertUtcTimeToLocalTime('myTable tbody', true);
        });
        $('#real-product-status').on('change', function (event) {
            var $this = $(this);
            var val = event.val;
//            console.log(val);
            if(val == '{{ \App\IekModel\Version1_0\Constants\RealProductStatus::WAIT_PRODUCT }}') {
                $this.siblings('#bulk-export').show();
            } else {
                $this.siblings('#bulk-export').hide();
            }
            var isProduced = $('#real-product-fail .btn.active').val();
            if(val == '{{ \App\IekModel\Version1_0\Constants\RealProductStatus::FAIL }}') {
                $this.siblings('#real-product-fail').show();
                if(isProduced != 'true') {
                    $this.siblings('#create-real-product').show();
                }
            } else {
                $this.siblings('#real-product-fail').hide();
                $this.siblings('#create-real-product').hide();
            }
            var path = originPath();
            var url = path + 'realProducts';
            if(val != 'all') {
                url += '?status=' + val;
            }
            if(isProduced != 'all') {
                if(!isNull(val) && val != 'all') {
                    url += '&';
                } else {
                    url += '?';
                }
                url += 'isProduced=' + isProduced;
            }
            oTable.ajax.url(url).load();
        })
        $('#real-product-fail .btn').on('click', function (event) {
            var $this = $(this);
            var val = $this.val();
            var path = originPath();
            var url = path + 'realProducts';
            var status = $('#real-product-status').select2('val');
            if(!isNull(status)) {
                if(status != 'all') {
                    url += '?status=' + status;
                }
            }
            if(val != 'all') {
                if(!isNull(status) && status != 'all') {
                    url += '&';
                } else {
                    url += '?';
                }
                url += 'isProduced=' + val;
            }
            if(val == 'true') {
                $this.parent().siblings('#create-real-product').hide();
            } else {
                $this.parent().siblings('#create-real-product').show();
            }
            oTable.ajax.url(url).load();
        })
        $('#introduction-type').on('change', function (event) {
            var $this = $(this);
            var val = event.val;
            var path = originPath();
            var url = path + 'introductions';
            if(val != 'all') {
                url += '?type=' + val;
            }
            oTable.ajax.url(url).load();
        })
    </script>
    <script src="js/dataTable.js"></script>
@stop