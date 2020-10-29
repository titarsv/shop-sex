<div id="ukrpost-checkout-selection">
    <div class="row">
        <div class="col-sm-4">
            <label for="checkout-step__city" class="checkout-step__label checkout-step__del-info">{{ trans('app.the_address') }}</label>
        </div>
        <div class="col-sm-8">
            <div class="checkout-step__del-info clearfix">
                <input type="text" class="checkout-step__del-info-input checkout-step__del-info-input_long" style="width: 100%;" name="ukrpost[region]" placeholder="{{ trans('app.region') }}" />
            </div>
            <div class="checkout-step__del-info clearfix">
                <input type="text" class="checkout-step__del-info-input checkout-step__del-info-input_long" name="ukrpost[city]" placeholder="{{ trans('app.city') }}" />
                <input type="text" class="checkout-step__del-info-input checkout-step__del-info-input_medium" name="ukrpost[index]" placeholder="{{ trans('app.index') }}">
            </div>
            <div class="checkout-step__del-info clearfix">
                <input type="text" class="checkout-step__del-info-input checkout-step__del-info-input_long" name="ukrpost[street]" placeholder="{{ trans('app.the_street') }}">
                <input type="text" class="checkout-step__del-info-input checkout-step__del-info-input_short" name="ukrpost[house]" placeholder="{{ trans('app.house') }}">
                <input type="text" class="checkout-step__del-info-input checkout-step__del-info-input_short" name="ukrpost[apart]" placeholder="{{ trans('app.apt') }}">
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
            <option value="prepayment">{{ trans('app.prepayment') }}</option>
        </select>
    </div>
</div>