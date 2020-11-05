<div class="form-group">
    <div class="row">
        <label class="col-sm-2 text-right{{ !empty($required) ? ' control-label' : '' }}">{{ $label }}</label>
        <div class="form-element col-sm-10">
            <select name="{{ $key }}{{ !empty($multiple) ? '[]' : '' }}" autocomplete="off" class="form-control chosen-select"{{ !empty($multiple) ? ' multiple="multiple"' : '' }}>
                @foreach($options as $option)
                    <option value="{{ $option->id }}"
                            @if(!empty(old($key)))
                                @if(in_array($option->id, (array)old($key)))
                                selected
                                @endif
                            @elseif(in_array($option->id, $selected))
                            selected
                            @endif
                    >{{ $option->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>