<div class="order-page__form-select-wrapper">
    <select name="payment" id="checkout-step__payment" class="order-page__form-select">
        <option disabled="" selected="">{{ trans('app.select_a_payment_method') }}</option>
        <option value="cash">{{ trans('app.cash_at_pickup') }}</option>
        <option value="prepayment">{{ trans('app.prepayment') }}</option>
    </select>
</div>