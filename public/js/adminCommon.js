/**
 * Add box nick scroll init.
 * @param $box: jq dom object
 * @param color: color, eg: 'red', '#fff', 'rgb(0,0,0)'
 * @param position: 'left' | 'right'
 * @returns {boolean}
 */
function addNiceScroll($box, color, position) {
    if(isUndefined($box)) {
        return false;
    }
    if(isNull(color)) {
        color = 'red';
    }
    if(isNull(position)) {
        position = 'left';
    }
    var winWidth = getWindowWidth();
    if(winWidth > 480) {
        $box.niceScroll({
            cursorcolor: color,//#CC0071 光标颜色
            cursoropacitymax: 1, //改变不透明度非常光标处于活动状态（scrollabar“可见”状态），范围从1到0
            touchbehavior: false, //使光标拖动滚动像在台式电脑触摸设备
            cursorwidth: "3px", //像素光标的宽度
            cursorborder: "0", //游标边框css定义
            cursorborderradius: "3px",//以像素为光标边界半径
            autohidemode: false, //是否隐藏滚动条
            railalign: position
        });
    }
}

/**
 * Append view to container, then init the elements in container, next the first input focus.
 * @param view
 * @param params:
 *  {
 *      init: {
 *          checkbox: bool,
 *          radio: bool,
 *          file: bool,
 *          select: bool,
 *          tip: bool,
 *          datePicker: bool,
 *          colorPicker: bool
 *      }
 *  }
 */
function appendViewToContainer(view, params) {
    if(!isNull(view)) {
        $('#container').html(view);
        initPageElement('container', params);
        $('#container input').eq(0).focus();
    }
}

/**
 * Init page elements.
 * @param selectorId
 * @param params:
 *  {
 *      init: {
 *          checkbox: bool,
 *          radio: bool,
 *          file: bool,
 *          select: bool,
 *          tip: bool,
 *          datePicker: bool,
 *          colorPicker: bool
 *      }
 *  }
 */
function initPageElement(selectorId, params) {
    var checkbox = true,
        radio = true,
        file = true,
        select = true,
        tip = true,
        datePicker = true,
        colorPicker = true;
    if(!isNull(params) && !isNull(params.init)) {
        var init = params.init;
        if(!isNull(init.checkbox)) {
            checkbox = init.checkbox;
        }
        if(!isNull(init.radio)) {
            radio = init.radio;
        }
        if(!isNull(init.file)) {
            file = init.file;
        }
        if(!isNull(init.select)) {
            select = init.select;
        }
        if(!isNull(init.tip)) {
            tip = init.tip;
        }
        if(!isNull(init.datePicker)) {
            datePicker = init.datePicker;
        }
        if(!isNull(init.colorPicker)) {
            colorPicker = init.colorPicker;
        }
    }
    if(checkbox != false) {
        $('#' + selectorId + ' input[type=checkbox]').uniform();
    }
    if(radio != false) {
        $('#' + selectorId + ' input[type=radio]').uniform();
    }
    if(file != false) {
        $('#' + selectorId + ' input[type=file]').uniform();
    }
    $('#' + selectorId + '  span.icon input:checkbox, #' + selectorId + '  th input:checkbox').click(function() {
        var checkedStatus = this.checked;
        var checkbox = $(this).parents('.widget-box').find('tr td:first-child input:checkbox');
        checkbox.each(function() {
            this.checked = checkedStatus;
            if (checkedStatus == this.checked) {
                $(this).closest('.checker > span').removeClass('checked');
            }
            if (this.checked) {
                $(this).closest('.checker > span').addClass('checked');
            }
        });
    });
    if(select != false) {
        $('#' + selectorId + ' select').select2();
    }
    // === Tooltips === //
    if(tip != false) {
        $('#' + selectorId + ' .tip').tooltip();
        $('#' + selectorId + ' .tip-left').tooltip({placement: 'left'});
        $('#' + selectorId + ' .tip-right').tooltip({placement: 'right'});
        $('#' + selectorId + ' .tip-top').tooltip({placement: 'top'});
        $('#' + selectorId + ' .tip-bottom').tooltip({placement: 'bottom'});
    }

    if(datePicker != false) {
        $('#' + selectorId + ' .datepicker').datepicker();
    }
    if(colorPicker != false) {
        $('#' + selectorId + ' .colorpicker').colorpicker();
    }
    // time
    convertUtcTimeToLocalTime(selectorId);
    //image
    $('#' + selectorId + ' img').unbind('error').on('error', function () {
        $(this).attr('src', '/img/default.png');
    })
}

