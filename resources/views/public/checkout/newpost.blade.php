<div id="newpost-checkout-selection">

    <div class="order-page__form-select-wrapper">
        <select id="checkout-step__region" class="order-page__form-select" name="newpost[region]" onchange="newpostUpdate('region', jQuery(this).val());">
            <option value="0">{{ trans('app.select_area') }}</option>
            @foreach($regions as $region)
                <option value="{!! $region->id !!}">{!! $region->name !!}</option>
            @endforeach
        </select>
    </div>

    <div class="order-page__form-select-wrapper">
        <select id="checkout-step__city" class="order-page__form-select" name="newpost[city]" onchange="newpostUpdate('city', jQuery(this).val());">
            <option value="0">{{ trans('app.first_select_a_city') }}</option>
        </select>
    </div>

    <div class="order-page__form-select-wrapper">
        <select id="checkout-step__warehouse" class="order-page__form-select" name="newpost[warehouse]">
            <option value="0">{{ trans('app.first_select_a_city') }}</option>
        </select>
    </div>

    <div class="order-page__form-select-wrapper">
        <select name="payment" id="checkout-step__payment" class="order-page__form-select">
            <option disabled="" selected="">{{ trans('app.select_a_payment_method') }}</option>
            <option value="cash">{{ trans('app.cash_at_pickup') }}</option>
            <option value="prepayment">{{ trans('app.prepayment') }}</option>
        </select>
    </div>

</div>