<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/10/19
 * Time: 10:46
 */
$shares = $result->data;
$skip  = isset($result->skip) ? $result->skip : 0;
$take  = isset($result->take) ? $result->take : 6;
$params = isset($result->params) ? $result->params : null;
$url = 'share?params='.$params;
$transDataTable = trans('dataTable');

//dd($shares);
?>
<style>
    .btn{
        margin-right: 10px;
    }
    .btn.active{
        background: #afdd22;
        color: white;
    }
</style>
@extends('layout.widget')

@section('title')
    分享记录列表
@stop
@section('content')
    <div class="widget-content">
        <h4>请选择查询条件</h4>
        <form class=" form-horizontal">
            <div class="control-group">
                <label for="checkboxes" class="control-label">分享类型</label>
                <div class="controls">
                    <div data-toggle="buttons-checkbox" name="type" class="btn-group">
                        <button class="btn" type="button" data="person">Person</button>
                        <button class="btn" type="button" data="publication">Publication</button>
                        <button class="btn" type="button" data="iwall">Iwall</button>
                    </div>
                </div>
            </div>
            <div class="control-group">
                <label for="checkboxes" class="control-label">分享平台</label>
                <div class="controls">
                    <div data-toggle="buttons-checkbox" name="platform" class="btn-group">
                        <button class="btn" type="button" data="QQ">QQ</button>
                        <button class="btn" type="button" data="WeChat">WeChat</button>
                        <button class="btn" type="button" data="SinaBlog">SinaBlog</button>
                        <button class="btn" type="button" data="TencentBlog">TencentBlog</button>
                        <button class="btn" type="button" data="weixinweb">WeiXinWeb</button>
                    </div>
                </div>
            </div>
            @include('admin.trades.condition')
            <div >
                <a type="button" class ="btn btn-success" title="查询" id="shareSearch"><i class="icon-white">查询</i></a>
            </div>
        </form>
    </div>
    <div class="widget-content">
        <h4>分享记录列表</h4>
        <div class="data-list clearfix">
            <table class="table table-striped table-bordered table-hover" >
                <thead>
                <tr>
                    <th>ID</th>
                    <th>分享链接</th>
                    <th>分享对象</th>
                    <th>分享类型</th>
                    <th>分享平台</th>
                    <th>分享人</th>
                    <th>分享IP</th>
                    <th>分享时间</th>
                </tr>
                </thead>
                <tbody>
                @if(!$shares->isEmpty())
                    @foreach($shares as $k=> $share)
                    <tr>
                        <td width="5%">{{$k+1}}</td>
                        <td width="35%">{{!is_null($share->share)?$share->share->uri:null}}}</td>
                        <td width="15%">
                            @if($share->share->content_type == 'person')
                                <?php $nicks = $share->share->person->personNick;?>
                                @if(!$nicks->isEmpty())
                                    @foreach($nicks as $nick)
                                        <a href="javascript:void(0);" class="obj-person" data="{{$nick->person_id}}">
                                            {{$nick->is_active == true?$nick->nick->nick:null}}
                                        </a>
                                    @endforeach
                                @endif
                            @endif
                            @if($share->share->content_type == 'publication')
                                <?php $titles = $share->share->publication->publicationTitle;?>
                                @if(!$titles->isEmpty())
                                    @foreach($titles as $title)
                                        <a href="javascript:void(0);" class="obj-publication" data="{{$title->publication_id}}">
                                            {{$title->is_active == true?$title->plainStyle->description->content:null}}
                                        </a>
                                    @endforeach
                                @endif
                            @endif
                            @if($share->share->content_type == 'iwall')
                                <?php $names = $share->share->iwall->iwallTitle;?>
                                @if(!$names->isEmpty())
                                    @foreach($names as $name)
                                        <a href="javascript:void(0);" class="obj-iwall" data="{{$title->iwall_id}}">
                                            {{$name->is_active == true?$name->plainStyle->description->content:null}}
                                        </a>
                                    @endforeach
                                @endif
                            @endif
                        </td>
                        <td width="5%">
                            @if($share->share->content_type == 'person')
                                {{'Person'}}
                            @endif
                            @if($share->share->content_type == 'publication')
                                {{'Publication'}}
                                @endif
                                @if($share->share->content_type == 'iwall')
                                    {{'IWall'}}
                                @endif
                        </td>
                        <td width="5%">{{!is_null($share->platform)?$share->platform->platform:null}}</td>
                        <td width="15%">
                            @if(!is_null($share->person))
                            @if(!$share->person->personNick->isEmpty())
                                @foreach($share->person->personNick as $nick)
                                        <a href="javascript:void(0);" class="share-person" data="{{$nick->person_id}}">
                                            {{$nick->is_active == true?$nick->nick->nick:null}}
                                        </a>
                                @endforeach
                                @endif
                                @else
                                {{'无'}}
                                @endif
                        </td>
                        <td width="10%">{{!is_null($share->ip)?$share->ip->ip:null}}</td>
                        <td width="10%">{{$share->created_at}}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8" style="text-align: center;background-color: #cde69c;font-size: medium;">{{"哎哟哟。。。是不是没查到任何数据啊"}}</td>
                    </tr>
                @endif
                </tbody>
            </table>
            @include('layout/pagination')
            <div>总共：{{$result->total}}条  每页：{{$result->take}}条</div>
        </div>
    </div>
    <div class="widget-content">
        <h4>数据占比统计</h4>
        <div class="center">
            <div style="font-size: 15px;">分享类型统计</div>
            <ul class="stat-boxes2">
                <li style="background-color: #ff522a">
                    <div class="left peity_bar_neutral">
                        <span><canvas width="33" height="33" style='background-image: url("/img/person.png")'></canvas></span>
                        {{$result->total == 0 ? 0 :round(($result->num['personNum']/$result->total)*100).'%'}}
                    </div>
                    <div class="right"> <strong>{{$result->num['personNum']}}</strong> Person </div>
                </li>
                <li style="background-color: #ff981a">
                    <div class="left peity_line_neutral">
                        <span><canvas width="33" height="33"style='background-image: url("/img/publication.png")'></canvas></span>
                        {{$result->total == 0 ? 0 :round(($result->num['publicationNum']/$result->total)*100).'%'}}
                    </div>
                    <div class="right"> <strong>{{$result->num['publicationNum']}}</strong> Publication </div>
                </li>
                <li style="background-color: #fffb0d">
                    <div class="left peity_bar_bad">
                        <span><canvas width="33" height="33"style='background-image: url("/img/iwall.png")'></canvas></span>
                        {{$result->total == 0 ? 0 :round(($result->num['iwallNum']/$result->total)*100).'%'}}
                    </div>
                    <div class="right"> <strong>{{$result->num['iwallNum']}}</strong> IWall </div>
                </li>
            </ul>
            <div style="font-size: 15px;">分享平台统计</div>
            <ul class="stat-boxes2">
                <li style="background-color: #3aff05">
                    <div class="left peity_line_good">
                        <span><canvas width="33" height="33" style='background-image: url("/img/qq.png")'></canvas></span>
                        {{$result->total == 0 ? 0 :round(($result->num['qqNum']/$result->total)*100).'%'}}
                    </div>
                    <div class="right"> <strong>{{$result->num['qqNum']}}</strong> QQ </div>
                </li>
                <li style="background-color: #07fff7">
                    <div class="left peity_bar_good">
                        <span><canvas width="33" height="33" style='background-image: url("/img/weixin.png")'></canvas></span>
                        {{$result->total == 0 ? 0 :round(($result->num['weChatNum']/$result->total)*100).'%'}}
                    </div>
                    <div class="right"> <strong>{{$result->num['weChatNum']}}</strong> WeChat </div>
                </li>
                <li style="background-color: #83afff">
                    <div class="left peity_bar_good">
                        <span><canvas width="33" height="33" style='background-image: url("/img/sina.png")'></canvas></span>
                        {{$result->total == 0 ? 0 :round(($result->num['sinaBlogNum']/$result->total)*100).'%'}}
                    </div>
                    <div class="right"> <strong>{{$result->num['sinaBlogNum']}}</strong> SinaBlog </div>
                </li>
                <li style="background-color: #f759ff">
                    <div class="left peity_bar_good">
                        <span><canvas width="33" height="33" style='background-image: url("/img/tencentblog.png")'></canvas></span>
                        {{$result->total == 0 ? 0 :round(($result->num['tencentBlogNum']/$result->total)*100).'%'}}
                    </div>
                    <div class="right"> <strong>{{$result->num['tencentBlogNum']}}</strong> TencentBlog </div>
                </li>
                <li style="background-color: #dec3ff">
                    <div class="left peity_bar_good">
                        <span><canvas width="33" height="33" style='background-image: url("/img/weixinweb.png")'></canvas></span>
                        {{$result->total == 0 ? 0 :round(($result->num['weixinwebNum']/$result->total)*100).'%'}}
                    </div>
                    <div class="right"> <strong>{{$result->num['weixinwebNum']}}</strong> WeiXinWeb </div>
                </li>
            </ul>
        </div>
    </div>
    {{--<div class="row-fluid">
        <div class="widget-title"> <span class="icon"> <i class="icon-bar-chart"></i> </span>
            <h5>分享统计</h5>
        </div>
        <div class="widget-content">
            <div id="main" style="height:500px">

            </div>
        </div>
    </div>--}}
