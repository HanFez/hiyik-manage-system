<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2019/6/6
 * Time: 11:31
 */
$url = 'cartList';
$total = isset($result->total) ? $result->total : 0;
$skip  = isset($result->skip) ? $result->skip : 0;
$take  = isset($result->take) ? $result->take : 6;
$transDataTable = trans('dataTable');
?>
@extends('layout/widget')

@section('title')
    购物车列表
@stop
@section('content')
    <div class="data-list clearfix">
        @if($result->data->isEmpty())
            {{ $transDataTable['zeroRecords'] or 'zero records' }}
        @else
            <ul class="recent-posts">
                @foreach($result->data as $cart)
                    <li>
                        <div class="article-post">
                            <div class="fr">
                                <a name="cart-detail" href="javascript:void(0)" data="{{ $cart->id }}" class="btn btn-primary">
                                    查看购物车产品
                                </a>
                            </div>
                            <p class="article-title">
                                购物车拥有人:
                                @if(!is_null($cart->person))
                                    <a name="show-person" href="javascript:void(0)" data="{{ $cart->person_id }}">
                                        @if(!$cart->person->personNick->isEmpty())
                                            {{ $cart->person->personNick[0]->nick->nick }}
                                        @endif
                                    </a>
                                    @endif
                            </p>
                            <p class="article-content">
                            <div>
                                创建时间: <span data-time="utc">{{ $cart->created_at }}</span>
                            </div>
                            </p>
                        </div>
                    </li>
                @endforeach
            </ul>
            @include('layout/pagination')
        @endif
    </div>
    <script>
        bindEventToFilterButton({
            'type': 'advice',
            'take': '{{ $take }}'
        })
        $('a[name="show-person"]').on('click', bindEventToShowPublicationAuthor);
        $('a[name="cart-detail"]').on('click', function () {
            var $this = $(this);
            var id = $this.attr('data').trim();
            ajaxData('get','cart/'+id+'?take='+'{{$take}}'+'&skip='+'{{$skip}}',appendViewToContainer);
        });
    </script>
@stop