<div class="imgedit-wrap wp-clearfix">
    <div id="imgedit-panel-{{ $image->id }}">
        <div class="imgedit-settings">
            <div class="imgedit-group">
                <div class="imgedit-group-top">
                    <h2>Масштабировать</h2>
                    <button type="button" class="dashicons dashicons-editor-help imgedit-help-toggle" onclick="imageEdit.toggleHelp(this);return false;" aria-expanded="false"><span class="screen-reader-text">Подсказка по масштабированию</span></button>
                    <div class="imgedit-help">
                        <p>Можно изменить размер исходного изображения с сохранением пропорций. Для получения наилучших результатов масштабирование следует выполнять до обрезки, отражения и поворота. Изображения можно уменьшить, но не увеличить.</p>
                    </div>
                    <p>Исходный размер 84 &times; 52</p>
                    <div class="imgedit-submit">

                        <fieldset class="imgedit-scale">
                            <legend>Новый размер:</legend>
                            <div class="nowrap">
                                <label><span class="screen-reader-text">ширина для масштабирования</span>
                                    <input type="text" id="imgedit-scale-width-{{ $image->id }}" onkeyup="imageEdit.scaleChanged({{ $image->id }}, 1, this)" onblur="imageEdit.scaleChanged({{ $image->id }}, 1, this)" value="84" />
                                </label>
                                <span class="imgedit-separator">&times;</span>
                                <label><span class="screen-reader-text">высота для масштабирования</span>
                                    <input type="text" id="imgedit-scale-height-{{ $image->id }}" onkeyup="imageEdit.scaleChanged({{ $image->id }}, 0, this)" onblur="imageEdit.scaleChanged({{ $image->id }}, 0, this)" value="52" />
                                </label>
                                <span class="imgedit-scale-warn" id="imgedit-scale-warn-{{ $image->id }}">!</span>
                                <input id="imgedit-scale-button" type="button" onclick="imageEdit.action({{ $image->id }}, '{{ $nonce }}', 'scale')" class="button button-primary" value="Масштабировать" />
                            </div>
                        </fieldset>

                    </div>
                </div>
            </div>
            <div class="imgedit-group">
                <div class="imgedit-group-top">
                    <h2>Обрезать</h2>
                    <button type="button" class="dashicons dashicons-editor-help imgedit-help-toggle" onclick="imageEdit.toggleHelp(this);return false;" aria-expanded="false"><span class="screen-reader-text">Подсказка по обрезанию</span></button>

                    <div class="imgedit-help">
                        <p>Чтобы обрезать изображение, нажмите на него и выделите нужную часть, удерживая кнопку мыши.</p>

                        <p><strong>Пропорции области</strong><br />
                            Пропорции &#8212; это соотношение ширины и высоты. При изменении размера выделенной области можно сохранить пропорции, зажав Shift. Укажите желаемые пропорции в полях ниже, например 1:1 (квадрат), 4:3, 16:9 и т.д.</p>

                        <p><strong>Размер области</strong><br />
                            Выделив область, можно её отрегулировать, указав размер в пикселях. Минимальный размер области равен размеру миниатюры, заданному на странице &laquo;Настройки медиафайлов&raquo;.</p>
                    </div>
                </div>

                <fieldset class="imgedit-crop-ratio">
                    <legend>Пропорции:</legend>
                    <div class="nowrap">
                        <label><span class="screen-reader-text">ширина для обрезания</span>
                            <input type="text" id="imgedit-crop-width-{{ $image->id }}" onkeyup="imageEdit.setRatioSelection({{ $image->id }}, 0, this)" onblur="imageEdit.setRatioSelection({{ $image->id }}, 0, this)" />
                        </label>
                        <span class="imgedit-separator">:</span>
                        <label><span class="screen-reader-text">высота для обрезания</span>
                            <input type="text" id="imgedit-crop-height-{{ $image->id }}" onkeyup="imageEdit.setRatioSelection({{ $image->id }}, 1, this)" onblur="imageEdit.setRatioSelection({{ $image->id }}, 1, this)" />
                        </label>
                    </div>
                </fieldset>

                <fieldset id="imgedit-crop-sel-{{ $image->id }}" class="imgedit-crop-sel">
                    <legend>Размер:</legend>
                    <div class="nowrap">
                        <label><span class="screen-reader-text">ширина выделения</span>
                            <input type="text" id="imgedit-sel-width-{{ $image->id }}" onkeyup="imageEdit.setNumSelection({{ $image->id }}, this)" onblur="imageEdit.setNumSelection({{ $image->id }}, this)" />
                        </label>
                        <span class="imgedit-separator">&times;</span>
                        <label><span class="screen-reader-text">высота выделения</span>
                            <input type="text" id="imgedit-sel-height-{{ $image->id }}" onkeyup="imageEdit.setNumSelection({{ $image->id }}, this)" onblur="imageEdit.setNumSelection({{ $image->id }}, this)" />
                        </label>
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="imgedit-panel-content wp-clearfix">
            <div class="imgedit-menu wp-clearfix">
                <button type="button" onclick="imageEdit.crop({{ $image->id }}, '{{ $nonce }}', this)" class="imgedit-crop button disabled" disabled><span class="screen-reader-text">Обрезать</span></button>			<button type="button" class="imgedit-rleft button" onclick="imageEdit.rotate( 90, {{ $image->id }}, '{{ $nonce }}', this)"><span class="screen-reader-text">Повернуть против часовой стрелки</span></button>
                <button type="button" class="imgedit-rright button" onclick="imageEdit.rotate(-90, {{ $image->id }}, '{{ $nonce }}', this)"><span class="screen-reader-text">Повернуть по часовой стрелке</span></button>

                <button type="button" onclick="imageEdit.flip(1, {{ $image->id }}, '{{ $nonce }}', this)" class="imgedit-flipv button"><span class="screen-reader-text">Отразить по вертикали</span></button>
                <button type="button" onclick="imageEdit.flip(2, {{ $image->id }}, '{{ $nonce }}', this)" class="imgedit-fliph button"><span class="screen-reader-text">Отразить по горизонтали</span></button>

                <button type="button" id="image-undo-{{ $image->id }}" onclick="imageEdit.undo({{ $image->id }}, '{{ $nonce }}', this)" class="imgedit-undo button disabled" disabled><span class="screen-reader-text">Отменить</span></button>
                <button type="button" id="image-redo-{{ $image->id }}" onclick="imageEdit.redo({{ $image->id }}, '{{ $nonce }}', this)" class="imgedit-redo button disabled" disabled><span class="screen-reader-text">Повторить</span></button>
            </div>
            <input type="hidden" id="imgedit-sizer-{{ $image->id }}" value="1" />
            <input type="hidden" id="imgedit-history-{{ $image->id }}" value="" />
            <input type="hidden" id="imgedit-undone-{{ $image->id }}" value="0" />
            <input type="hidden" id="imgedit-selection-{{ $image->id }}" value="" />
            <input type="hidden" id="imgedit-x-{{ $image->id }}" value="84" />
            <input type="hidden" id="imgedit-y-{{ $image->id }}" value="52" />

            <div id="imgedit-crop-{{ $image->id }}" class="imgedit-crop-wrap">
                <img id="image-preview-{{ $image->id }}" onload="imageEdit.imgLoaded('{{ $image->id }}')" src="/admin/ajax?action=imgedit-preview&_ajax_nonce={{ $nonce }}&postid={{ $image->id }}&rand=100000" alt="" />
            </div>
            <div class="imgedit-submit">
                <input type="button" onclick="imageEdit.close({{ $image->id }}, 1)" class="button imgedit-cancel-btn" value="Отмена" />
                <input type="button" onclick="imageEdit.save({{ $image->id }}, '{{ $nonce }}')" disabled="disabled" class="button button-primary imgedit-submit-btn" value="Сохранить" />
            </div>
        </div>
    </div>
    <div class="imgedit-wait" id="imgedit-wait-{{ $image->id }}"></div>
    <div class="hidden" id="imgedit-leaving-{{ $image->id }}">Несохранённые изменения будут потеряны. «ОК» — продолжить, «Отмена» — вернуться в редактор изображений.</div>
</div>
