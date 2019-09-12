<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 11/11/16
 * Time: 5:34 PM
 */
$total = isset($result->total) ? $result->total : 0;
$skip  = isset($result->skip) ? $result->skip : 0;
$take  = isset($result->take) ? $result->take : 6;
$page  = ceil($total / $take) == 0 ? 1 : ceil($total / $take);
$nowPage = $skip/$take + 1;

$leftPage = $nowPage - 2 > (1 + 2) ? $nowPage - 2 : 2;
$rightPage = $nowPage + 2 < $page - 2 ? $nowPage + 2 : $page - 1;
?>
<div class="clearfix"></div>
<div id="pagination" class="pagination alternate">
    <ul>
        <li class="{{ $nowPage == 1 ? 'disabled' : '' }}"><a data="prev" href="javascript:void(0)">{{ $transDataTable['previous'] or 'previous' }}</a></li>
        @if($page == 1)
            <li class="active"><a href="javascript:void(0)">1</a></li>
            <li class="disabled"><a href="javascript:void(0)">{{ $transDataTable['next'] or 'next' }}</a></li>
        @else
            <li class="{{ $nowPage == 1 ? 'active' : ''}}"><a href="javascript:void(0)">1</a></li>
            @if($page < 10)
                @for($i = 2; $i <= $page - 1; $i ++)
                    <li class="{{ $nowPage == $i ? 'active' : ''}}"><a href="javascript:void(0)">{{ $i }}</a></li>
                @endfor
            @else
                @if($leftPage < 3 && $leftPage > 1)
                    <li class="{{ $nowPage == 2 ? 'active' : ''}}"><a href="javascript:void(0)">2</a></li>
                @elseif($leftPage > 3)
                    <li><span href="javascript:void(0)">...</span></li>
                @endif
                @for($i = $leftPage; $i <= $rightPage; $i ++)
                    @if($i != 2 && $i != $page - 1)
                        <li class="{{ $nowPage == $i ? 'active' : ''}}"><a href="javascript:void(0)">{{ $i }}</a></li>
                    @endif
                @endfor
                @if($rightPage > $page - 2 && $rightPage < $page)
                    <li class="{{ $nowPage == $page - 1 ? 'active' : ''}}"><a href="javascript:void(0)">{{ $page - 1 }}</a></li>
                @elseif($rightPage < $page - 2)
                    <li><span href="javascript:void(0)">...</span></li>
                @endif
            @endif
            @if($page > 1)
            <li class="{{ $nowPage == $page ? 'active' : ''}}"><a href="javascript:void(0)">{{ $page }}</a></li>
            @endif
            <li class="{{ $page == $nowPage ? 'disabled' : '' }}"><a data="next" href="javascript:void(0)">{{ $transDataTable['next'] or 'next' }}</a></li>
        @endif
    </ul>
</div>
<script>
    $('#pagination a').on('click', function () {
        var $this = $(this);
        var url = '<?php echo $url ?>';
        if(!$this.parent().hasClass('disabled') && !isNull(url)) {
            var take = parseInt('{{ $take }}');
            var skip = parseInt('{{ $skip }}');
            var text = $this.text().trim();
            var skipNum = 0;
            if (isUndefined($this.attr('data'))) {
                skipNum = (parseInt(text) - 1) * take;
            } else {
                var data = $this.attr('data').trim();
                if (data == 'prev') {
                    skipNum = skip - take;
                } else if (data == 'next') {
                    skipNum = skip + take;
                }
            }
            if(url.indexOf('?') > -1) {
                url += '&';
            } else {
                url += '?';
            }
            url += 'take=' + take + '&skip=' + skipNum;
            ajaxData('get', url, appendViewToContainer);
        }
    });
</script>
