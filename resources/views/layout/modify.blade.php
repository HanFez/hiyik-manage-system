<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 11/21/16
 * Time: 9:13 AM
 */
$transAdmin = trans('admin');
//$result = json_decode(json_encode($result));
//dd($result);
?>
@include('layout/field')

@extends('layout/widget')

@section('icon')
    <i class="{{ $icon or 'icon-plus' }}"></i>
@stop

@section('title')
    {{ $transAdmin[$action] or $action }}
    @include('layout/required')
@stop

@section('content')
    <form class="form-horizontal">
        @if(isset($field))
            @foreach($field as $column)
                <?php
                $columnName = $column -> column_name;
                $isNull = strtoupper($column -> is_nullable) == 'YES' ? true : false;
                $type = $column -> data_type;
                $maxLength = $column -> character_maximum_length;
                $transName = $column -> column_name_trans;
                $value = count($result) > 0 ? $result[$columnName] : '';
                $gender = count($result) > 0 ? $result->gender : '';
                ?>
                @if($columnName == 'created_at' || $columnName == 'updated_at' || $columnName == 'is_active' || $columnName == 'is_removed')
                @else
                    <div class="control-group">
                        <label class="control-label">
                            @if($isNull == false)
                                <span class="text-important">*</span>
                            @endif
                            {{ $transName }} :
                        </label>
                        <div class="controls">
                            @if($columnName == 'gender')
                                <label class="label-inline">
                                    <input type="radio" name="gender" style="opacity: 0;" value="m" {{ $gender == 'm' ? 'checked="checked"' : '' }}>
                                    {{ $transAdmin['male'] or 'male' }}</label>
                                <label class="label-inline">
                                    <input type="radio" name="gender" style="opacity: 0;" value="f" {{ $gender == 'f' ? 'checked="checked"' : '' }}>
                                    {{ $transAdmin['female'] or 'female' }}</label>
                            @elseif($columnName == 'password')
                                <input type="password" class="span5" placeholder="{{ $transName }}" name="{{ $columnName }}"
                                       {{ $isNull ? '' : ' required="required"' }} value="{{ $result[$columnName] or '' }}">
                            @elseif(stripos($type, 'time') !== false)
                                <input type="date" class="span5 datepicker" placeholder="{{ $transName }}" name="{{ $columnName }}" {{ $isNull ? '' : ' required="required"' }}
                                data-date-format="yyyy-mm-dd" data-date="2016-10-28" value="{{ explode(' ', $value)[0] }}">
                            @elseif($columnName == 'only_core_need')
                                <label class="control-label">
                                    <input type="radio" name="{{$columnName}}" {{ $value==1 ? 'checked="checked"' : '' }} value="1"/>是</label>
                                <label class="control-label">
                                    <input type="radio" name="{{$columnName}}" {{ $value==0 ? 'checked="checked"' : '' }} value="0" />否</label>
                            @else{{--if($type == 'text')--}}
                            <input type="text" class="span5" placeholder="{{ $transName }}" name="{{ $columnName }}"
                                   {{ $isNull ? '' : ' required="required"' }} value="{{ $value }}">
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        @endif
        <div class="form-actions">
            <a type="submit" class="btn btn-success">{{ $transAdmin[$action] or $action}}</a>
        </div>
    </form>
@stop
