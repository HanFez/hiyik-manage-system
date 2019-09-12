$(document).ready(function () {
    $('#breadcrumb span').text(trans_admin.home);
    $('#breadcrumb a').attr('title', trans_admin.goTo + ' ' + trans_admin.home);
    ajaxData('get', 'userNav', handleGetViewUserNavCallback);
    ajaxData('get', 'subMenu', handleGetViewSubMenuCallback);
    ajaxData('get', 'content', appendViewToContainer);

    $.gritter.options = {
        position: "center-center",
        fade_out_speed: 1000,
        time: 2000
    }
})

function handleGetViewUserNavCallback(result) {
    $('#user-nav').append(result);
    $('.logout').unbind('click').on('click', function () {
        ajaxData('get', 'admin/quit', function (result) {
            $('body').append(result);
        })
    })
    //user nav
    $('#profile-messages ul a').on('click', function () {
        var $this = $(this);
        if(!isUndefined($this.attr('data-type'))) {
            var btnId = $this.attr('data-type').trim();
            var btn = $('#' + btnId);
            var subMenu = btn.parent().parent().parent();
            if (subMenu.hasClass('submenu')) {
                $('>a', subMenu).trigger('click');
            }
            btn.trigger('click');
        }
    })
}
function handleGetViewSubMenuCallback(result) {
    $('#sidebar').append(result);
    adminMenu();
    addNiceScroll($('#sidebar'));

    //announce
    $('#announce-list').on('click', function () {
        ajaxData('get', 'announceList?type=audit&take=6&skip=0', appendViewToContainer);
    })
    $('#announce-add').on('click', function () {
        ajaxData('get', 'createAnnounce', appendViewToContainer);
    })
    //advice
    $('#advice-list').on('click', function () {
        ajaxData('get', 'getAdviceList?isDeal=false&take=6&skip=0',  function (view) {
            $('#container').html(view);
            convertUtcTimeToLocalTime('container');
        });
    });
    //voucher
    $('#voucher-list').on('click',function(){
        ajaxData('get', 'voucher',appendViewToContainer);
    });
    $('#voucher-add').on('click',function(){
        ajaxData('get', 'voucherAdd',appendViewToContainer);
    });
    //report
    $('#report-menu a').on('click', function () {
        var $this = $(this);
        var type = $this.attr('type');
        ajaxData('get', 'reports?type=' + type + '&isDeal=false&take=6&skip=0', function (view) {
            $('#container').html(view);
            convertUtcTimeToLocalTime('container');
        });
    })
    //employee
    var url_employee = 'employee';
    $('#employee-list').on('click', function () {
        ajaxData('get', url_employee, appendViewToContainer);
    });
    $('#employee-add').on('click', function () {
        ajaxData('get', url_employee + '/create', handleGetViewEditDataCallback, [], url_employee);
    });
    //publication
    $('#publication-list').on('click', function () {
        ajaxData('get', 'publications?type=view&take=12&skip=0', appendViewToContainer);
    });
    //product
    $('#product-list').on('click', function () {
        ajaxData('get', 'new_pro/products?type=all&take=12&skip=0', appendViewToContainer);
    });
    //person
    $('#person-list').on('click', function () {
        ajaxData('get', 'persons?take=12&skip=0', appendViewToContainer);
    });
    //folder
    $('#folder-list').on('click', function () {
        ajaxData('get', 'getFolderList?isDeleted=false&take=6&skip=0', function (view) {
            $('#container').html(view);
            convertUtcTimeToLocalTime('container');
        });
    })
    //iwall
    $('#iwall-list').on('click',function(){
        ajaxData('get','iwall?type=normal&take=12&skip=0',appendViewToContainer);
    });
    //cart
    $('#cart-list').on('click',function(){
        ajaxData('get','cartList?take=6&skip=0',appendViewToContainer);
    });
    //order
    $('#order-list').on('click', function () {
        ajaxData('get', 'order?type=unpaid&take=6&skip=0', appendViewToContainer);
    });
    $('#order-statistics').on('click', function () {
        ajaxData('get', 'orderStatistics?type=unpaid&take=6&skip=0', appendViewToContainer);
    });
    $('#status-list').on('click', function () {
        ajaxData('get', 'status',appendViewToContainer);
    });
    $('#status-add').on('click', function () {
        ajaxData('get', 'addStatus',appendViewToContainer);
    });
    $('#refund-request-list').on('click', function () {
        ajaxData('get', 'refundRequest?auditing=0&take=6&skip=0',appendViewToContainer);
    });
    $('#order-refund').on('click',function(){
        ajaxData('get','refundList?type=walletPay&take=10&skip=0',appendViewToContainer);
    });
    $('#reject-request-list').on('click', function () {
        ajaxData('get', 'reject?auditing=false&take=6&skip=0',appendViewToContainer);
    });
    $('#company-list').on('click',function(){
        ajaxData('get','company',appendViewToContainer);
    });
    $('#company-add').on('click',function(){
        ajaxData('get','company/create',handleGetViewEditDataCallback,[],'company');
    });
    //settings
    $('#system-settings').on('click', function () {
        ajaxData('get', 'settings', appendViewToContainer);
    });
    $('#system-tags').on('click', function () {
        ajaxData('get', 'tags', appendViewToContainer);
    });
    $('#scene-list').on('click', function () {
        ajaxData('get', 'scene', appendViewToContainer);
    });
    $('#scene-add').on('click', function () {
        ajaxData('get', 'sceneAdd', appendViewToContainer);
    });
    $('#crowd-list').on('click', function () {
        ajaxData('get', 'crowd', appendViewToContainer);
    });
    $('#crowd-add').on('click', function () {
        ajaxData('get', 'crowdAdd', appendViewToContainer);
    });
    $('#sex-list').on('click', function () {
        ajaxData('get', 'sex', appendViewToContainer);
    });
    $('#sex-add').on('click', function () {
        ajaxData('get', 'sexAdd', appendViewToContainer);
    });

    //accessory
    $('#accessory-list').on('click', function () {
        ajaxData('get', 'accessory', appendViewToContainer);
    });
    $('#accessory-add').on('click', function () {
        ajaxData('get', 'accessory/create', handleGetViewEditDataCallback,[],'accessory');
    });

    //my
    $('#my-profile').on('click', function () {
        var adminId = getVal($('#adminId'));
        ajaxData('get', url_employee + '/' + adminId +'/edit', handleGetViewEditDataCallback, [], url_employee);
    });
    $('#modify-password').on('click', function () {
        ajaxData('get', 'manager/password', appendViewToContainer);
    });
    //manager
    var url_manager = 'manager';
    $('#manager-list').on('click', function () {
        ajaxData('get', url_manager, appendViewToContainer);
    });
    $('#manager-add').on('click', function () {
        ajaxData('get', url_manager + '/create', appendViewToContainer);
    });
    $('#manager-role-add').on('click', function () {
        ajaxData('get', 'managerRoles', appendViewToContainer);
    });
    //role
    var url_role = 'role';
    $('#role-list').on('click', function () {
        ajaxData('get', url_role, appendViewToContainer);
    });
    $('#role-add').on('click', function () {
        ajaxData('get', url_role + '/create', handleGetViewEditDataCallback, [], url_role);
    });
    $('#role-privilege-add').on('click', function () {
        ajaxData('get', 'getRoleList', appendViewToContainer);
    });
    //privilege
    var url_privilege = 'privilege';
    $('#privilege-list').on('click', function () {
        ajaxData('get', url_privilege, appendViewToContainer);
    });
    $('#privilege-add').on('click', function () {
        ajaxData('get', 'definedPrivilege', appendViewToContainer);
    });
    //table-data-list
    $('#table-data-list').on('click', function () {
        ajaxData('get', 'tables', appendViewToContainer);
    });
    //trades record
    $('#purchase-record').on('click',function () {
        ajaxData('get', 'purchase?type=all&take=10&skip=0', appendViewToContainer);
    });
    $('#reward-record').on('click',function () {
        ajaxData('get', 'reward?type=all&take=10&skip=0', appendViewToContainer);
    });
    $('#refund-record').on('click',function () {
        ajaxData('get', 'refundRecord?type=all&take=10&skip=0', appendViewToContainer);
    });
    $('#recharge-record').on('click',function () {
        ajaxData('get', 'recharge?take=10&skip=0', appendViewToContainer);
    });
    $('#cash-record').on('click',function () {
        ajaxData('get', 'cash?take=10&skip=0', appendViewToContainer);
    });
    $('#gain-record').on('click',function () {
        ajaxData('get', 'gain?take=10&skip=0', appendViewToContainer);
    });
    $('#postage-refund-record').on('click',function () {
        ajaxData('get', 'postage?take=10&skip=0', appendViewToContainer);
    });
    //share
    $('#share-record').on('click',function () {
        ajaxData('get', 'share?take=6&skip=0', appendViewToContainer);
    });
    //cash
    $('#cash-request-list').on('click',function () {
        ajaxData('get','withdraw?take=6&skip=0&type=cash',appendViewToContainer);
    });
    //category
    $('#handle-category-add').on('click',function () {
        ajaxData('get', 'new_pro/addHCategory', appendViewToContainer);
    });
    $('#handle-category-list').on('click',function () {
        ajaxData('get', 'new_pro/listHCategory', appendViewToContainer);
    });
    $('#material-category-add').on('click',function () {
        ajaxData('get', 'new_pro/addMCategory', appendViewToContainer);
    });
    $('#material-category-list').on('click',function () {
        ajaxData('get', 'new_pro/listMCategory', appendViewToContainer);
    });
    $('#product-category-add').on('click',function () {
        ajaxData('get', 'new_pro/addPCategory', appendViewToContainer);
    });
    $('#product-category-list').on('click',function () {
        ajaxData('get', 'new_pro/listPCategory', appendViewToContainer);
    });
    //produce
    $('#handle-add').on('click',function () {
        ajaxData('get', 'new_pro/addHandle', appendViewToContainer);
    });
    $('#handle-list').on('click',function () {
        ajaxData('get', 'new_pro/listHandle', appendViewToContainer);
    });
    $('#facade-add').on('click',function () {
        ajaxData('get', 'new_pro/addFacade', appendViewToContainer);
    });
    $('#facade-list').on('click',function () {
        ajaxData('get', 'new_pro/listFacade', appendViewToContainer);
    });
    $('#shape1-add').on('click',function () {
        ajaxData('get', 'new_pro/addShape', appendViewToContainer);
    });
    $('#shape1-list').on('click',function () {
        ajaxData('get', 'new_pro/listShape', appendViewToContainer);
    });
    $('#material1-add').on('click',function () {
        ajaxData('get', 'new_pro/addMaterial', appendViewToContainer);
    });
    $('#material1-list').on('click',function () {
        ajaxData('get', 'new_pro/listMaterial', appendViewToContainer);
    });
    $('#texture1-add').on('click',function () {
        ajaxData('get', 'new_pro/addTexture', appendViewToContainer);
    });
    $('#texture1-list').on('click',function () {
        ajaxData('get', 'new_pro/listTexture', appendViewToContainer);
    });
    $('#material-section-add').on('click',function () {
        ajaxData('get', 'new_pro/addMaterialSection', appendViewToContainer);
    });
    $('#material-section-list').on('click',function () {
        ajaxData('get', 'new_pro/materialSection', appendViewToContainer);
    });
    $('#material-texture-add').on('click',function () {
        ajaxData('get', 'new_pro/addMaterialTexture', appendViewToContainer);
    });
    $('#material-texture-list').on('click',function () {
        ajaxData('get', 'new_pro/materialTexture', appendViewToContainer);
    });
    $('#product-define-add').on('click',function () {
        ajaxData('get', 'new_pro/addProductDefine', appendViewToContainer);
    });
    $('#product-define-list').on('click',function () {
        ajaxData('get', 'new_pro/productDefine', appendViewToContainer);
    });
    $('#line-size-add').on('click',function () {
        ajaxData('get', 'new_pro/addLineSize', appendViewToContainer);
    });
    $('#line-size-list').on('click',function () {
        ajaxData('get', 'new_pro/lineSize', appendViewToContainer);
    });
    $('#predefine-add').on('click',function () {
        ajaxData('get', 'new_pro/addPredefine', appendViewToContainer);
    });
    $('#predefine-list').on('click',function () {
        ajaxData('get', 'new_pro/predefine', appendViewToContainer);
    });
    $('#product-define-category-add').on('click',function () {
        ajaxData('get', 'new_pro/addProductDefineCategory', appendViewToContainer);
    });
    $('#product-define-category-list').on('click',function () {
        ajaxData('get', 'new_pro/productDefineCategory', appendViewToContainer);
    });
    $('#border-define-add').on('click',function () {
        ajaxData('get', 'new_pro/addBorderDefine', appendViewToContainer);
    });
    $('#border-define-list').on('click',function () {
        ajaxData('get', 'new_pro/borderDefine', appendViewToContainer);
    });
    $('#border-material-define-add').on('click',function () {
        ajaxData('get', 'new_pro/addBorderMaterialDefine', appendViewToContainer);
    });
    $('#border-material-define-list').on('click',function () {
        ajaxData('get', 'new_pro/borderMaterialDefine', appendViewToContainer);
    });
    $('#core-define-add').on('click',function () {
        ajaxData('get', 'new_pro/addCoreDefine', appendViewToContainer);
    });
    $('#core-define-list').on('click',function () {
        ajaxData('get', 'new_pro/coreDefine', appendViewToContainer);
    });
    $('#core-material-define-add').on('click',function () {
        ajaxData('get', 'new_pro/addCoreMaterialDefine', appendViewToContainer);
    });
    $('#core-material-define-list').on('click',function () {
        ajaxData('get', 'new_pro/coreMaterialDefine', appendViewToContainer);
    });
    $('#core-handle-define-add').on('click',function () {
        ajaxData('get', 'new_pro/addCoreHandleDefine', appendViewToContainer);
    });
    $('#core-handle-define-list').on('click',function () {
        ajaxData('get', 'new_pro/coreHandleDefine', appendViewToContainer);
    });
    $('#show-define-add').on('click',function () {
        ajaxData('get', 'new_pro/addShowDefine', appendViewToContainer);
    });
    $('#show-define-list').on('click',function () {
        ajaxData('get', 'new_pro/showDefine', appendViewToContainer);
    });
    $('#show-material-define-add').on('click',function () {
        ajaxData('get', 'new_pro/addShowMaterialDefine', appendViewToContainer);
    });
    $('#show-material-define-list').on('click',function () {
        ajaxData('get', 'new_pro/showMaterialDefine', appendViewToContainer);
    });
    $('#frame-material-define-add').on('click',function () {
        ajaxData('get', 'new_pro/addFrameMaterialDefine', appendViewToContainer);
    });
    $('#frame-material-define-list').on('click',function () {
        ajaxData('get', 'new_pro/frameMaterialDefine', appendViewToContainer);
    });
    $('#frame-hole-define-add').on('click',function () {
        ajaxData('get', 'new_pro/addFrameHole', appendViewToContainer);
    });
    $('#frame-hole-define-list').on('click',function () {
        ajaxData('get', 'new_pro/frameHole', appendViewToContainer);
    });
    $('#back-material-define-add').on('click',function () {
        ajaxData('get', 'new_pro/addBackMaterialDefine', appendViewToContainer);
    });
    $('#back-material-define-list').on('click',function () {
        ajaxData('get', 'new_pro/backMaterialDefine', appendViewToContainer);
    });
    $('#front-material-define-add').on('click',function () {
        ajaxData('get', 'new_pro/addFrontMaterialDefine', appendViewToContainer);
    });
    $('#front-material-define-list').on('click',function () {
        ajaxData('get', 'new_pro/frontMaterialDefine', appendViewToContainer);
    });
    $('#bf-material-define-add').on('click',function () {
        ajaxData('get', 'new_pro/addBackFacade', appendViewToContainer);
    });
    $('#bf-material-define-list').on('click',function () {
        ajaxData('get', 'new_pro/backFacade', appendViewToContainer);
    });
    $('#hole-line-define-add').on('click',function () {
        ajaxData('get', 'new_pro/addHoleLine', appendViewToContainer);
    });
    $('#hole-line-define-list').on('click',function () {
        ajaxData('get', 'new_pro/holeLine', appendViewToContainer);
    });
    $('#line-material-define-add').on('click',function () {
        ajaxData('get', 'new_pro/addLineMaterial', appendViewToContainer);
    });
    $('#line-material-define-list').on('click',function () {
        ajaxData('get', 'new_pro/lineMaterial', appendViewToContainer);
    });
}
function handleGetViewEditDataCallback(result, url) {
    appendViewToContainer(result);
    $('#container a[type="submit"]').unbind('click').on('click', function () {
        var form = $('#container form');
        removeInputMessage(form);
        var data = getFormValue(form);
        if(data != false) {
            var params = {};
            params.data = data;
            ajaxData('post', url, parseAddEmployeeCallback, [], params);
        }
    })
}

function parseAddEmployeeCallback(result) {
    $('#container .form-horizontal').append(result);
}

