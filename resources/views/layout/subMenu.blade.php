<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 10/28/16
 * Time: 9:47 AM
 */
$transAdmin = trans('admin');
$manager = isset($manager) ? $manager : null;
?>
<a href="#" class="visible-phone"><i class="icon icon-home"></i> {{ $transAdmin['home'] or 'home' }}</a>
<ul>
    <li class="active"><a href="admin.html"><i class="icon icon-home"></i> <span>{{ $transAdmin['home'] or 'home' }}</span></a> </li>
    @if(!is_null($manager))
        @if(!$manager->managerRole->isEmpty())
            @foreach($manager->managerRole as $role)
                @if($role->roles->name == 'super' || $role->roles->name == 'announceManager')
                    <li class="submenu">
                        <a href="javascript:void (0);">
                            <i class="icon icon-volume-up"></i>
                            <span>{{ $transAdmin['announce'] or 'announce' }}{{ $transAdmin['manage'] or 'manage' }}</span>
                            <i class="icon icon-chevron-down menu-icon-right"></i>
                        </a>
                        <ul>
                            <li><a id="announce-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>公告列表</a></li>
                            <li><a id="announce-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>添加公告</a></li>
                            <li class="hide">
                                <a id="announce-edit" href="javascript:void (0);"><i class="icon icon-caret-right"></i>修改公告</a>
                            </li>
                        </ul>
                    </li>
                @endif
                @if($role->roles->name == 'super' || $role->roles->name == 'userPublicationManager')
                    <li class="submenu">
                        <a href="#">
                            <i class="icon icon-th"></i>
                            <span>用户作品{{ $transAdmin['manage'] or 'manage' }}</span>
                            <i class="icon icon-chevron-down menu-icon-right"></i>
                        </a>
                        <ul>
                            <li>
                                <a id="publication-list" href="javascript:void (0);">
                                    <i class="icon icon-caret-right"></i>
                                    {{ $transAdmin['publication'] or 'publication' }}{{ $transAdmin['manage'] or 'manage' }}
                                </a>
                            </li>
                            <li class="hide"><a id="person-publication-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>用户作品</a></li>
                            <li><a id="iwall-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>IWall管理</a></li>
                            <li class="hide"><a id="person-iwall-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>用户Iwall</a></li>
                            <li><a id="product-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>产品管理</a></li>
                            <li><a id="person-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>用户管理</a></li>
                            <li><a id="folder-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>收藏夹管理</a></li>
                        </ul>
                    </li>
                @endif
                @if($role->roles->name == 'super' || $role->roles->name == 'reportManager')
                    <li class="submenu">
                        <a href="javascript:void (0);">
                            <i class="icon icon-exclamation-sign"></i>
                            <span>举报管理</span>
                            <i class="icon icon-chevron-down menu-icon-right"></i>
                        </a>
                        <ul id="report-menu">
                            <li><a type="comment" href="javascript:void (0);"><i class="icon icon-caret-right"></i>评论举报</a></li>
                            <li><a type="message" href="javascript:void (0);"><i class="icon icon-caret-right"></i>私信举报</a></li>
                            <li><a type="publication" href="javascript:void (0);"><i class="icon icon-caret-right"></i>作品举报</a></li>
                            <li><a type="person" href="javascript:void (0);"><i class="icon icon-caret-right"></i>用户举报</a></li>
                        </ul>
                    </li>
                @endif
                @if($role->roles->name == 'super' || $role->roles->name == 'adviceManager')
                    <li><a id="advice-list" href="javascript:void (0);"><i class="icon icon-bell"></i> <span>意见建议</span></a></li>
                @endif
                @if($role->roles->name == 'super' || $role->roles->name == 'voucherManager')
                    <li class="submenu"><a href="javascript:void (0);"><i class="icon-gift"></i> <span>优惠券管理</span> <i class="icon icon-chevron-down menu-icon-right"></i></a>
                        <ul>
                            <li><a id="voucher-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>优惠券列表</a></li>
                            <li class="hide"><a id="voucher-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>添加优惠券</a></li>
                        </ul>
                    </li>
                @endif
                @if($role->roles->name == 'super' || $role->roles->name == 'systemManager')
                    <li class="submenu"> <a href="#"><i class="icon icon-cogs"></i> <span>系统管理</span> <i class="icon icon-chevron-down menu-icon-right"></i></a>
                        <ul>
                            <li><a id="system-settings" href="javascript:void (0);"><i class="icon icon-caret-right"></i>系统设置</a></li>
                            <li><a id="system-tags" href="javascript:void (0);"><i class="icon icon-caret-right"></i>标签管理</a></li>
                            {{--<li><a href="javascript:void (0);"><i class="icon icon-caret-right"></i>关键字管理</a></li>--}}

                            <li><a id="scene-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>场景列表</a></li>
                            <li class="hide"><a id="scene-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>添加场景</a></li>
                            <li><a id="crowd-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>人群列表</a></li>
                            <li class="hide"><a id="crowd-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>添加人群</a></li>
                            <li><a id="sex-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>性别列表</a></li>
                            <li class="hide"><a id="sex-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>添加性别</a></li>
                        </ul>
                    </li>
                @endif
                @if($role->roles->name == 'super' || $role->roles->name == 'employeeManager')
                    <li class="submenu"> <a href="#"><i class="icon-group"></i> <span>员工管理</span> <i class="icon icon-chevron-down menu-icon-right"></i></a>
                        <ul>
                            <li><a id="employee-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>员工列表</a></li>
                            <li class="hide"><a id="employee-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>添加员工</a></li>
                            <li class="hide"><a id="employee-edit" href="javascript:void (0);"><i class="icon icon-caret-right"></i>修改员工</a></li>
                            <li class="hide"><a id="employee-show" href="javascript:void (0);"><i class="icon icon-caret-right"></i>查看员工</a></li>
                        </ul>
                    </li>
                @endif
                @if($role->roles->name == 'super' || $role->roles->name == 'permissionManager')
                    <li class="submenu"> <a href="#"><i class="icon-user-md"></i> <span>管理员管理</span> <i class="icon icon-chevron-down menu-icon-right"></i></a>
                        <ul>
                            <li><a id="manager-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>管理员列表</a></li>
                            <li class="hide"><a id="manager-role-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>管理员角色列表</a></li>
                            <li class="hide"><a id="manager-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>添加管理员</a></li>
                            <li class="hide"><a id="manager-edit" href="javascript:void (0);"><i class="icon icon-caret-right"></i>修改管理员</a></li>
                            <li><a id="manager-role-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>管理员角色分配</a></li>
                            <li><a id="role-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>角色列表</a></li>
                            <li class="hide"><a id="role-privilege-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>角色权限列表</a></li>
                            <li class="hide"><a id="role-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>添加角色</a></li>
                            <li class="hide"><a id="role-edit" href="javascript:void (0);"><i class="icon icon-caret-right"></i>修改角色</a></li>
                            <li><a id="role-privilege-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>角色权限分配</a></li>
                            <li><a id="privilege-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>权限列表</a></li>
                            <li class="hide"><a id="privilege-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>定义权限</a></li>
                            <li class="hide"><a id="privilege-edit" href="javascript:void (0);"><i class="icon icon-caret-right"></i>修改权限</a></li>
                        </ul>
                    </li>
                @endif
                @if($role->roles->name == 'super'  || $role->roles->name == 'sellManager')
                    <li class="submenu"><a href="javascript:void (0);"><i class="icon-bar-chart"></i> <span>销售管理</span> <i class="icon icon-chevron-down menu-icon-right"></i></a>
                        <ul>
                            <li><a id="order-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>订单列表</a></li>
                            <li><a id="refund-request-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>退款申请</a></li>
                            <li><a id="order-refund" href="javascript:void (0);"><i class="icon icon-caret-right"></i>订单退款</a></li>
                            <li><a id="reject-request-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>退换申请</a></li>
                            <li><a id="cash-request-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i><span>提现申请</span></a></li>
                            <li><a id="order-statistics" href="javascript:void (0);"><i class="icon icon-caret-right"></i>订单统计</a></li>
                            <li><a id="status-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>状态列表</a></li>
                            <li class="hide"><a id="status-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>添加状态</a></li>
                            <li><a id="company-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>快递公司列表</a></li>
                            <li class="hide"><a id="company-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>添加快递公司</a></li>
                            <li><a id="cart-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i> <span>购物车</span></a></li>
                        </ul>
                    </li>
                @endif
                @if($role->roles->name == 'super'  || $role->roles->name == 'tradeManager')
                    <li class="submenu"><a href="javascript:void (0);"><i class="icon-money"></i> <span>交易管理</span><i class="icon icon-chevron-down menu-icon-right"></i></a>
                        <ul>
                            <li><a id="purchase-record" href="javascript:void (0);"><i class="icon icon-caret-right"></i>购买记录</a></li>
                            <li><a id="reward-record" href="javascript:void (0);"><i class="icon icon-caret-right"></i>打赏记录</a></li>
                            <li><a id="refund-record" href="javascript:void (0);"><i class="icon icon-caret-right"></i>退款记录</a></li>
                            <li><a id="recharge-record" href="javascript:void (0);"><i class="icon icon-caret-right"></i>充值记录</a></li>
                            <li><a id="cash-record" href="javascript:void (0);"><i class="icon icon-caret-right"></i>提现记录</a></li>
                            <li><a id="gain-record" href="javascript:void (0);"><i class="icon icon-caret-right"></i>版费记录</a></li>
                            <li><a id="postage-refund-record" href="javascript:void (0);"><i class="icon icon-caret-right"></i>退邮记录</a></li>
                        </ul>
                    </li>
                @endif
                @if($role->roles->name == 'super' || $role->roles->name == 'shareManager')
                    <li class="submenu"><a href="javascript:void (0);"><i class="icon-share"></i><span> 分享管理</span><i class="icon icon-chevron-down menu-icon-right"></i></a>
                        <ul>
                            <li><a id="share-record" href="javascript:void (0);"><i class="icon icon-caret-right"></i>分享记录</a></li>
                        </ul>
                    </li>
                @endif
                @if($role->roles->name == 'super'  || $role->roles->name == 'productManager')
                    <li class="submenu"><a href="javascript:void (0);"><i class="icon-th-large"></i><span> 分类管理</span><i class="icon icon-chevron-down menu-icon-right"></i></a>
                        <ul>
                            <li class="hide"><a id="handle-category-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>添加工艺分类</a></li>
                            <li><a id="handle-category-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>工艺分类列表</a></li>
                            <li class="hide"><a id="material-category-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>添加材料分类</a></li>
                            <li><a id="material-category-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>材料分类列表</a></li>
                            <li class="hide"><a id="product-category-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>添加产品分类</a></li>
                            <li><a id="product-category-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>产品分类列表</a></li>
                        </ul>
                    </li>
                @endif
                @if($role->roles->name == 'super'  || $role->roles->name == 'productManager')
                    <li class="submenu"><a href="javascript:void (0);"><i class="icon-asterisk"></i><span> 生产数据管理</span><i class="icon icon-chevron-down menu-icon-right"></i></a>
                        <ul>
                            <li class="hide"><a id="handle-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>添加工艺</a></li>
                            <li><a id="handle-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>工艺列表</a></li>
                            <li class="hide"><a id="shape1-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>添加形状</a></li>
                            <li><a id="shape1-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>形状列表</a></li>
                            <li class="hide"><a id="material1-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>添加材料</a></li>
                            <li><a id="material1-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>材料列表</a></li>
                            <li class="hide"><a id="facade-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>添加外观图</a></li>
                            <li><a id="facade-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>外观图列表</a></li>
                            <li class="hide"><a id="texture1-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>添加纹理</a></li>
                            <li><a id="texture1-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>纹理列表</a></li>
                            <li class="hide"><a id="material-texture-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>添加材料纹理</a></li>
                            <li><a id="material-texture-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>材料纹理列表</a></li>
                            <li class="hide"><a id="material-section-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>添加材料截面</a></li>
                            <li><a id="material-section-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>材料截面列表</a></li>
                            <li><a id="accessory-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>配件列表</a></li>
                            <li class="hide"><a id="accessory-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>添加配件</a></li>
                        </ul>
                    </li>
                @endif
                @if($role->roles->name == 'super'  || $role->roles->name == 'productManager')
                    <li class="submenu"><a href="javascript:void (0);"><i class="icon-briefcase"></i><span> 产品定义管理</span><i class="icon icon-chevron-down menu-icon-right"></i></a>
                        <ul>
                            <li class="hide"><a id="product-define-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>定义产品</a></li>
                            <li><a id="product-define-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>产品定义列表</a></li>
                            <li class="hide"><a id="product-define-category-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>添加产品定义分类</a></li>
                            <li><a id="product-define-category-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>产品定义分类列表</a></li>
                            <li class="hide"><a id="line-size-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>添加线条尺寸</a></li>
                            <li><a id="line-size-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>线条尺寸列表</a></li>

                            <li class="hide"><a id="border-define-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>定义框</a></li>
                            <li><a id="border-define-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>框定义列表</a></li>
                            <li class="hide"><a id="border-material-define-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>定义框材料</a></li>
                            <li><a id="border-material-define-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>框材料定义列表</a></li>
                            <li class="hide"><a id="core-define-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>定义画芯</a></li>
                            <li><a id="core-define-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>画芯定义列表</a></li>
                            <li class="hide"><a id="core-material-define-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>定义画芯材料</a></li>
                            <li><a id="core-material-define-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>画芯材料定义列表</a></li>
                            <li class="hide"><a id="core-handle-define-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>定义画芯工艺</a></li>
                            <li><a id="core-handle-define-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>画芯工艺定义列表</a></li>
                            <li class="hide"><a id="frame-material-define-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>定义卡纸材料</a></li>
                            <li><a id="frame-material-define-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>卡纸材料定义列表</a></li>
                            <li class="hide"><a id="frame-hole-define-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>定义卡纸开洞</a></li>
                            <li><a id="frame-hole-define-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>卡纸开洞定义列表</a></li>
                            <li class="hide"><a id="back-material-define-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>定义背板材料</a></li>
                            <li><a id="back-material-define-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>背板材料定义列表</a></li>
                            <li class="hide"><a id="front-material-define-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>定义玻璃材料</a></li>
                            <li><a id="front-material-define-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>玻璃材料定义列表</a></li>
                            <li class="hide"><a id="bf-material-define-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>定义背板装饰材料</a></li>
                            <li><a id="bf-material-define-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>背板装饰材料定义列表</a></li>
                            <li class="hide"><a id="show-define-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>定义装饰</a></li>
                            <li><a id="show-define-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>装饰定义列表</a></li>
                            <li class="hide"><a id="show-material-define-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>定义装饰材料</a></li>
                            <li><a id="show-material-define-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>装饰材料定义列表</a></li>
                            <li class="hide"><a id="hole-line-define-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>定义洞线条</a></li>
                            <li><a id="hole-line-define-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>洞线条定义列表</a></li>
                            <li class="hide"><a id="line-material-define-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>定义洞线条材料</a></li>
                            <li><a id="line-material-define-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>洞线条材料定义列表</a></li>
                            <li class="hide"><a id="predefine-add" href="javascript:void (0);"><i class="icon icon-caret-right"></i>预定义产品</a></li>
                            <li><a id="predefine-list" href="javascript:void (0);"><i class="icon icon-caret-right"></i>预定义产品列表</a></li>
                        </ul>
                    </li>
                @endif
                @if($role->roles->name == 'super' || $role->roles->name == 'tableManager')
                    <li><a id="table-data-list" href="javascript:void (0);"><i class="icon-barcode"></i> <span>表数据</span></a></li>
                @endif
            @endforeach
            <li class="submenu"><a href="javascript:void (0);"><i class="icon icon-user"></i>
                    <span>{{ $transAdmin['personalInformation'] or 'personalInformation' }}</span>
                    <i class="icon icon-chevron-down menu-icon-right"></i></a>
                <ul>
                    {{--<li><a id="my-profile" href="javascript:void (0);"><i class="icon icon-caret-right"></i>{{ $transAdmin['profile'] or 'profile' }}</a></li>--}}
                    <li><a id="modify-password" href="javascript:void (0);"><i class="icon icon-caret-right"></i>{{ $transAdmin['modifyPassword'] or 'modifyPassword' }}</a></li>
                </ul>
            </li>
        @endif
    @endif
</ul>
