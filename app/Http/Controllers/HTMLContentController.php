<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HTMLContent;
use Validator;
use App\Models\News;

class HTMLContentController extends Controller
{
    protected $rules = [
        'name' => 'required|unique:html_content',
        'content' => 'required',
        'url_alias' => 'required|unique:html_content',
//        'meta_title' => 'required|max:75',
//        'meta_description' => 'max:180',
//        'meta_keywords' => 'max:180',
    ];
    protected $messages = [
        'name.required' => 'Поле должно быть заполнено!',
        'name.unique' => 'Значение должно быть уникальным!',
        'content.required' => 'Поле должно быть заполнено!',
        'url_alias.required' => 'Поле должно быть заполнено!',
        'url_alias.unique' => 'Значение должно быть уникальным!',
        'meta_title.required' => 'Поле должно быть заполнено!',
//        'meta_title.max' => 'Максимальная длина заголовка не может превышать 75 символов!',
//        'meta_description.max' => 'Максимальная длина описания не может превышать 180 символов!',
//        'meta_keywords.max' => 'Максимальная длина ключевых слов не может превышать 180 символов!',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(HTMLContent $content)
    {
        return view('admin.htmlcontent.index')
            ->with('content', $content->paginate(10));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(HTMLContent $content)
    {
        return view('admin.htmlcontent.create')
            ->with('pages', $content->all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, HTMLContent $content)
    {
        $validator = Validator::make($request->all(), $this->rules, $this->messages);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->with('message-error', 'Сохранение не удалось! Проверьте форму на ошибки!')
                ->withErrors($validator);
        }

        $content->fill($request->except('_token'));
        $content->content = htmlentities($request->content);
        $content->sort_order = !empty($request->sort_order) ? $request->sort_order : 0;
        $content->save();

        return redirect('/admin/html')
            ->with('content', $content->paginate(10))
            ->with('message-success', 'Страница ' . $content->name . ' успешно добавлена.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, HTMLContent $content)
    {
        return view('admin.htmlcontent.edit')
            ->with('pages', $content->all())
            ->with('content', $content->find($id));
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
        $rules['name'] = 'required|unique:html_content,name,'.$id;
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
        $content->content = htmlentities($request->content);
        $content->sort_order = !empty($request->sort_order) ? $request->sort_order : 0;
        $content->save();

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
