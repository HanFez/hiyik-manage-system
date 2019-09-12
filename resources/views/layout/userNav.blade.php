<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 10/28/16
 * Time: 9:48 AM
 */

$transAdmin = trans('admin');
$manager = isset($manager) ? $manager : null;
?>
<input id="token" type="hidden" name="_token" value="{{csrf_token()}}">
<input id="adminId" type="hidden" value="{{ session('login.id') }}">
<ul class="nav">
    @if(!is_null($manager))
        @if(!$manager->managerRole->isEmpty())
            @foreach($manager->managerRole as $role)
                @if($role->roles->name == 'super')
                    <li>
                        <a href="admin.html"><span>HIYIK管理</span></a>
                    </li>
                    <li>
                        <a href="thirdProduct.html"><span>溯源产品管理</span></a>
                    </li>
                @elseif($role->roles->name == 'sourceManager')
                    <li>
                        <a href="thirdProduct.html"><span>溯源产品管理</span></a>
                    </li>
                @else
                    <li>
                        <a href="admin.html"><span>HIYIK管理</span></a>
                    </li>
                    @break
                @endif
            @endforeach
        @endif
    @endif
    <li class="dropdown" id="profile-messages" >
        <a title="" href="#" data-toggle="dropdown" data-target="#profile-messages" class="dropdown-toggle">
            <i class="icon icon-user"></i>
            <span class="text">{{ $transAdmin['welcome'] or 'welcome' }}，{{ session('login.name') }}</span>
            <b class="caret"></b>
        </a>
        <ul class="dropdown-menu">
            {{--<li><a data-type="my-profile" href="javascript:void(0)"><i class="icon-user"></i> {{ $transAdmin['profile'] or 'profile' }}</a></li>--}}
            {{--<li class="divider"></li>--}}
            <li><a data-type="modify-password" href="javascript:void(0)"><i class="icon-key"></i> {{ $transAdmin['modifyPassword'] or 'modifyPassword' }}</a></li>
            <li class="divider"></li>
            <li class="logout"><a href="javascript:void(0)"><i class="icon-share-alt"></i> {{ $transAdmin['logout'] or 'logout' }}</a></li>
        </ul>
    </li>
    {{--<li class="dropdown" id="menu-messages"><a href="#" data-toggle="dropdown" data-target="#menu-messages" class="dropdown-toggle"><i class="icon icon-envelope"></i> <span class="text">我的消息</span> <span class="label label-important">5</span> <b class="caret"></b></a>
        <ul class="dropdown-menu">
            <li><a class="sAdd" title="" href="#"><i class="icon-plus"></i> 新消息</a></li>
            <li class="divider"></li>
            <li><a class="sInbox" title="" href="#"><i class="icon-envelope"></i> 收件箱</a></li>
            <li class="divider"></li>
            <li><a class="sOutbox" title="" href="#"><i class="icon-arrow-up"></i> 发件箱</a></li>
            <li class="divider"></li>
            <li><a class="sTrash" title="" href="#"><i class="icon-trash"></i> 回收站</a></li>
        </ul>
    </li>--}}
    {{--<li class=""><a title="" href="#"><i class="icon icon-cog"></i> <span class="text">{{ $transAdmin['setting'] or 'setting' }}</span></a></li>--}}
    <li class="logout"><a title="" href="#"><i class="icon icon-share-alt"></i> <span class="text">{{ $transAdmin['logout'] or 'logout' }}</span></a></li>
</ul>
