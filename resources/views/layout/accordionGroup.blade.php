<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 11/18/16
 * Time: 4:04 PM
 */
use App\IekModel\Version1_0\IekModel;
?>

@if($result->statusCode == 0)
    <div class="alert alert-error alert-block alert-right">
        <a class="close" data-dismiss="alert" href="#">×</a>
        <h4 class="alert-heading">温馨提示!</h4>
        添加或者修改此页信息时,请填写 <span style="font-size: 14px; font-weight: bold">英文</span>！
    </div>
    <button id="add-setting" class="btn btn-success" style="margin-top: 15px">添加父元素</button>
    <div class="accordion" id="system-group">
        @foreach($system as $i=>$setting)
            <div class="accordion-group widget-box">
                <div class="accordion-heading">
                    <div class="widget-title">
                        <a data-parent="#system-group" href="#setting-{{$setting->id}}" data-toggle="collapse">
                            <span class="icon"><i class="icon-cogs"></i></span>
                            <h5>{{ IekModel::strTrans($setting->name, $transFile) }}</h5>
                        </a>
                    </div>
                </div>
                <div class="collapse accordion-body {{ $i == 0 ? 'in' : ''}}" id="setting-{{$setting->id}}">
                    <div class="widget-content dataTables_wrapper clearfix">
                        <table class="table table-bordered data-table">
                            <thead>
                            <tr>
                                <?php $setting = json_decode(json_encode($setting)) ?>
                                @foreach($setting as $name=> $value)
                                    @if($name != 'content')
                                        <th data-value="{{ $name }}">{{ IekModel::strTrans($name, 'table') }}</th>
                                    @endif
                                @endforeach
                                <th data-value="action">{{ IekModel::strTrans('action', 'table') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                @foreach($setting as $name=> $value)
                                    @if($name != 'content')
                                        <?php
                                        if($value === true) {
                                            $dataValue = 'true';
                                        } else if($value === false) {
                                            $dataValue = 'false';
                                        } else {
                                            $dataValue = $value;
                                        }
                                        ?>
                                        <td data-value="{{ $dataValue  }}" {{ ($name === 'created_at' || $name === 'updated_at') ? 'data-time=utc' : '' }}>
                                            @if($value === true)
                                                true
                                            @elseif($value === false)
                                                false
                                            @elseif($name === 'name')
                                                {{ IekModel::strTrans($value, $transFile) }}
                                            @else
                                                {{ $value }}
                                            @endif
                                        </td>
                                    @endif
                                @endforeach
                                <td class="text-right button-groups">
                                    <a href="#" class="tip-top" data="{{ $setting->id }}" data-type="modify" data-original-title="修改"><i class="icon-pencil"></i></a>
                                    {{--<button class="btn btn-info" data="{{ $setting->id }}" data-type="modify">Modify</button>&nbsp;--}}
                                    @if($setting->is_removed === false)
                                        <a href="#" class="tip-top" data="{{ $setting->id }}" data-type="delete" data-original-title="删除"><i class="icon-remove"></i></a>
                                        {{--<button class="btn btn-danger" data="{{ $setting->id }}" data-type="delete">Delete</button>--}}
                                    @else
                                        <a href="#" class="tip-top" data="{{ $setting->id }}" data-type="recover" data-original-title="恢复"><i class="icon-undo"></i></a>
                                        {{--<button class="btn btn-warning" data="{{ $setting->id }}" data-type="recover">Recover</button>--}}
                                    @endif
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <p>子元素:</p>
                        @if(count($setting -> content) > 0)
                        <table class="table table-bordered data-table">
                            <thead>
                            <tr>
                                <?php $columns = [] ?>
                                @foreach($setting->content[0] as $name=> $value)
                                    <th data-value="{{ $name }}">{{ IekModel::strTrans($name, 'table') }}</th>
                                    <?php array_push($columns, $name) ?>
                                @endforeach
                                    <th data-value="action">{{ IekModel::strTrans('action', 'table') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($setting->content as $index=> $arr)
                                <tr>
                                    @foreach($columns as $column)
                                        @foreach($arr as $name=>$value)
                                            @if($name == $column)
                                                <?php
                                                if($value === true) {
                                                    $dataValue = 'true';
                                                } else if($value === false) {
                                                    $dataValue = 'false';
                                                } else {
                                                    $dataValue = $value;
                                                }
                                                ?>
                                                <td data-value="{{ $dataValue }}" {{ ($name === 'created_at' || $name === 'updated_at') ? 'data-time=utc' : '' }}>
                                                    @if(gettype($value) == 'object' || gettype($value) == 'array')
                                                        object
                                                    @else
                                                        @if($value === true)
                                                            true
                                                        @elseif($value === false)
                                                            false
                                                        @elseif($name === 'name')
                                                            {{ IekModel::strTrans($value, $transFile) }}
                                                        @else
                                                            {{ $value }}
                                                        @endif
                                                    @endif
                                                </td>
                                            @endif
                                        @endforeach
                                    @endforeach
                                    <td class="text-right button-groups">
                                        <a href="#" class="tip-top" data="{{ $setting->id }}" data-id="{{ $type !== 'settings' ? $arr->id : '' }}" data-index="{{ $index }}" data-type="modify" data-original-title="修改"><i class="icon-pencil"></i></a>
                                        {{--<button class="btn btn-info" data="{{ $setting->id }}" data-id="{{ $type !== 'settings' ? $arr->id : '' }}" data-index="{{ $index }}" data-type="modify">Modify</button>&nbsp;--}}
                                        @if(!isset($arr->is_removed) || $arr->is_removed === false)
                                            <a href="#" class="tip-top" data="{{ $setting->id }}" data-id="{{ $type !== 'settings' ? $arr->id : '' }}" data-index="{{ $index }}" data-type="delete" data-original-title="删除"><i class="icon-remove"></i></a>
                                            {{--<button class="btn btn-danger" data="{{ $setting->id }}" data-id="{{ $type !== 'settings' ? $arr->id : '' }}" data-index="{{ $index }}" data-type="delete">Delete</button>--}}
                                        @else
                                            <a href="#" class="tip-top" data="{{ $setting->id }}" data-id="{{ $type !== 'settings' ? $arr->id : '' }}" data-index="{{ $index }}" data-type="recover" data-original-title="恢复"><i class="icon-undo"></i></a>
                                            {{--<button class="btn btn-warning" data="{{ $setting->id }}" data-id="{{ $type !== 'settings' ? $arr->id : '' }}" data-index="{{ $index }}" data-type="recover">Recover</button>--}}
                                        @endif
                                        @if(isset($arr->is_default) && $arr->is_default === false)
                                            <a href="#" class="tip-top" data="{{ $setting->id }}" data-id="{{ $type !== 'settings' ? $arr->id : '' }}" data-index="{{ $index }}" data-type="setDefault" data-original-title="设为默认"><i class="icon-lock"></i></a>
                                            {{--&nbsp;<button class="btn btn-primary" data="{{ $setting->id }}" data-id="{{ $type !== 'settings' ? $arr->id : '' }}" data-index="{{ $index }}" data-type="setDefault">Set Default</button>--}}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @endif
                        <button class="btn btn-success" data="{{ $setting->id }}" data-type="add">添加子元素</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <script src="/js/systemSetting.js"></script>
    <script src="translation?trans=table"></script>
    <script>
        $(document).ready(function () {
            var systemType = '{{ $type }}';
            $('#system-group .button-groups a, #system-group button').on('click', function () {
                bindEventToButtonInSystemSetting(this, systemType);
            });
            $('#add-setting').on('click', function () {
                var tables = $('#system-group table');
                var table = tables.eq(-2);
                var columns = getTableColumns(table, ['is_official', 'is_default', 'is_active', 'is_removed', 'created_at', 'updated_at']);
                addSettingContentOrEditSetting(systemType, table, 'add', 'parent', null, null, null, columns);
            })
        })
    </script>
@elseif($result->statusCode == 10014)
    @include('message.messageAlert',['type'=>'error','message'=>'无效的type'])
@endif