@stop
<script>
    $('#shareSearch').on('click',function(){
        var btn1 = $('.btn-group[name="type"] .active');
        var type = [];
        btn1.each(function(){
            var param1 = $(this).attr('data');
            type.push(param1);
        });
        var btn2 = $('.btn-group[name="platform"] .active');
        var platform = [];
        btn2.each(function(){
            var param2 = $(this).attr('data');
            platform.push(param2);
        });
        var btn3 = $('.btn-group[name="time"] .active');
        var time = [];
        btn3.each(function(){
            var param3 = $(this).attr('data');
            time.push(param3);
        });
        var startTime = $('#start-time').val();
        var endTime = $('#end-time').val();
        var params = {};
        params.type = type;
        params.platform = platform;
        params.time = time;
        params.startTime = startTime;
        params.endTime = endTime;
        if(params.type.length == 0 && params.platform.length == 0 && params.time.length == 0
        && params.startTime == '' && params.endTime == ''){
            bootstrapQ.alert("请至少选择一个条件！");
            return false;
        }
        params = JSON.stringify(params);
        ajaxData('get','share?take=6'+'&skip=0'+'&params='+params,appendViewToContainer);
    });
    $('.share-person').on('click',function(){
        var id = $(this).attr('data');
        bootstrapQ.dialog({
            type: 'get',
            url: 'persons/'+id,
            title: '用户详情',
            className: 'modal-lg',
            foot: false
        });
    });
    $('.obj-person').on('click',function(){
        var id = $(this).attr('data');
        bootstrapQ.dialog({
            type: 'get',
            url: 'persons/'+id,
            title: '用户详情',
            className: 'modal-lg',
            foot: false
        });
    });
    $('.obj-publication').on('click',function(){
        var id = $(this).attr('data');
        bootstrapQ.dialog({
            type: 'get',
            url: 'publications/'+id,
            title: '作品详情',
            className: 'modal-lg',
            foot: false
        });
    });
    $('.obj-iwall').on('click',function(){
        var id = $(this).attr('data');
        bootstrapQ.dialog({
            type: 'get',
            url: 'iwall/'+id,
            title: 'iwall详情',
            className: 'modal-lg',
            foot: false
        });
    });
</script>