<div class="header" style="text-align: center;">
    <img src="{!! url('/images/logo.png') !!}" alt="logo" title="shop-sex.com.ua" width="228" height="60" />
    <p style="font-size: 20px;">{{ trans('app.new_order_on_the_site') }} shop-sex.com.ua!</p>
</div>

<table border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse" width="100%">
    <tbody>
        <tr style="background:#1185c2; color: #fff; text-transform:uppercase;">
            <td align="center" height="40px" width="20%">{{ trans('app.product_picture') }}</td>
            <td align="center" height="40px" width="40%">{{ trans('app.name_of_product') }}</td>
            <td align="center" height="40px" width="20%">{{ trans('app.number') }}</td>
            <td align="center" height="40px" width="20%">{{ trans('app.price') }}</td>
        </tr>
            @foreach($order->getProducts() as $item)
                <tr>
                    <td align="center" width="20%" height="150px">
                        <a href="{!! url('/product/' . $item['product']->url_alias) !!}">
                            <img src="{!! url($item['product']->image->url('product_list')) !!}" alt="product-image" width="100px" height="100px" title="{!! $item['product']->name !!}">
                        </a>
                    </td>
                    <td align="center" width="40%" height="150px">
                        <a href="{!! url('/product/' . $item['product']->url_alias) !!}" style="color: #333;" onmouseover="this.style.color='#333'">{!! $item['product']->name !!}</a>
                    </td>
                    <td align="center" width="20%" height="150px">
                        {!! $item['quantity'] !!}
                    </td>
                    <td align="center" width="20%" height="150px">
                        {!! $item['product']->price * $item['quantity'] !!} {{ trans('app.hryvnias') }}
                    </td>
                </tr>
            @endforeach
        <tr>
            <td colspan="4" height="30px" align="right"><p style="font-size:16px;"><strong>{{ trans('app.number') }}:</strong> {!! $order->total_quantity !!}</p></td>
        </tr>
        <tr>
            <td colspan="4" height="30px" align="right"><p style="font-size:16px;"><strong>{{ trans('app.cost') }}</strong> {!! $order->total_price !!} {{ trans('app.hryvnias') }}</p></td>
        </tr>
    </tbody>
</table>

@if($admin)
    @if($order->payment == 'cash')
        <p style="font-size:16px; color: #333;"><strong>{{ trans('app.payment') }}: </strong>{{ trans('app.cash_on_delivery') }}</p>
    @elseif($order->payment == 'prepayment')
        <p style="font-size:16px; color: #333;"><strong>{{ trans('app.payment') }}: </strong>{{ trans('app.prepayment') }}</p>
    @elseif($order->payment == 'card')
        <p style="font-size:16px; color: #333;"><strong>{{ trans('app.payment') }}: </strong>{{ trans('app.to_the_settlement_account_of_privat_bank') }}</p>
    @elseif($order->payment == 'nal_delivery')
        <p style="font-size:16px; color: #333;"><strong>{{ trans('app.payment') }}: </strong>{{ trans('app.cash_to_the_courier') }}</p>
    @elseif($order->payment == 'nal_samovivoz')
        <p style="font-size:16px; color: #333;"><strong>{{ trans('app.payment') }}: </strong>{{ trans('app.payment_upon_pickup') }}</p>
    @elseif($order->payment == 'nalogenniy')
        <p style="font-size:16px; color: #333;"><strong>{{ trans('app.payment') }}: </strong>{{ trans('app.Cash_on_delivery') }}</p>
    @endif
    <p><strong>{{ trans('app.phone') }}:</strong> {!! $user['phone'] !!}</p>
    <p><strong>{{ trans('app.comment') }}:</strong> {!! $user['comment'] !!}</p>
@else
    <p style="font-size: 16px; color: #333;">{{ trans('app.thank_you_for_ordering_in_the_online_store_shop-sexcomua_our_manager_will_contact_you_shortly_to_clarify_the_details_of_the_order') }}</p>
@endif