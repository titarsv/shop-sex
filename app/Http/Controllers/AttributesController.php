<?php

namespace App\Http\Controllers;

use App\Models\AttributeValues;
use App\Models\ProductAttributes;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Attribute;
use Validator;
use Config;

class AttributesController extends Controller
{
    private $rules = [
        'name_ru' => 'required'
    ];

    private $messages = [
        'name_ru.required' => 'Поле должно быть заполнено!',
        'values.*.distinct' => 'Значения одинаковы!',
        'values.*.filled' => 'Поле должно быть заполнено!',
        'max_quantity.required_if' => 'Поле должно быть заполнено!',
        'image_width.numeric' => 'Значение должно быть числовым!',
        'image_height.numeric' => 'Значение должно быть числовым!',
        'max_quantity.numeric' => 'Значение должно быть числовым!',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.attributes.index')
            ->with('attributes', Attribute::paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Attribute $attributes){
        $validator = Validator::make($request->all(), ['name_'.Config::get('app.locale') => 'required'], ['name_'.Config::get('app.locale').'.required' => 'Поле должно быть заполнено!']);

        if($validator->fails()){
            return response()->json($validator);
        }

        $name_key = 'name_'.Config::get('app.locale');

        $id = $attributes->insertGetId(['slug' => Str::slug(str_replace(['-', '_', ' '], '', mb_strtolower(translit($request->$name_key))))]);
        $attribute = $attributes->find($id);
        $attribute->saveLocalization($request);

        $attribute->load('localization');

        return response()->json(['result' => 'success', 'redirect' => '/admin/attributes/edit/'.$id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){
        $attribute = Attribute::find($id);

        return view('admin.attributes.edit')
            ->with('languages', Config::get('app.locales_names'))
            ->with('attribute', $attribute);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        $rules = $this->rules;
        $rules['values.*.name_ru'] = 'distinct|filled';
        $rules['values.*.value'] = 'distinct|filled';

        $validator = Validator::make($request->all(), $rules, $this->messages);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->with('message-error', 'Сохранение не удалось! Проверьте форму на ошибки!')
                ->withErrors($validator);
        }

        $attribute = Attribute::find($id);

        $attribute->fill($request->only(['slug']));
        $attribute->saveLocalization($request);
        $attribute->save();

        foreach ($request->values as $attribute_value_id => $value){
            $attribute_value = AttributeValues::find($attribute_value_id);
            $attribute_value->value = str_replace(['-', '_', ' '], '',$value['value']);
            $attribute_value->save();
            $new_request = new Request();
            $new_request->merge([
                'name_ru' => $value['name_ru'],
                'name_ua' => $value['name_ua'],
                'name_en' => $value['name_en']
            ]);
            $attribute_value->saveLocalization($new_request);
        }

        return redirect('/admin/attributes')
            ->with('message-success', 'Атрибут ' .$attribute->name . ' успешно обновлен.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $attribute = Attribute::find($id);
        $attribute->delete();
        $attribute->values()->delete();
        ProductAttribute::where('attribute_id', $id)->delete();

        return redirect('/admin/attributes')
            ->with('message-success', 'Атрибут ' .$attribute->name . ' успешно удален.');
    }

    /**
     * Загрузка изображений аттрибута
     * @param Request $request
     */
//    public function upload_image(Request $request)
//    {
//        if($request->file('attribute_image')) {
//            $file = $request->file('attribute_image');
//            $newFileName = str_random(10) . '.' . $file->guessExtension();
//            $destinationPath = public_path() . '\assets\attributes_images';
//            $file->move($destinationPath, $newFileName);
//
//            return response()->json(['href' => $newFileName]);
//        }
//    }

    /**
     * Создание значения атрибута
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function adminStoreValueAction(Request $request){
        $name_key = 'name_'.Config::get('app.locale');

        $attribute_value = new AttributeValues;

        $id = $attribute_value->insertGetId([
            'attribute_id' => $request->attribute_id,
            'value' => str_replace(['-', '_', ''], '', mb_strtolower(translit($request->$name_key)))
        ]);
        $value = $attribute_value->find($id);

        $value->saveLocalization($request);

        return response()->json(['result' => 'success', 'html' => view('admin.attributes.value')->with('value', $value)->render()]);
    }

    /**
     * Удаление значения атрибута
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function adminDestroyValueAction($id){
        $value = AttributeValues::find($id);

        ProductAttributes::where('attribute_value_id', $id)->delete();

        $value->delete();

        return response()->json(['result' => 'success']);
    }
}
