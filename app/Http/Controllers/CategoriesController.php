<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\Attribute;
use App\ProductAttribute;
use Illuminate\Http\Request;
use App\Models\Categories;
use App\Models\Filter;
use Validator;
use Config;

class CategoriesController extends Controller
{

    private $rules = [
        'name_ru' => 'required',
        'url_alias' => 'required|unique:categories',
    ];

    private $messages = [
        'name_ru.required' => 'Поле должно быть заполнено!',
        'meta_title.required' => 'Поле должно быть заполнено!',
        'url_alias.required' => 'Поле должно быть заполнено!',
        'url_alias.unique' => 'Значение должно быть уникальным для каждой категории!'
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.categories.index')->with('categories', Categories::paginate(20));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.categories.create')
            ->with('categories', Categories::all())
            ->with('attributes', Attribute::all())
            ->with('languages', Config::get('app.locales_names'))
            ->with('editors', localizationFields(['description']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Categories $categories)
    {

        $validator = Validator::make($request->all(), $this->rules, $this->messages);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->with('message-error', 'Сохранение не удалось! Проверьте форму на ошибки!')
                ->withErrors($validator);
        }

        $categories->fill($request->except('_token'));
        $categories->description = !empty($request->description) ? $request->description : null;
        $categories->sort_order = !empty($request->sort_order) ? $request->sort_order : 0;
        $categories->save();
        $categories->saveLocalization($request);

        return redirect('/admin/categories')
            ->with('message-success', 'Категория ' . $categories->name . ' успешно добавлена.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Categories::find($id);
        $attributes = [];
        if(!empty($category->attributes)) {
            foreach ($category->attributes as $attribute){
                $attributes[] = $attribute->id;
            }
        }

        return view('admin.categories.edit')
            ->with('attributes', Attribute::all())
            ->with('related_attributes', $attributes)
            ->with('category', $category)
            ->with('categories', Categories::all())
            ->with('languages', Config::get('app.locales_names'))
            ->with('editors', localizationFields(['description']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, Categories $categories)
    {

        $rules = $this->rules;
        $rules['url_alias'] = 'required|unique:categories,url_alias,'.$id;

        $validator = Validator::make($request->all(), $rules, $this->messages);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->with('message-error', 'Сохранение не удалось! Проверьте форму на ошибки!')
                ->withErrors($validator);
        }

        $category = $categories->find($id);
        $category->fill($request->except('_token'));
        $category->description = !empty($request->description) ? $request->description : null;
        $category->sort_order = !empty($request->sort_order) ? $request->sort_order : 0;
        $category->save();
        $category->saveLocalization($request);

        $category->attributes()->sync($request->related_attribute_ids);

        return redirect('/admin/categories')
            ->with('message-success', 'Категория ' . $category->name . ' успешно обновлена.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Categories::find($id);
        $category->delete();

        return redirect('/admin/categories')
            ->with('message-success', 'Категория ' . $category->name . ' успешно удалена.');
    }

    public function catalog(Categories $categories){
        $root_categories = $categories->get_root_categories();

        return view('public.catalog')
            ->with('categories', $root_categories);
    }

    /**
     * Страница категории
     *
     * @param $request
     * @param null $alias
     * @param string $filters
     * @return mixed
     */
    public function show(Request $request, Filter $filter, $alias = null, $filters = '', $page = 'page1')
    {
        $categories = new Categories;

        // Если нет фильтров
        if(strpos($filters, 'page') === 0 && $page == 'page1'){
            $page = $filters;
            $filters = '';
        }

        // Сео редирект для первой страницы
        if($page == 1){
            return redirect()->action(
                'CategoriesController@show', ['request' => $request, 'alias' => $alias, 'filters' => $filters], 301
            );
        }

        // Установка текущей страницы пагинации
        $request->page = (int) str_replace('page', '', $page);

        // Получение текущей категории
        if($alias == null)
            $category = null;
        else
            $category = $categories->where('url_alias', $alias)->first();

        if($category === null)
            abort(404);

        // Установка параметров фильтрации
        $filter->setCategory($category)->setRequest($request->toArray())->setFilterPath($filters);

        $hash = md5($alias.serialize($request->toArray()).serialize($filters));
        $attributes = Cache::remember('attributes_'.$hash, 480, function () use ($filter) {
            return $filter->getFilterAttributes();
        });

        $orders = [
            'date-desc' => ['id', 'desc'],
            'price-asc' => ['price', 'asc'],
            'price-desc' => ['price', 'desc'],
        ];

        return view('public.category')
            ->with('category', $category)
            ->with('products', $filter->getProducts(isset($orders[$request->order]) ? $orders[$request->order] : ['price', 'asc'], 18, $request->page))
            ->with('attributes', $attributes)
            ->with('price', $filter->getPriceSlider());
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function brands(Attribute $attribute){
        $brands = $attribute->where('name', 'Бренд')->first()->values;
        return view('public.brands')
            ->with('brands', $brands);
    }
}
