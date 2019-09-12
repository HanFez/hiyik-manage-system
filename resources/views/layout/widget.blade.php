<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 11/8/16
 * Time: 10:35 AM
 */
?>
<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    @yield('icon', '<i class="icon-th"></i>')
                </span>
                <h5>
                    @yield('title')
                </h5>
            </div>
            <div class="widget-content nopadding clearfix">
                @yield('content')
            </div>
        </div>
    </div>
</div>