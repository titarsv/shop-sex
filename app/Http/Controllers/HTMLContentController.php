<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HTMLContent;
use App\Models\News;
use App;
use Validator;
use Config;

class HTMLContentController extends Controller
{
    protected $rules = [
        'name_ru' => 'required',
        'url_alias' => 'required|unique:html_content'
    ];
    protected $messages = [
        'name_ru.required' => 'Поле должно быть заполнено!',
        'url_alias.required' => 'Поле должно быть заполнено!',
        'url_alias.unique' => 'Значение должно быть уникальным!'
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(HTMLContent $content){
        return view('admin.htmlcontent.index')
            ->with('content', $content->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, HTMLContent $pages){
        $validator = Validator::make($request->all(), ['name_'.Config::get('app.locale') => 'required'], ['name_'.Config::get('app.locale').'.required' => 'Поле должно быть заполнено!']);

        if($validator->fails()){
            return response()->json($validator);
        }

        $id = $pages->insertGetId(['parent_id' => 0, 'content' => '', 'status' => 0, 'sort_order' => 0]);
        $page = $pages->find($id);
        $page->saveLocalization($request);

        return response()->json(['result' => 'success', 'redirect' => '/admin/html/edit/'.$id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, HTMLContent $content){
        $page = $content->find($id);
        $page->content = html_entity_decode($page->content);
        return view('admin.htmlcontent.edit')
            ->with('pages', $content->all())
            ->with('content', $page)
            ->with('languages', Config::get('app.locales_names'))
            ->with('editors', localizationFields(['content']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = $this->rules;
        $rules['url_alias'] = 'required|unique:html_content,url_alias,'.$id;

        $validator = Validator::make($request->all(), $rules, $this->messages);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->with('message-error', 'Сохранение не удалось! Проверьте форму на ошибки!')
                ->withErrors($validator);
        }

        $content = HTMLContent::find($id);
        $content->fill($request->except('_token'));
        $content->sort_order = !empty($request->sort_order) ? $request->sort_order : 0;
        $content->save();
        $content->saveLocalization($request);

        return redirect('/admin/html')
            ->with('content', $content->paginate(10))
            ->with('message-success', 'Страница ' . $content->name . ' успешно обновлена.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $content = HTMLContent::find($id);
        $content->delete();

        return redirect('/admin/html')
            ->with('products', $content->paginate(10))
            ->with('message-success', 'Страница ' . $content->name . ' успешно удалена.');
    }

    public function show($alias)
    {
        $content = HTMLContent::where('url_alias', $alias)->first();

        $blog = new News();

        $news = $blog->where('published', 1)->where('category', 'Новости и акции')->orderBy('updated_at', 'desc')->paginate(12);
        $articles = $blog->where('published', 1)->where('category', 'Статьи')->orderBy('updated_at', 'desc')->paginate(12);
        $handling = $blog->where('published', 1)->where('category', 'Уход за обувью')->orderBy('updated_at', 'desc')->paginate(12);

//        return view('public.html_pages')
        return view('public.page')
            ->with('content', $content)
            ->with('news', $news)
            ->with('articles', $articles)
            ->with('handling', $handling);
    }
}
