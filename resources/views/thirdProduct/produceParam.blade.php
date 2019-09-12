<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/29/17
 * Time: 4:14 PM
 */

use App\IekModel\Version1_0\Constants\UnqualifiedPart;

$unqualifiedPart = UnqualifiedPart::UNQUALIFIED_PART;
//dd($product);
$product = isset($product) ? $product:null;
//$product = json_encode($product);
//dd(json_decode(json_encode($product)));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>HIYIK</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="/img/favicon.ico" >
    <link rel="stylesheet" href="/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="/css/colorpicker.css" />
    <link rel="stylesheet" href="/css/datepicker.css">
    <link rel="stylesheet" href="/css/uniform.css">
    <link rel="stylesheet" href="/css/select2.css">
    <link rel="stylesheet" href="/css/fullcalendar.css" />
    <link rel="stylesheet" href="/css/matrix-style.css" />
    <link rel="stylesheet" href="/css/matrix-media.css" />
    <link rel="stylesheet" href="/font-awesome/css/font-awesome.css" />
    <link rel="stylesheet" href="/css/jquery.gritter.css" />
</head>
<body style="background: #f4f4f4; height: auto; padding-bottom: 100px">
    <div class="row-fluid" id="container">
        <div class="span12">
            <div class="widget-box collapsible" style="background: #ffffff; margin-bottom: 0;">
                <div class="widget-title">
                    <a href="#collapseProduce" data-toggle="collapse" class="clearfix"><span class="icon"><i class="icon-th"></i></span>
                        <h5>
                            生产管理
                        </h5>
                    </a>
                </div>
                <div class="collapse in" id="collapseProduce">
                    <div class="widget-content clearfix">
                        @if(!isset($product))
                            <h1 style="text-align: center">无效的产品</h1>
                        @endif
                    </div>
                </div>
                @if(isset($product))
                    <div class="widget-title">
                        <a href="#collapseOrder" data-toggle="collapse" class="clearfix"><span class="icon"><i class="icon-th"></i></span>
                            <h5>
                                订单信息
                            </h5>
                        </a>
                    </div>
                    <div class="collapse in" id="collapseOrder">
                        <div class="widget-content clearfix">
                            无
                        </div>
                    </div>
                @endif
                @if(isset($product) && !isset($product->produced) && (!isset($product->checker) || $product->status == \App\IekModel\Version1_0\Constants\RealProductStatus::FAIL))
                <div class="widget-title">
                    <a href="#collapseChecker" data-toggle="collapse" class="clearfix"><span class="icon"><i class="icon-th"></i></span>
                        <h5>
                            质检
                        </h5>
                    </a>
                </div>
                <div class="collapse in" id="collapseChecker" style="font-size: 14px;">
                    <div class="widget-content nopadding clearfix">
                        <form class="form-horizontal span12">
                            <div class="control-group">
                                <label class="control-label">产品是否合格 :</label>
                                <div class="controls" id="check">
                                    <label><input name="check" type="radio" value="pass" data="pass" checked />合格</label>
                                    <label><input name="check" type="radio" value="fail" data="fail" />不合格</label>
                                    <div class="controls-box" id="unqualifiedPart" style="display: none;">
                                        <div class="control-group">
                                            <label class="control-label">不合格处（可多选） :</label>
                                            <div class="controls">
                                                @if(isset($unqualifiedPart))
                                                    <div class="widget-box collapsible" style="margin-top: 0;">
                                                    @foreach($unqualifiedPart as $key => $value)
                                                        <div class="widget-title">
                                                            <a href="#collapse-{{ $key }}" data-toggle="collapse" class="clearfix">
                                                                <h5>{{ \App\IekModel\Version1_0\IekModel::strTrans($key, 'table') }}</h5>
                                                            </a>
                                                        </div>
                                                        <div class="collapse" id="collapse-{{ $key }}" name="{{ $key }}">
                                                            <div class="widget-content clearfix">
                                                                @if(isset($value))
                                                                    @foreach($value as $item)
                                                                        <label>
                                                                            <input name="unqualifiedPart" type="checkbox" value="{{ $item }}">
                                                                            {{ $item }}
                                                                        </label>
                                                                    @endforeach
                                                                @else
                                                                @endif
                                                                    <div>其他: <input class="span11" type="text" placeholder="其他" value=""></div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    </div>
                                                    <div class="other">其他: <input class="span11" type="text" placeholder="其他" value=""></div>
                                                    <div id="unqualifiedResult" style="margin-top: 10px; padding: 10px 0; border-top: 1px solid #ccc">
                                                        <span class="label label-inverse">已选/已填:</span>
                                                        <span class="span11"></span>
                                                    </div>
                                                @else
                                                    数据错误，请联系技术部
                                                @endif
                                                {{--<label><input name="poorQualityPart" type="checkbox" value="" data="框" />框</label>
                                                <label><input name="poorQualityPart" type="checkbox" value="" data="画芯" />画芯 </label>
                                                <label><input name="poorQualityPart" type="checkbox" value="" data="射钉将框打穿" />射钉将框打穿 </label>
                                                <label><input name="poorQualityPart" type="checkbox" value="" data="装裱画芯气泡" />装裱画芯气泡</label>
                                                <label><input name="poorQualityPart" type="checkbox" value="" data="背面绒布粘贴不平整" />背面绒布粘贴不平整</label>
                                                <label>
                                                    <input class="span11" type="text" id="otherPoorQualityPart" placeholder="其他" value="">
                                                </label>--}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">质检员 :</label>
                                <div class="controls">
                                    <input type="text" class="span11" id="checker" value="01">
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" id="submitCheckResult" class="btn btn-success">保存</button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif
                @if(isset($product) && (isset($product->checker) && $product->status == \App\IekModel\Version1_0\Constants\RealProductStatus::PASS) && isset($product->orderRealProduct))
                    <div class="widget-title">
                        <a href="#collapseSend" data-toggle="collapse" class="clearfix"><span class="icon"><i class="icon-th"></i></span>
                            <h5>
                                发货
                            </h5>
                        </a>
                    </div>
                    <div class="collapse in" id="collapseSend" style="font-size: 14px;">
                        <form class="form-horizontal form-block clearfix">
                            <div class="control-group">
                                <label class="control-label"><span class="text-important">*</span>快递公司 :</label>
                                <div class="controls">
                                    <input type="text" class="span11" placeholder="快递公司" name="shipCompany" value="">
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label"><span class="text-important">*</span>快递单号 :</label>
                                <div class="controls">
                                    <input type="text" class="span11" placeholder="快递单号" name="shipNo" value="">
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label"><span class="text-important">*</span>真实产品编号 :</label>
                                <div class="controls">
                                    <input type="text" class="span11" placeholder="所有请填全部；有多个以‘,’间隔开。" name="productNo" value="">
                                </div>
                            </div>
                        </form>
                        <div class="form-actions" style="margin: 0;">
                            <a class="btn btn-success" name="add">添加输入框</a>
                            <a class="btn btn-success" name="save">保存发货信息</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

<script src="/js/jquery.min.js"></script>
<script src="/js/jquery.json.min.js"></script>
<script src="/js/jquery.ui.custom.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script src="/js/bootstrap-datepicker.js"></script>
<script src="/js/bootstrapQ.js"></script>
<!--<script src="/js/jquery.flot.min.js"></script>-->
<!--<script src="/js/jquery.flot.resize.min.js"></script> -->
<script src="/js/jquery.peity.min.js"></script>
<script src="/js/jquery.gritter.min.js"></script>
<script src="/js/jquery.validate.js"></script>
<script src="/js/jquery.wizard.js"></script>
<script src="/js/jquery.uniform.js"></script>
<script src="/js/jquery.dataTables.js"></script>
<script src="/js/jquery.nicescroll.min.js"></script>
<script src="/js/excanvas.min.js"></script>
<script src="/js/fullcalendar.min.js"></script>
<script src="/js/select2.min.js"></script>
<script src="/js/adminCommon.js"></script>
<script src="/js/prototype.js"></script>
<script src="/js/common.js"></script>
<script src="/js/errors.js"></script>
<script src="/js/event.js"></script>
<script src="/ckEditor/ckEditor.js"></script>
<script src="/ckEditor/myEditor.js"></script>
<script src="/js/uploadFile.js"></script>
<script src="/translation?trans=error"></script>
<script src="/translation?trans=admin"></script>
<script src="/translation?trans=table"></script>
<script src="/js/bootstrap-colorpicker.js"></script>
<script src="/js/dataTable.js"></script>
<script src="/js/produceParam.js"></script>

<script>
    $(function () {
        var product = '{!! json_encode($product) !!}';
        if(!isNull(product) && product != 'null'){
            product = JSON.parse(product);
            $('#submitCheckResult').attr('data', product.id);
            var html = createRowInfo(product, originPath() + 'realProducts');
            if (isNull(html)) {
                html = '<h1>没有数据</h1>';
            }
            $('#collapseProduce').find('.widget-content').append(html);
            initPageElement('container');
        }
        $('#check input[type="radio"]').change(function(){
            if($(this).attr('data') == 'fail'){
                $('#unqualifiedPart').show();
            }else {
                $('#unqualifiedPart').hide();
            }
        });
        $('#submitCheckResult').click(function(event){
            submitCheckResult(event,$(this));
        });
        $('#unqualifiedPart input:text').on('input propertychange', function () {
            showUnqualifiedResult();
        })
        $('#unqualifiedPart input:checkbox').on('change', function () {
            showUnqualifiedResult();
        })
        showUnqualifiedResult();
        if(!isNull(product.orderRealProduct) && !isUndefined(product.orderRealProduct)){
            addOrderMsg(product.orderRealProduct.order, product.hiyik_origin_uri);
            $('#collapseSend .btn[name="add"]').on('click', function (event) {
                var $this = $(this);
                var $form = $this.parent().prev('form');
                var formHtml = $form.prop('outerHTML');
                var $newForm = $(formHtml);
                $newForm.find('input').val();
                $this.parent().before($newForm);
            });
            $('#collapseSend .btn[name="save"]').on('click', function (event) {
                var content = $('#collapseSend form');
                var shipParams = [];
                for(var i=0;i<content.length;i++){
                    var param = {};
                    var no = $(content[i]).find('input[name="shipNo"]').val();
                    var company = $(content[i]).find('input[name="shipCompany"]').val();
                    var productNo = $(content[i]).find('input[name="productNo"]').val();
                    if(no.length <1 || company.length <1 || productNo.length < 1){
                        continue;
                    }
                    param.shipNo = no;
                    param.shipCompany = company;
                    param.productNo = productNo;
                    shipParams.push(param);
                }
                var data = {};
                var params = {};
                params.shipMsg = shipParams;
                data.data = params;
                if(shipParams.length == 0) {
                    bootstrapQ.alert('请输入至少一个完整的快递信息再保存噢٩(๑>◡<๑)۶ ');
                } else {
                    bootstrapQ.confirm('你添加了' + content.length + '个快递输入框，只输入了' + shipParams.length + '个完整的快递信息，是否确认保存？', function (params) {
                        ajaxData('post', originPath() + 'orders/' + product.orderRealProduct.order.order_no + '/ships', function (data) {
                            if (data.statusCode != 0) {
                                bootstrapQ.alert('保存失败，请重试！如连续失败，请联系技术人员！');
                            } else {
//                                bootstrapQ.alert('保存成功！');
                                window.location.reload();
                            }
                        }, '', data);
                    });
                }
            })
        }
    });
</script>
</body>
</html>
