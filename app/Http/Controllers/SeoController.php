<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seo;
use Validator;


class SeoController extends Controller
{

    private $rules = [
        'name' => 'required',
        'url' => 'required|unique:seo',
    ];

    private $messages = [
        'name.required' => 'Поле должно быть заполнено!',
        'meta_title.required' => 'Поле должно быть заполнено!',
        'url.required' => 'Поле должно быть заполнено!',
        'url.unique' => 'Значение должно быть уникальным для каждой записи!'
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.seo.index')->with('seo', Seo::paginate(20));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.seo.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Seo $seo)
    {

        $validator = Validator::make($request->all(), $this->rules, $this->messages);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->with('message-error', 'Сохранение не удалось! Проверьте форму на ошибки!')
                ->withErrors($validator);
        }

        $seo->fill($request->except('_token'));
        $seo->description = !empty($request->description) ? $request->description : null;
        $seo->save();

        return redirect('/admin/seo/list')
            ->with('message-success', 'Запись ' . $seo->name . ' успешно добавлена.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $seo = Seo::find($id);

        return view('admin.seo.edit')
            ->with('seo', $seo);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, Seo $seo)
    {

        $rules = $this->rules;
        $rules['url'] = 'required|unique:seo,url,'.$id;

        $validator = Validator::make($request->all(), $rules, $this->messages);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->with('message-error', 'Сохранение не удалось! Проверьте форму на ошибки!')
                ->withErrors($validator);
        }

        $seo = $seo->find($id);
        $seo->fill($request->except('_token'));
        $seo->description = !empty($request->description) ? $request->description : null;
        $seo->save();

        return redirect('/admin/seo/list')
            ->with('message-success', 'Запись ' . $seo->name . ' успешно обновлена.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Seo::find($id);
        $category->delete();

        return redirect('/admin/seo/list')
            ->with('message-success', 'Запись ' . $category->name . ' успешно удалена.');
    }
}
