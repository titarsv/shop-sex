<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seo;
use App\Models\Redirect;
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
	
	public function redirects(Request $request, Redirect $redirects){
		if ($request->search) {
			$current_search = $request->search;
			$redirects = $redirects->where('old_url', 'like', '%' . $current_search . '%')->orWhere('new_url', 'like', '%' . $current_search . '%')->get();

			// Пагинация
			$paginator_options = [
				'path' => url($request->url()),
			];

			$per_page = 50;
			$current_page = $request->page ? $request->page : 1;
			$current_page_redirects = $redirects->slice(($current_page - 1) * $per_page, $per_page)->all();
			$redirects = new LengthAwarePaginator($current_page_redirects, count($redirects), $per_page, $current_page, $paginator_options);
		}else{
			$current_search = '';
			$redirects = $redirects::paginate(50);
		}

		return view('admin.seo.redirects.index')
			->with('current_search', $current_search)
			->with('redirects', $redirects);
	}

	public function createRedirect(){
		return view('admin.seo.redirects.create');
	}

	public function storeRedirect(Request $request, Redirect $redirects){
		$validator = Validator::make($request->all(), [
			'old_url' => 'required|unique:redirects,old_url',
			'new_url' => 'required'
		], [
			'old_url.required' => 'Обязательное поле',
			'old_url.unique' => 'Поле должно быть уникальным',
			'new_url.required' => 'Обязательное поле'
		]);

		if ($validator->fails()) {
			return redirect()
				->back()
				->withInput()
				->with('message-error', 'Сохранение не удалось! Проверьте форму на ошибки!')
				->withErrors($validator);
		}

		$redirects->fill($request->except('_token'));
		$redirects->save();

		$redirects->where('new_url', $request->old_url)->update(['new_url' => $request->new_url]);

		return redirect('/admin/seo/redirects')
			->with('message-success', 'Редирект успешно добавлен.');
	}

	public function destroyRedirect($id){
		$redirect = Redirect::find($id);
		$redirect->delete();

		return redirect('/admin/seo/redirects')
			->with('message-success', 'Редирект успешно удалён.');
	}

	public function editRedirect($id){
		$redirect = Redirect::find($id);

		return view('admin.seo.redirects.edit')
			->with('redirect', $redirect);
	}

	public function updateRedirect(Request $request, Redirect $redirects, $id){
		$validator = Validator::make($request->all(), [
			'old_url' => 'required|unique:redirects,old_url,'.$id,
			'new_url' => 'required'
		], [
			'old_url.required' => 'Обязательное поле',
			'old_url.unique' => 'Поле должно быть уникальным',
			'new_url.required' => 'Обязательное поле'
		]);

		if ($validator->fails()) {
			return redirect()
				->back()
				->withInput()
				->with('message-error', 'Сохранение не удалось! Проверьте форму на ошибки!')
				->withErrors($validator);
		}

		$redirect = $redirects->find($id);
		$redirect->fill($request->except('_token'));
		$redirect->save();

		return redirect('/admin/seo/redirects')
			->with('message-success', 'Запись успешно обновлена.');
	}
}
