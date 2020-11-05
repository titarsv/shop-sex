<div class="form-group">
    <div class="row">
        <label class="col-sm-2 text-right{{ !empty($required) ? ' control-label' : '' }}">{{ $label }}</label>
        <div class="form-element col-sm-10">
            <div class="row">
                @if(!empty($languages) && count($languages) > 1 && (!isset($item) || (!is_array($item->localized_fields) || in_array($key, $item->localized_fields))))
                    @foreach($languages as $lang_key => $lang_name)
                        <div class="col-xs-4">
                            <input type="text" class="form-control" name="{{ $key }}_{{ $lang_key }}" value="{{ old($key.'_'.$lang_key) ? old($key.'_'.$lang_key) : (isset($item) ? $item->localize($lang_key, $key) : '') }}" placeholder="{{ $lang_name }}"{{ !empty($required) ? ' required' : '' }} />
                            @if($errors->has($key.'_'.$lang_key))
                                <p class="warning" role="alert">{{ $errors->first($key.'_'.$lang_key,':message') }}</p>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="col-xs-12">
                        <input type="text" class="form-control" name="{{ $key }}{{ isset($locale) ? '_'.$locale : '' }}"
                            value="{{ old($key) ? old($key) : (isset($locale) ? (isset($item) ? $item->localize($locale, $key) : '') : (isset($item) && !empty($item->$key) ? $item->$key : '')) }}"
                            {{ !empty($required) ? ' required' : '' }} />
                        @if($errors->has($key))
                            <p class="warning" role="alert">{{ $errors->first($key,':message') }}</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>