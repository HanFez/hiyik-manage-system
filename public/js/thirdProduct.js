$(document).ready(function () {
    $('#breadcrumb span').text(trans_admin.home);
    $('#breadcrumb a').attr('title', trans_admin.goTo + ' ' + trans_admin.home);
    ajaxData('get', 'userNav', handleGetViewUserNavCallback);
    ajaxData('get', 'content', appendViewToContainer);

    $.gritter.options = {
        position: "center-center",
        fade_out_speed: 1000,
        time: 2000
    }
    adminMenu();
    addNiceScroll($('#sidebar'));
    addSubMenuClickEvent();
});

function addSubMenuClickEvent() {
    var path = originPath();
    //product manage.
    $('#tb-product-add').on('click', function() {
        var str = JSON.stringify(["craft"]);
        ajaxData('get', path + 'createProduct?introductions=' + str, appendViewToContainer, [], {
            init: {
                file: false
            }
        });
    });
    $('#tb-product-list').on('click', function () {
        ajaxData('get', path + 'products?isDelete=false&isSell=true&take=12&skip=0',   function (view) {
            $('#container').html(view);
            convertUtcTimeToLocalTime('container');
        });
    });

    //publication manage.
    $('#tb-publication-add').on('click', function () {
        ajaxData('get', path + 'createPublication?introductions=["craft"]', appendViewToContainer, [], {
            init: {
                file: false
            }
        });
    });
    $('#tb-publication-list').on('click', function () {
        ajaxData('get', path + 'publications', appendViewToContainer);
    });

    //author manage.
    $('#tb-author-add').on('click', function () {
        ajaxData('get', path + 'createAuthor', appendViewToContainer, [], {
            init: {
                file: false
            }
        });
    });
    $('#tb-author-list').on('click', function () {
        ajaxData('get', path + 'authors', appendViewToContainer);
    });

    //museum manage.
    $('#tb-museum-add').on('click', function () {
        ajaxData('get', path + 'createMuseum', appendViewToContainer, [], {
            init: {
                file: false
            }
        });
    });
    $('#tb-museum-list').on('click', function () {
        ajaxData('get', path + 'museums', appendViewToContainer);
    });

    //shop manage.
    $('#tb-shop-add').on('click', function () {
        ajaxData('get', path + 'createShop', appendViewToContainer);
    });
    $('#tb-shop-list').on('click', function () {
        ajaxData('get', path + 'shops', appendViewToContainer);
    });

    //introduction manage.
    $('#tb-introduction-add').on('click', function () {
        ajaxData('get', path + 'createIntroduction', appendViewToContainer, [], {
            init: {
                file: false
            }
        });
    });
    $('#tb-introduction-list').on('click', function () {
        ajaxData('get', path + 'introductions', appendViewToContainer);
    });

    //real produce manage.
    $('#tb-real-product-add').on('click', function () {
        ajaxData('get', path + 'createRealProduct', appendViewToContainer, [], {
            init: {
                file: false
            }
        });
    });
    $('#tb-real-product-list').on('click', function () {
        ajaxData('get', path + 'realProducts', appendViewToContainer);
    });

    //search real product by no.
    $('#tb-real-product-no').on('click', function () {
        ajaxData('get', path + 'realProductSearch', appendViewToContainer);
    })
    $('#tb-order-no').on('click', function () {
        ajaxData('get', path + 'orderSearch', appendViewToContainer);
    })
}

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