/**
 * Convert utc time to local time.
 * @param selectorId: the time box parent.
 * @param isClass: bool, if time box has class time-utc, isClass is true, else the box has attribute data-time=utc. isClass is false or null.
 */
function convertUtcTimeToLocalTime(selectorId, isClass) {
    if(!isNull(isClass) && isClass == true) {
        var utcTimes = $('#' + selectorId + ' .time-utc');
    } else {
        var utcTimes = $('#' + selectorId + ' *[data-time="utc"]');
    }
    utcTimes.each(function () {
        var $this = $(this);
        var time = $this.text().trim();
        var local = convertDateTime(time, 'local', 'yyyy-MM-dd hh:mm:ss');
        // console.log(time +'  -  ' + local);
        $this.text(local);
    })
}

function adminMenu() {

    // === Sidebar navigation === //

    $('.submenu > a').click(function (e) {
        e.preventDefault();
        var submenu = $(this).siblings('ul');
        var li = $(this).parents('li');
        var submenuIconRight = li.find('.menu-icon-right');
        var submenus = $('#sidebar li.submenu ul');
        var submenusParents = $('#sidebar li.submenu');
        var winWidth = getWindowWidth();
        if (li.hasClass('open')) {
            if ((winWidth > 768) || (winWidth < 479)) {
                submenu.slideUp();
            } else {
                submenu.fadeOut(250);
            }
            li.removeClass('open');
            if(submenuIconRight.length > 0) {
                submenuIconRight.removeClass('icon-chevron-up').addClass('icon-chevron-down');
            }
        } else {
            if ((winWidth > 768) || (winWidth < 479)) {
                submenus.slideUp();
                submenu.slideDown();
            } else {
                submenus.fadeOut(250);
                submenu.fadeIn(250);
            }
            submenusParents.removeClass('open');
            submenusParents.find('.menu-icon-right').removeClass('icon-chevron-up').addClass('icon-chevron-down');
            li.addClass('open');
            if(submenuIconRight.length > 0) {
                submenuIconRight.removeClass('icon-chevron-down').addClass('icon-chevron-up');
            }
        }
    });

    var ul = $('#sidebar > ul');

    $('#sidebar > a').click(function (e) {
        e.preventDefault();
        var sidebar = $('#sidebar');
        if (sidebar.hasClass('open')) {
            sidebar.removeClass('open');
            ul.slideUp(250);
        } else {
            sidebar.addClass('open');
            ul.slideDown(250);
        }
    });

    $('#sidebar li').on('click', function () {
        var $this = $(this);
        var index = $("#sidebar li").index(this);
        if(!$this.hasClass('submenu')) {
            if($this.hasClass('show')) {
                return false;
            }
            $('div.datepicker').remove();
            $('#sidebar li').removeClass('active');
            $('#sidebar a').removeClass('hover');
            $('#breadcrumb a').not($('#breadcrumb a').eq(0)).remove();
            if($this.parent().parent().hasClass('submenu')) {
                $this.parent().parent().addClass('active');
                $('>a', $this).addClass('hover');
                var text = $('>a', $this.parent().parent()).text().trim();
                $('#breadcrumb').append('<a class="tip-bottom" href="#" data-original-title="">' + text + '</a>');
            } else {
                $this.addClass('active');
            }
            if(!$this.hasClass('show')) {
                $('#sidebar li.show').removeClass('show').addClass('hide');
            }
            if($this.hasClass('hide')) {
                $this.removeClass('hide').addClass('show');
            }
            if(index != 0) {
                $('#breadcrumb').append('<a class="current" href="#">' + $this.text().trim() + '</a>');
            }
        }
    })

    // === Resize window related === //
    $(window).resize(function () {
        var winWidth = getWindowWidth();
        if (winWidth >= 479) {
            ul.css({'display': 'block'});
            $('#content-header .btn-group').css({width: 'auto'});
        }
        if (winWidth < 479) {
            ul.css({'display': 'none'});
            fix_position();
        }
        if (winWidth >= 768) {
            $('#user-nav > ul').css({width: 'auto', margin: '0'});
            $('#content-header .btn-group').css({width: 'auto'});
        }
    });

    var winWidth = getWindowWidth();
    if (winWidth < 479) {
        ul.css({'display': 'none'});
        fix_position();
    }

    if (winWidth > 479) {
        $('#content-header .btn-group').css({width: 'auto'});
        ul.css({'display': 'block'});
    }

    // === Fixes the position of buttons group in content header and top user navigation === //
    function fix_position() {
        var uwidth = $('#user-nav > ul').width();
        // $('#user-nav > ul').css({width:uwidth,'margin-left':'-' + uwidth / 2 + 'px'});

        var cwidth = $('#content-header .btn-group').width();
        $('#content-header .btn-group').css({width: cwidth, 'margin-left': '-' + uwidth / 2 + 'px'});
    }
    // === Tooltips === //
    $('.tip').tooltip();
    $('.tip-left').tooltip({placement: 'left'});
    $('.tip-right').tooltip({placement: 'right'});
    $('.tip-top').tooltip({placement: 'top'});
    $('.tip-bottom').tooltip({placement: 'bottom'});

    /*// === Search input typeahead === //
   $('#search input[type=text]').typeahead({
       source: ['Dashboard', 'Form elements', 'Common Elements', 'Validation', 'Wizard', 'Buttons', 'Icons', 'Interface elements', 'Support', 'Calendar', 'Gallery', 'Reports', 'Charts', 'Graphs', 'Widgets'],
       items: 4
   });


   // === Style switcher === //
   $('#style-switcher i').click(function () {
       if ($(this).hasClass('open')) {
           $(this).parent().animate({marginRight: '-=190'});
           $(this).removeClass('open');
       } else {
           $(this).parent().animate({marginRight: '+=190'});
           $(this).addClass('open');
       }
       $(this).toggleClass('icon-arrow-left');
       $(this).toggleClass('icon-arrow-right');
   });

   $('#style-switcher a').click(function () {
       var style = $(this).attr('href').replace('#', '');
       $('.skin-color').attr('href', 'css/maruti.' + style + '.css');
       $(this).siblings('a').css({'border-color': 'transparent'});
       $(this).css({'border-color': '#aaaaaa'});
   });

   $('.lightbox_trigger').click(function (e) {

       e.preventDefault();

       var image_href = $(this).attr("href");

       if ($('#lightbox').length > 0) {

           $('#imgbox').html('<img src="' + image_href + '" /><p><i class="icon-remove icon-white"></i></p>');

           $('#lightbox').slideDown(500);
       }

       else {
           var lightbox =
               '<div id="lightbox" style="display:none;">' +
               '<div id="imgbox"><img src="' + image_href + '" />' +
               '<p><i class="icon-remove icon-white"></i></p>' +
               '</div>' +
               '</div>';

           $('body').append(lightbox);
           $('#lightbox').slideDown(500);
       }

   });


   $('#lightbox').live('click', function () {
       $('#lightbox').hide(200);
   });*/

}
