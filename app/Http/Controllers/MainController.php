<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use App\Models\Products;
use App\Models\Modules;
use App\Models\Moduleslideshow;
use App\Models\ModuleBestsellers;
use App\Models\Categories;
use App\Models\Blog;
use App\Models\News;
use App\Http\Requests;
use App\Models\Image;
use App\Models\HTMLContent;
use App\Models\Attribute;
use App;


class MainController extends Controller
{
    public function index(Categories $categories, Modules $modules, Moduleslideshow $slideshow)
    {
//        if(!empty(request()->index)){
//            $products = Products::limit(1000)->offset(1000 * (request()->index - 1))->with('localization')->get();
//            foreach($products as $product){
//                $product->updateSearchField();
//            }
//        }

        $root_categories = $categories->get_root_categories();

        $module = $modules->getSlideshow('shops');
        $settings = json_decode($module->settings);

        $shops = [];

        if(!empty($settings->slides)){
            foreach ($settings->slides as $slide){
                $shops[] = (object) [
                    'image_id' => $slide->image_id,
                    'slide_title' => app()->getLocale() != 'ru' && isset($slide->{'slide_title_'.app()->getLocale()}) ? $slide->{'slide_title_'.app()->getLocale()} : $slide->slide_title,
                    'image' => Image::find($slide->image_id)
                ];
            }
        }

        $bestsellers = [];
        foreach (ModuleBestsellers::all() as $product){
            $bestsellers[] = $product->product;
        }

        return view('index')
	        ->with('categories', $root_categories)
	        ->with('shops', $shops)
	        ->with('bestsellers', $bestsellers)
            ->with('slideshow', $slideshow->where('status', 1)->orderBy('sort_order', 'asc')->get()->filter(function ($value, $key) {
                $data = json_decode($value->slide_data, true);
                return empty($data['lang']) || $data['lang'] === app()->getLocale();
            }));
    }

    /**
     * @param Categories $categories
     * @param Products $products
     * @param Blog $blog
     * @param HTMLContent $html
     * @param null $alias
     * @param null $filters
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function route(Categories $categories, Products $products, News $news, HTMLContent $html, $alias = null, $filters = null){
        $parts = explode('/', str_replace('https://', '', url()->current()));
        $part = end($parts);

        $redirects = array(
            '/forumy/obshchaya-diskussiya' => '/page/forum',
			'/osobye-pokrytiya-vibratorov-dlya-nezabyvaemyh-oshchushcheniy' => '/page/forum',
            '/user/login' => '/login',
            '/user/register' => '/login',
            '/user/password' => '/login',
        );

        if(isset($redirects[urldecode(str_replace(env('APP_URL', 'https://shop-sex.com.ua'), '', url()->current()))])){
            return redirect($redirects[urldecode(str_replace(env('APP_URL', 'https://shop-sex.com.ua'), '', url()->current()))], 301);
        }elseif($categories->where('url_alias', $part)->count()){
            return redirect()->action(
                'CategoriesController@show', ['alias' => $part], 301
            );
        }elseif(count($parts) >= 2 && $products->where('url_alias', $part)->count()){
            return redirect()->action(
                'ProductsController@show', ['alias' => $part], 301
            );
        }elseif(count($parts) == 2 && $news->where('url_alias', $part)->count()){
            return redirect()->action(
                'NewsController@show', ['alias' => $part], 301
            );
        }elseif(count($parts) == 2 && $html->where('url_alias', $part)->count()){
            return redirect()->action(
                'HTMLContentController@show', ['alias' => $part], 301
            );
        }elseif(in_array(substr($part, -4), ['.jpg', '.png', 'jpeg'])){
            $image = new Image();
            return redirect('/uploads/no_image.jpg', 301);
        }

        return abort(404);
    }
}
