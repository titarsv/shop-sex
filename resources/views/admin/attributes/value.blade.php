<div class="row form-group" id="value_{{ $value->id }}">
    <div class="col-xs-5 attribute-name">
        <input type="text" name="values[{{ $value->id }}][name_ru]" class="form-control" value="{{ $value->name }}" placeholder="На русском" />
    </div>
    <div class="col-xs-5 attribute-name">
        <input type="text" name="values[{{ $value->id }}][value]" class="form-control" value="{{ $value->value }}" placeholder="Значение" />
    </div>
    <div class="col-xs-2 text-center">
        <button type="button" class="btn btn-danger" onclick="confirmAttributeValueDelete({{ $value->id }});"><i class="glyphicon glyphicon-trash"></i></button>
    </div>
    <div class="col-xs-5 attribute-name">
        <input type="text" name="values[{{ $value->id }}][name_ua]" class="form-control" value="{{ $value->localize('ua', 'name') }}" placeholder="Українською" />
    </div>
    <div class="col-xs-5 attribute-name">
        <input type="text" name="values[{{ $value->id }}][name_en]" class="form-control" value="{{ $value->localize('en', 'name') }}" placeholder="In English" />
    </div>
</div>