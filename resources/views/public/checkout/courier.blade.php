<div id="courier-checkout-selection">
    <div class="row">
        <div class="col-sm-4">
            <label for="checkout-step__city" class="checkout-step__label checkout-step__del-info">{{ trans('app.the_address') }}</label>
        </div>
        <div class="col-sm-8">
            <div class="checkout-step__del-info clearfix">
                <input type="text" class="checkout-step__del-info-input checkout-step__del-info-input_long" name="courier[street]" placeholder="{{ trans('app.the_street') }}">
                <input type="text" class="checkout-step__del-info-input checkout-step__del-info-input_short" name="courier[house]" placeholder="{{ trans('app.house') }}">
                <input type="text" class="checkout-step__del-info-input checkout-step__del-info-input_short" name="courier[apart]" placeholder="{{ trans('app.apt') }}">
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-4">
        <label for="checkout-step__payment" class="checkout-step__label">{{ trans('app.payment_method') }}</label>
    </div>
    <div class="col-sm-8">
        <select name="payment" id="checkout-step__payment" class="checkout-step__select">
            <option value="cash">{{ trans('app.cash_upon_receipt') }}</option>
            <option value="prepayment">{{ trans('app.prepayment') }}</option>
        </select>
    </div>
</div>