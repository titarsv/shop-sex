<div class="form-group">
    <div class="row">
        <label class="col-sm-2 text-right{{ !empty($required) ? ' control-label' : '' }}">{{ $label }}</label>
        <div class="form-element col-sm-10">
            <div class="row">
                @if(!empty($languages) && count($languages) > 1)
                    @foreach($languages as $lang_key => $lang_name)
                        <div class="col-xs-4">
                            <textarea class="form-control" rows="6" autocomplete="off" name="{{ $key }}_{{ $lang_key }}" placeholder="{{ $lang_name }}"{{ !empty($required) ? ' required' : '' }}>{{ old($key.'_'.$lang_key) ? old($key.'_'.$lang_key) : (isset($item) ? $item->localize($lang_key, $key) : '') }}</textarea>
                            @if($errors->has($key.'_'.$lang_key))
                                <p class="warning" role="alert">{{ $errors->first($key.'_'.$lang_key,':message') }}</p>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="col-xs-12">
                        <textarea class="form-control" rows="6" autocomplete="off" name="{{ $key }}{{ isset($locale) ? '_'.$locale : '' }}"{{ !empty($required) ? ' required' : '' }}>
                            {{ old($key) ? old($key) : (isset($locale) ? (isset($item) ? $item->localize($locale, $key) : '') : (isset($item) && !empty($item->$key) ? $item->$key : '')) }}
                        </textarea>
                        @if($errors->has($key))
                            <p class="warning" role="alert">{{ $errors->first($key,':message') }}</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>