<?php

namespace App\Providers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Cartalyst\Sentinel\Native\Facades\Sentinel;

use App\Models\HTMLContent;
use App\Models\Settings;
use App\Models\Categories;
use App\Models\Wishlist;
use App\Models\Cart;
use App\Models\User;
use App\Models\Review;
use App\Models\Order;
use App\Models\PersonalSale;
use App\Models\Paginator;
use App\Models\Modules;
use App\Models\Seo;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    private $user;
    private $roles_array = array();
    public function boot(Categories $categories, Request $request)
    {
        $user = Sentinel::getUser();
        if(!is_null($user)) {
            $user = User::find($user->id);
        }

        $this->user = $user;

        if(!is_null($user)) {

            view()->composer('admin.layouts.main', function($view) use ($user) {
                $orders = Order::where('status_id', 1)->count();
                $reviews = Review::where('new', 1)->count();
                $personal_sales = PersonalSale::where('status', 'new')->count();

                $view->with([
                    'user'          => $user,
                    'new_orders'    => $orders,
                    'new_reviews'   => $reviews,
                    'personal_sales'   => $personal_sales
                ]);
            });
            view()->composer('public.order', function ($view) {
                $view->with('user', User::find($this->user->id));
            });

            if($this->user) {
                $roles = Sentinel::getRoles()->toArray();
                foreach($roles as $role){
                    $this->roles_array[] = $role['slug'];
                }
            }

            view()->composer('public.layouts.header-middle', function ($view) {
                $view->with('user_logged', $this->user);
            });

            view()->composer([
                'public.layouts.header-main',
                'public.layouts.product',
                'public.product',
                'public.layouts.cart',
                'public.category',
                'admin.orders.edit',
                'public.order'
            ], function ($view) {
                $view->with('user_id', $this->user->id)
                    ->with('user_logged', true)
                    ->with('user_roles', $this->roles_array);
            });

            view()->composer('public.layouts.header-main', function($view) {
                $view->with('wishlist', $this->user->wishlist);
            });

        } else {
            view()->composer([
                'public.layouts.header-main',
                'public.layouts.header-middle',
                'public.layouts.product',
                'public.product',
                'public.order'
                ], function ($view) {
                $view->with('user_logged', false);
            });

            view()->composer([
                    'public.layouts.product',
                    'public.layouts.product_small',
                    'public.product',
                    'public.layouts.cart',
                    'public.category',
                    'public.layouts.header-main'
                ], function ($view) {
                $view->with('user_id', 0)->with('user_wishlist',[]);
            });
        }
        
//        view()->composer(['public.layouts.main-menu', 'index'], function ($view) use ($categories) {
//            $root_categories = $categories->get_root_categories();
//            $view->with('items', $root_categories);
//        });

        view()->composer(['public.layouts.header-main'], function ($view) use ($categories) {
            $cart = new Cart;
            $current_cart = $cart->current_cart();
            //$root_categories = $categories->get_root_categories();
            $view->with('cart', $current_cart);
        });

        view()->composer([
            'public/*',
            'users/*',
            'errors/*',
            'index',
            'login',
            'registration',
            'forgotten'
        ], function ($view) use ($user) {
            $settings = new Settings;
            $view->with([
                'settings' => $settings->get_global(),
                'user' => $user ? $user : false
            ]);
        });

        view()->composer('admin.layouts.sidebar', function($view) {
            $view->with('new_reviews', Review::where('new', 1)->get());
        });

        view()->composer([
            'public.layouts.pagination',
            'public.layouts.header',
            'public.category'
            ], function($view) {
            $view->with('cp', new Paginator());
        });

//        view()->composer('public.layouts.header-main', function($view) {
//            $module = Modules::where('alias_name', 'menu')->first();
//            $menu = json_decode($module->settings);
//            $view->with('menu', $menu);
//        });

        view()->composer([
            'public.layouts.header',
            'public.category',
            ], function($view) use ($request) {
            $path = preg_replace('/\/page\d+/i', '', $request->path());
            $view->with('seo', Seo::where('url', $path)->orWhere('url', '/'.$path)->orWhere('url', env('APP_URL').'/'.$path)->first());
        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
