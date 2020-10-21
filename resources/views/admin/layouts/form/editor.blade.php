@php
    if(isset($locale) && (empty($languages) || count($languages) < 2)){
        $key .= '_'.$locale;
    }
    $id = str_replace(['[', ']'], '', $key);
@endphp
<div class="form-group">
    <div class="row">
        <label class="col-sm-2 text-right{{ !empty($required) ? ' control-label' : '' }}">{{ $label }}</label>
        <div class="form-element col-sm-10">
            <div class="row">
                @if(!empty($languages) && count($languages) > 1)
                    @foreach($languages as $lang_key => $lang_name)
                        <div class="col-xs-4">
                            <div id="wp-{{ $id }}_{{ $lang_key }}-wrap" class="wp-core-ui wp-editor-wrap tmce-active">
                                <div id="wp-{{ $id }}_{{ $lang_key }}-editor-tools" class="wp-editor-tools hide-if-no-js">
                                    <div id="wp-{{ $id }}_{{ $lang_key }}-media-buttons" class="wp-media-buttons">
                                        <button type="button" id="insert-media-button" class="button insert-media add_media" data-editor="{{ $id }}_{{ $lang_key }}"><span class="wp-media-buttons-icon"></span> Добавить медиафайл</button>
                                    </div>
                                    <div class="wp-editor-tabs">
                                        <button type="button" id="{{ $id }}_{{ $lang_key }}-tmce" class="wp-switch-editor switch-tmce" data-wp-editor-id="{{ $id }}_{{ $lang_key }}">Визуально</button>
                                        <button type="button" id="{{ $id }}_{{ $lang_key }}-html" class="wp-switch-editor switch-html" data-wp-editor-id="{{ $id }}_{{ $lang_key }}">Текст</button>
                                    </div>
                                </div>
                                <div id="wp-{{ $id }}_{{ $lang_key }}-editor-container" class="wp-editor-container">
                                    <div id="qt_{{ $id }}_{{ $lang_key }}_toolbar" class="quicktags-toolbar"></div>
                                    <textarea class="wp-editor-area" rows="20" autocomplete="off" cols="40" name="{{ $key }}_{{ $lang_key }}" id="{{ $id }}_{{ $lang_key }}" placeholder="{{ $lang_name }}"{{ !empty($required) ? ' required' : '' }}>{{ old($key.'_'.$lang_key) ? old($key.'_'.$lang_key) : (isset($item) ? $item->localize($lang_key, $key) : '') }}</textarea>
                                </div>
                            </div>
                            @if($errors->has($key.'_'.$lang_key))
                                <p class="warning" role="alert">{{ $errors->first($key.'_'.$lang_key,':message') }}</p>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="col-xs-12">
                        <div id="wp-{{ $id }}-wrap" class="wp-core-ui wp-editor-wrap tmce-active">
                            <div id="wp-{{ $id }}-editor-tools" class="wp-editor-tools hide-if-no-js">
                                <div id="wp-{{ $id }}-media-buttons" class="wp-media-buttons">
                                    <button type="button" id="insert-media-button" class="button insert-media add_media" data-editor="{{ $id }}"><span class="wp-media-buttons-icon"></span> Добавить медиафайл</button>
                                </div>
                                <div class="wp-editor-tabs">
                                    <button type="button" id="{{ $id }}-tmce" class="wp-switch-editor switch-tmce" data-wp-editor-id="{{ $id }}">Визуально</button>
                                    <button type="button" id="{{ $id }}-html" class="wp-switch-editor switch-html" data-wp-editor-id="{{ $id }}">Текст</button>
                                </div>
                            </div>
                            <div id="wp-{{ $id }}-editor-container" class="wp-editor-container">
                                <div id="qt_{{ $id }}_toolbar" class="quicktags-toolbar"></div>
                                <textarea class="wp-editor-area" rows="20" autocomplete="off" cols="40" name="{{ $key }}" id="{{ $id }}"{{ !empty($required) ? ' required' : '' }}>
                                    {{ old($key) ? old($key) : (isset($locale) ? (isset($item) ? $item->localize($locale, isset($locale) ? substr($key, 0, -3) : $key) : '') : (isset($item) && !empty($item->$key) ? $item->$key : '')) }}
                                </textarea>
                            </div>
                        </div>
                        @if($errors->has($key))
                            <p class="warning" role="alert">{{ $errors->first($key,':message') }}</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>