@include('admin.layouts.header')
@extends('admin.layouts.main')
@section('title')
    Атрибуты
@endsection
@section('content')

    <h1>Редактирование атрибута</h1>

    @if(session('message-error'))
        <div class="alert alert-danger">
            {{ session('message-error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="form">
        <form method="post">
            {!! csrf_field() !!}
            <div class="panel-group">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Общая информация</h4>
                    </div>
                    <div class="panel-body">
                        @include('admin.layouts.form.string', [
                         'label' => 'Название',
                         'key' => 'name',
                         'locale' => 'ru',
                         'required' => true,
                         'item' => $attribute
                        ])
                        @include('admin.layouts.form.string', [
                         'label' => 'Слаг',
                         'key' => 'slug',
                         'item' => $attribute,
                         'languages' => null
                        ])
                        @if($errors->has('values'))
                            <p class="warning" role="alert">{!! $errors->first('values',':message') !!}</p>
                        @endif
                        <div class="form-group attribute-value">
                            <div class="row">
                                <label class="col-sm-2 text-right">Значения</label>
                                <div class="form-element col-sm-10" id="values">
                                    @if($attribute->values !== null)
                                        @foreach($attribute->values as $key => $value)
                                            @include('admin.attributes.value')
                                        @endforeach
                                    @endif
                                </div>
                                <div class="col-sm-12 text-right">
                                    <button type="button" class="btn btn-primary" id="add_attribute_value">Добавить</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12 text-right">
                                <button type="submit" class="btn">Сохранить</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function(){
            $('#add_attribute_value').click(function(e){
                e.preventDefault();
                swal({
                    title: 'Введите значение атрибута',
                    input: 'text',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    focusConfirm: false,
                    preConfirm: (name) => {
                        return new Promise((resolve, reject) => {
                            let formData = new FormData();
                            formData.append('name_{{ Config::get('app.locale') }}', name);
                            formData.append('attribute_id', {{ $attribute->id }});
                            $.ajax({
                                type:"POST",
                                url:"/admin/attributes/values/create",
                                data: formData,
                                processData: false,
                                contentType: false,
                                async:true,
                                success: function(response){
                                    if(response.result === 'success'){
                                        resolve(response.html);
                                    }else{
                                        reject(response.errors);
                                    }
                                }
                            });
                        })
                    }
                }).then(function(html) {
                    $('#values').append(html);
                }, function(errors) {
                    if(typeof errors !== 'string'){
                        var message = '';
                        for(err in errors){
                            message += errors[err] + '<br>';
                        }
                        swal(
                            'Ошибка!',
                            message,
                            'error'
                        );
                    }
                });
            });
        });

        function confirmAttributeValueDelete(id) {
            swal({
                title: 'Вы уверены?',
                text: "Это значение будет удалено!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Да, удалить его!',
                cancelButtonText: 'Нет, это ошибка.'
            }).then((result) => {
                    addPlaceholder();
                    $.post('/admin/attributes/values/delete/' + id, {}, function(response){
                        $('#value_'+id).remove();
                        removePlaceholder();
                    });
                },
                (cancel) => {}
            );
        }
    </script>
@endsection
