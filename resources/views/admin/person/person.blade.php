<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 10/31/16
 * Time: 5:26 PM
 */
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Constants\Gag;
use App\IekModel\Version1_0\Constants\GagInterval;
use App\IekModel\Version1_0\Constants\Path;
$gagTypes = Gag::getConstants();
$gagIntervals = GagInterval::getConstants();

$path = Path::FILE_PATH;
if($person->statusCode == 0){
    $personMsg = $person->data;
}
$reasons = $personMsg->reasons;
$isGag = false;
//dd(json_decode(json_encode($personMsg)));
$name = null;
if (isset($personMsg->name) && !is_null($personMsg->name)) {
    $name = $personMsg->name->name;
}
?>
@if(isset($personMsg))
    <div class="alert alert-error alert-block alert-right">
        <a class="close" data-dismiss="alert" href="#">×</a>
        <h4 class="alert-heading">用户状态</h4>
        @if($personMsg->is_forbidden)
            <span class="label label-danger">已被禁止</span>
        @else
            <span class="label label-inverse">未被禁止</span>
        @endif
        @if($isGag)
            <span class="label label-warning">已被禁言</span>
        @else
            <span class="label label-inverse">未被禁言</span>
        @endif
    </div>
    <div class="dialog-title">
        昵称：
        @if($personMsg != null)
            @if($personMsg->nick != null && $personMsg->nick->nick != null)
                <span name="forbidden-content">{{ $personMsg->nick->nick->nick }}</span>
                <button class="btn btn-danger" type="nick" data-type="{{ $personMsg->nick->nick->is_forbidden ? 'unForbidden' : 'forbidden'}}"
                        data="{{$personMsg->nick->nick_id}}">
                    {{ $personMsg->nick->nick->is_forbidden ? '取消禁止' : '禁止'}}</button>
                @if($personMsg->nick->nick->is_forbidden == true)
                        <a href="javascript:void(0);" class="seeReason group-left" data="seeNick/{{$personMsg->nick->nick->id}}" >查看原因</a>
                @endif
            @endif
        @endif
    </div>
    @if($personMsg->count != null)
        <div class="dialog-count">
            <span>喜欢：{{$personMsg->count->like_total or 0}}</span>
            <span>查看：{{$personMsg->count->view_total or 0}}</span>
            <span>评论：{{$personMsg->count->comment_total or 0}}</span>
            <span>特别推荐作品：{{$personMsg->count->official_publication_count or 0}}</span>
            <span>特别推荐Iwall：{{$personMsg->count->official_iwall_count or 0}}</span>
            <span>关注：{{$personMsg->count->fan_count or 0}}</span>
            <span>粉丝：{{$personMsg->count->follow_count or 0}}</span>
        </div>
        <div class="dialog-count">
            <span>喜欢平均数：{{$personMsg->count->like_average or 0}}</span><br>
            <span>查看平均数：{{$personMsg->count->view_average or 0}}</span><br>
            <span>评论平均数：{{$personMsg->count->comment_average or 0}}</span>
        </div>
    @endif
    <div class="dialog-header">
    </div>
    <div class="dialog-tags">
        擅长：
        @if(!is_null($personMsg->personFamiliar) && count($personMsg->personFamiliar) !== 0)
            <?php
                $officialFamiliarCount = 0;
                $familiarCount = 0;
            ?>
            @foreach($personMsg->personFamiliar as $familiar)
                @if(!is_null($familiar->familiar) && $familiar->is_active === true)
                    <?php $familiarCount ++;?>
                    @if($familiar->familiar->is_official === true)
                        <a>{{ IekModel::strTrans($familiar->familiar->name, 'Tag') }}</a>
                        <?php $officialFamiliarCount ++;?>
                    @endif
                @endif
            @endforeach
            @if($officialFamiliarCount === 0)
                未填
            @endif
        @else
            未填
        @endif
    </div>
    <div class="dialog-tags">
        自定义擅长：
        @if(!is_null($personMsg->personFamiliar) && count($personMsg->personFamiliar) !== 0 && $officialFamiliarCount !== $familiarCount)
            <ul>
                @foreach($personMsg->personFamiliar as $familiar)
                    @if(!is_null($familiar->familiar) && $familiar->is_active === true && $familiar->familiar->is_official === false)
                        <li>
                            <span name="forbidden-content" class="badge {{$familiar->familiar->is_forbidden ? '' : 'badge-info'}}">
                                {{IekModel::strTrans($familiar->familiar->name, 'Tag') }}
                            </span>
                            <button class="btn btn-danger btn-mini group-left" type="tag"
                                    data-type="{{ $familiar->familiar->is_forbidden ? 'unForbidden' : 'forbidden'}}" data="{{ $familiar->tag_id  }}">
                                {{$familiar->familiar->is_forbidden ? '取消禁止' : '禁止'}}
                            </button>
                            @if($familiar->familiar->is_forbidden == true)
                                <a href="javascript:void(0);" class="seeReason group-left" data="seeTag/{{$familiar->familiar->id}}" >查看原因</a>
                            @endif
                        </li>
                    @endif
                @endforeach
            </ul>
        @else
            未填
        @endif
    </div>
    <div class="dialog-tags">
        喜欢：
        @if(!is_null($personMsg->personFavor) && count($personMsg->personFavor) !== 0)
            <?php
                $officialFavorCount = 0;
                $favorCount = 0;
            ?>
            @foreach($personMsg->personFavor as $favor)
                @if(isset($favor->favor->name) && $favor->is_active === true)
                    <?php $favorCount ++;?>
                    @if($favor->favor->is_official === true)
                        <a>{{IekModel::strTrans($favor->favor->name, 'Tag')}}</a>
                        <?php $officialFavorCount ++;?>
                    @endif
                @endif
            @endforeach
            @if($officialFavorCount === 0)
                未填
            @endif
        @else
            未填
        @endif
    </div>
    <div class="dialog-tags">
        自定义喜欢：
        @if(!is_null($personMsg->personFavor) && count($personMsg->personFavor) !== 0 && $officialFavorCount !== $favorCount)
            <ul>
                @foreach($personMsg->personFavor as $favor)
                    @if(isset($favor->favor->name) && $favor->is_active === true && $favor->favor->is_official === false)
                        <li>
                            <span name="forbidden-content" class="badge {{$favor->favor->is_forbidden ? '' : 'badge-info'}}">
                                {{IekModel::strTrans($favor->favor->name, 'Tag')}}
                            </span>
                            <button class="btn btn-danger btn-mini group-left" type="tag"
                                    data-type="{{$favor->favor->is_forbidden ? 'unForbidden' : 'forbidden'}}" data="{{ $favor->tag_id }}">
                                {{$favor->favor->is_forbidden ? '取消禁止' : '禁止'}}
                            </button>
                            @if($favor->favor->is_forbidden == true)
                                <a href="javascript:void(0);" class="seeReason group-left" data="seeTag/{{$favor->favor->id}}" >查看原因</a>
                            @endif
                        </li>
                    @endif
                @endforeach
            </ul>
        @else
            未填
        @endif
    </div>
    <div class="dialog-content">
        <div class="group margin-bottom">
            <div class="group-left">账号：</div>
            <div class="group-right">
                @if($personMsg->personAccount != null)
                    {{ $personMsg->personAccount->account_id }}
                @endif
            </div>
        </div>
        <div class="group margin-bottom">
            <div class="group-left">姓名：</div>
            <div class="group-right">
                @if(isset($name) && !is_null($name))
                    <span name="forbidden-content">{{$name->first_name}} {{$name->middle_name}} {{$name->last_name}}</span>
                @else
                    未填
                @endif
            </div>
        </div>
        <div class="group margin-bottom">
            <div class="group-left">性别：</div>
            <div class="group-right">
                @if(!is_null($personMsg->gender))
                    {{ IekModel::strTrans($personMsg->gender->name, 'Gender') }}
                @else
                    未填
                @endif
            </div>
        </div>
        <div class="group margin-bottom">
            <div class="group-left">头像：</div>
            <div class="group-right">
                @if($personMsg->avatar != null && $personMsg->avatar->avatar != null)
                    <img src="{{$path.$personMsg->avatar->avatar->norms[4]->uri}}" alt="    " />
                    <button class="btn btn-danger" type="avatar" data-type="{{ $personMsg->avatar->avatar->is_forbidden ? 'unForbidden' : 'forbidden'}}"
                            data="{{$personMsg->avatar->image_id}}">
                        {{ $personMsg->avatar->avatar->is_forbidden ? '取消禁止' : '禁止'}}
                    </button>
                    @if($personMsg->avatar->avatar->is_forbidden == true)
                        <a href="javascript:void(0);" class="seeReason" data="seeAvatar/{{$personMsg->avatar->avatar->id}}" style="vertical-align: top;">查看原因</a>
                    @endif
                @else
                    {{'未上传头像'}}
                @endif
            </div>
        </div>
        <div class="group margin-bottom">
            <div class="group-left">邮箱：</div>
            <div class="group-right">
                @if($personMsg->mail != null)
                    @if($personMsg->mail->mail != null && $personMsg->mail->domain != null)
                        {{$personMsg->mail->mail->mail."@".$personMsg->mail->domain->domain}}
                    @endif
                @else
                    未填
                @endif
            </div>
        </div>
        <div class="group margin-bottom">
            <div class="group-left">电话：</div>
            <div class="group-right">
                @if($personMsg->phone != null && $personMsg->phone->phone != null)
                    {{$personMsg->phone->phone->phone}}
                @else
                    未填
                @endif
            </div>
        </div>
        <div class="group margin-bottom">
            <div class="group-left">签名：</div>
            <div class="group-right">
                @if($personMsg->signature != null)
                    @if($personMsg->signature->signature != null)
                        <span name="forbidden-content">
                            {{$personMsg->signature->signature->signature}}
                        </span>
                        <button class="btn btn-danger" type="signature" data-type="{{ $personMsg->signature->signature->is_forbidden ? 'unForbidden' : 'forbidden'}}"
                                data="{{$personMsg->signature->signature->id}}">
                            {{ $personMsg->signature->signature->is_forbidden ? '取消禁止' : '禁止'}}
                        </button>
                        @if($personMsg->signature->signature->is_forbidden == true)
                            <a href="javascript:void(0);" class="seeReason" data="seeSignature/{{$personMsg->signature->signature_id}}">查看原因</a>
                        @endif
                    @endif
                @else
                    未填
                @endif
            </div>
        </div>
        <div class="group margin-bottom">
            <div class="group-left">钱包总资产：</div>
            <div class="group-right">
                {{ '￥'.$personMsg->wealth }}
                @if(is_null($personMsg->wallet) || $personMsg->wallet->is_freeze == false)
                    <a href="javascript:void(0);" class="btn btn-danger" name="wallet" data="{{$personMsg->id}}" data-title="close">冻结钱包</a>
                @elseif($personMsg->wallet->is_freeze == true)
                    <a href="javascript:void(0);" class="btn btn-danger" name="wallet" data="{{$personMsg->id}}" data-title="open"> 解冻钱包</a>
                @endif
            </div>
        </div>
        @if(!is_null($personMsg->personGag) && count($personMsg->personGag) > 0)
        <div class="group margin-bottom">
            <div class="group-left">禁言记录：</div>
            <div class="group-right">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>禁言开始时间</th>
                            <th>禁言时间</th>
                            <th>是否永久禁言</th>
                            <th>禁言类型</th>
                            <th>操作人账号</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($personMsg->personGag as $value)
                        <?php
                            $now = date('Y-m-d H:i:s',time());
                            $end = date('Y-m-d H:i:s', strtotime($value->begin_at." + ".$value->expired." minute"));
                            $time = strtotime($now) - strtotime($end);
                            if($time < 0 && $value->is_forever === false) {
                                $gag = $value;
                            } else if($value->is_forever === true) {
                                $forever = $value;
                            }else{
                                $isGag = false;
                            }
                        ?>
                        <tr>
                            <td data-time="utc">{{ $value->begin_at }}</td>
                            <td>{{ $value->expired / 24 / 60}}天</td>
                            <td>{{ $value->is_forever === true ? '是' : '否'}}</td>
                            <td>{{ IekModel::strTrans(strtolower($value->type), 'gag') }}</td>
                            <td>{{ $value->operator_id }}</td>
                        </tr>
                    @endforeach
                    <?php
                        if(isset($forever) && isset($gag)) {
                            $gag = $forever;
                        } else if(isset($forever) || isset($gag)) {
                            $isGag = true;
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        @if(!$personMsg->manageLog->isEmpty())
        <div class="group margin-bottom">
            <div class="group-left">封号记录：</div>
            <div class="group-right">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>操作时间</th>
                            <th>账号状态</th>
                            <th>封号（解封）原因</th>
                            <th>操作人账号</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($personMsg->manageLog as $log)
                        <?php $contents = json_decode($log->content)?>
                        <tr>
                            <td data-time="utc">{{$log->created_at}}</td>
                            @if(!is_null($contents))
                                @foreach($contents as $content)
                                    <td>{{$content->is_removed == true ? '账号已停用':'正常使用'}}</td>
                                    <td>{{$content->is_removed == true ? $log->reason->reason.'（封号）' : $log->reason->reason.'（解封）'}}</td>
                                @endforeach
                            @else
                                <td>{{"内部测试"}}</td>
                                <td>{{$log->reason->reason}}</td>
                            @endif
                            <td>{{$log->operator_id or Null}}-{{!is_null($log->operator)?$log->operator->name: Null}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
    <div class="dialog-footer">
        <button class="btn btn-danger" type="person" data-type="{{ $personMsg->is_forbidden ? 'unForbidden' : 'forbidden'}}" data="{{$personMsg->id}}">
            {{ $personMsg->is_forbidden ? '取消封号' : '封号'}}</button>
        <button class="btn btn-warning" type="gag" data-type="{{ $isGag === false ? 'unGag' : 'gag'}}" data="{{$personMsg->id}}">
            {{ $isGag === false ? '禁言' : '取消禁言'}}</button>
    </div>

    @extends('layout/reason')
    @section('content')
        <div class="control-group gag hide">
            <label class="control-label">时间：</label>
            <div class="controls">
                <select name="interval">
                    @foreach($gagIntervals as $key=>$val)
                        <?php
                            if(isset($gag) && $gag->expired === $val) {
                                $checked = true;
                            } else {
                                $checked = false;
                            }
                        ?>
                        <option value="{{ $val }}" {{ $checked === true ? 'selected' : ''}}>{{ IekModel::strTrans(strtolower($key), 'gag') }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="control-group gag hide">
            <label class="control-label">类型：</label>
            <div class="controls">
                <select name="type">
                    @foreach($gagTypes as $key=>$val)
                        <?php
                            if(isset($gag) && $gag->type === $val) {
                                $checked = true;
                            } else {
                                $checked = false;
                            }
                        ?>
                        <option value="{{ $val }}" {{ $checked === true ? 'selected' : ''}}>{{ IekModel::strTrans(strtolower($key), 'gag') }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    @stop

@endif
<script>
    $('.seeReason').on('click',function(){
        var url = $(this).attr('data');
        bootstrapQ.dialog({
            type: 'get',
            url: url,
            title: '被禁原因',
            //foot:false
        });
    });
    $('a[name="wallet"]').on('click',function(){
        var id = $(this).attr('data');
        var action = $(this).attr('data-title');
        var param = {};
        param.data = {};
        param.data.action = action;
        ajaxData('post','person/'+id+'/wallet',function (result) {
            if(!isNull(result)) {
                $('#container').append(result);
            }
        }, [], param)
    });
</script>