<div class="header" style="text-align: center;">
    <img src="{!! url('/images/logo.png') !!}" alt="logo" title="shop-sex.com.ua" width="228" height="60" />
    <p style="font-size: 20px;">Новый заказ на сайте shop-sex.com.ua!</p>
</div>

<table border="0" cellspacing="0" cellpadding="0" style="border-collapse: collapse" width="100%">
    <tbody>
        <tr style="background:#1185c2; color: #fff; text-transform:uppercase;">
            <td align="center" height="40px" width="20%">Изображение товара</td>
            <td align="center" height="40px" width="40%">Наименование товара</td>
            <td align="center" height="40px" width="20%">Количество</td>
            <td align="center" height="40px" width="20%">Цена</td>
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
                        {!! $item['product']->price * $item['quantity'] !!} грн
                    </td>
                </tr>
            @endforeach
        <tr>
            <td colspan="4" height="30px" align="right"><p style="font-size:16px;"><strong>Количество:</strong> {!! $order->total_quantity !!}</p></td>
        </tr>
        <tr>
            <td colspan="4" height="30px" align="right"><p style="font-size:16px;"><strong>Стоимость:</strong> {!! $order->total_price !!} грн</p></td>
        </tr>
    </tbody>
</table>

@if($admin)
    <p><strong>Телефон:</strong> {!! $user['phone'] !!}</p>
    <p><strong>Комментарий:</strong> {!! $user['comment] !!}</p>
@else
    <p style="font-size: 16px; color: #333;">Благодарим Вас за заказ в интернет-магазине shop-sex.com.ua! В ближайшее время с Вами свяжется наш менеджер для уточнения деталей заказа!</p>
@endif

<p style="font-size:16px; color: #333;">Информация о доставке:</p>

@foreach($order->getDeliveryInfo() as $key => $value)
    @if($key == 'method') <p><strong>Способ доставки: </strong>{!! $value !!}</p> @endif
    @if($key == 'region') <p><strong>Область: </strong>{!! $value !!}</p> @endif
    @if($key == 'city') <p><strong>Город: </strong>{!! $value !!}</p> @endif
    @if($key == 'warehouse') <p><strong>Отделение Новой Почты: </strong>{!! $value !!}</p> @endif
    @if($key == 'index') <p><strong>Почтовый индекс: </strong>{!! $value !!}</p> @endif
    @if($key == 'street') <p><strong>Улица: </strong>{!! $value !!}</p> @endif
    @if($key == 'house') <p><strong>Дом: </strong>{!! $value !!}</p> @endif
    @if($key == 'apart') <p><strong>Квартира: </strong>{!! $value !!}</p> @endif
    @if($key == 'error') <p><strong>{!! $value !!}</strong></p> @endif
@endforeach

@if($order->payment == 'cash')
    <p style="font-size:16px; color: #333;"><strong>Оплата: </strong>Наличными при доставке</p>
@elseif($order->payment == 'prepayment')
    <p style="font-size:16px; color: #333;"><strong>Оплата: </strong>Предоплата</p>
@elseif($order->payment == 'privat')
    <p style="font-size:16px; color: #333;"><strong>Оплата: </strong>На расчетный счет Приват Банка</p>
@elseif($order->payment == 'nal_delivery')
    <p style="font-size:16px; color: #333;"><strong>Оплата: </strong>Наличными курьеру</p>
@elseif($order->payment == 'nal_samovivoz')
    <p style="font-size:16px; color: #333;"><strong>Оплата: </strong>Оплата при самовывозе</p>
@elseif($order->payment == 'nalogenniy')
    <p style="font-size:16px; color: #333;"><strong>Оплата: </strong>Оплата наложенным платежом</p>
@endif