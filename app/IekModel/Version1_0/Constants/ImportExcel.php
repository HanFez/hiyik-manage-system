<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 1/8/18
 * Time: 9:05 AM
 */

namespace App\IekModel\Version1_0\Constants;


class ImportExcel
{
    const MUSEUM = [
        'name' => '原名',
        'lang' => '翻译',
        'description' => '描述'
    ];
    const PUBLICATION = [
        'no' => '作品编号',
        'author_no' => '作者编号',
        'name' => '原名',
        'lang' => '翻译',
        'year' => '作品年代',
        'width' => '宽cm',
        'height' => '高cm',
        'museum_name' => '珍藏处',
        'museum_lang' => '珍藏处翻译'
    ];
    const AUTHOR = [
        'no' => '作者编号',
        'name' => '原名',
        'lang' => '翻译',
        'description' => '描述',
        'introduction' => '简介',
        'nationality' => '国籍',
        'saying' => '名言',
        'feature' => '艺术特色',
    ];
    const PRODUCE_PARAMS = [
        'product_no' => '产品编号',
        'core_no' => '画芯编号',
        'core_size' => '画芯尺寸',
        'border_no' => '画框编号',
        'border_size' => '画框尺寸',
        'back_size' => '背板尺寸',
        'core_nail' => '画芯钉',
        'back_nail' => '背板钉',
        'flannel_size' => '绒布尺寸',
        'flannel_width' => '绒布宽度',
        'coating' => '涂层',
        'hide_hook' => '暗挂',
        'hook' => '挂勾',
        'core_material' => '画芯材料',
        'wire_rope' => '钢丝绳',
        'line_locker' => '锁线器',
        'mount' => '装裱方式',
        'back_size' => '背板尺寸',
        'frame_size' => '卡纸尺寸',
        'frame_width' => '卡纸宽度',
    ];
    const TB_ORDER = [
        'order_no' => '订单编号',
        'receive_name' => '收货人姓名',
        'receive_address' => '收货地址',
        'receive_phone' => '联系手机',
        'receive_call' => '联系电话',
        'ship_no' => '物流单号',
        'ship_company' => '物流公司',
        'memo' => '订单备注',
    ];
    const TB_ORDER_PRODUCT = [
        'order_no' => '订单编号',
        'num' => '购买数量',
        'product_no' => '商家编码',
    ];
    const TB_ORDER_REAL_PRODUCT = [
        'order_no' => '订单编号',
        'real_product_no' => '购买数量',
    ];
}