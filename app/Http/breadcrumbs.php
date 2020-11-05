<?php

/**
 * Home
 */
Breadcrumbs::register('home', function($breadcrumbs) {
    $breadcrumbs->push(trans('app.home'), url(App::getLocale() == 'ru' ? '/' : '/'.App::getLocale()));
});

/**
 * User
 */
Breadcrumbs::register('user', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(trans('app.personal_area'), url((App::getLocale() == 'ru' ? '' : '/'.App::getLocale()).'/user'));
    $breadcrumbs->push(trans('app.personal_data'));
});

Breadcrumbs::register('history', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(trans('app.personal_area'), url((App::getLocale() == 'ru' ? '' : '/'.App::getLocale()).'/user'));
    $breadcrumbs->push(trans('app.history_of_orders'));
});

Breadcrumbs::register('wishlist', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(trans('app.personal_area'), url((App::getLocale() == 'ru' ? '' : '/'.App::getLocale()).'/user'));
    $breadcrumbs->push(trans('app.a_wish_list'));
});

Breadcrumbs::register('change_user', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(trans('app.personal_area'));
});

/**
 * Categories
 */
Breadcrumbs::register('categories', function($breadcrumbs, $category) {
    $breadcrumbs->parent('home');
    if(!empty($category[0])) {
        foreach (array_reverse($category[0]->get_parent_categories()) as $category) {
            if (!empty($category)) {
                if (is_object($category[0])) {
                    $name = $category[0]->name;
                    $alias = $category[0]->url_alias;
                } else {
                    $name = $category['name'];
                    $alias = $category['url_alias'];
                }
            }
            $breadcrumbs->push($name, url((App::getLocale() == 'ru' ? '' : '/'.App::getLocale()).'/catalog/' . $alias));
        }
    }elseif(is_object($category)){
        foreach (array_reverse($category->get_parent_categories()) as $category) {
            if (!empty($category)) {
                if (is_object($category[0])) {
                    $name = $category[0]->name;
                    $alias = $category[0]->url_alias;
                } else {
                    $name = $category['name'];
                    $alias = $category['url_alias'];
                }
            }
            $breadcrumbs->push($name, url((App::getLocale() == 'ru' ? '' : '/'.App::getLocale()).'/catalog/' . $alias));
        }
    }else{
        if (!empty($category)) {
            if (is_object($category[0])) {
                $name = $category[0]->name;
                $alias = $category[0]->url_alias;
            } else {
                $name = $category['name'];
                $alias = $category['url_alias'];
            }
        }
        $breadcrumbs->push($name, url((App::getLocale() == 'ru' ? '' : '/'.App::getLocale()).'/catalog/' . $alias));
    }
});

/**
 * Articles
 */
Breadcrumbs::register('blog', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(trans('app.articles'), url((App::getLocale() == 'ru' ? '' : '/'.App::getLocale()).'/articles'));
});

Breadcrumbs::register('blog_item', function($breadcrumbs, $article) {
    $breadcrumbs->parent('blog');
    $breadcrumbs->push($article->title);
});

/**
 * News
 */
Breadcrumbs::register('news', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(trans('app.news'), url((App::getLocale() == 'ru' ? '' : '/'.App::getLocale()).'/news'));
});

Breadcrumbs::register('news_item', function($breadcrumbs, $article) {
    $breadcrumbs->parent('news');
    $breadcrumbs->push($article->title);
});

/**
 * HTML Pages
 */
Breadcrumbs::register('html', function($breadcrumbs, $page) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push($page->name);
});

/**
 * Login and register
 */
Breadcrumbs::register('login', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(trans('app.authorization'), url((App::getLocale() == 'ru' ? '' : '/'.App::getLocale()).'/login'));
});

Breadcrumbs::register('register', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(trans('app.check_in'));
});

Breadcrumbs::register('forgotten', function($breadcrumbs) {
    $breadcrumbs->parent('login');
    $breadcrumbs->push(trans('app.password_recovery'));
});

/**
 * Products
 */
Breadcrumbs::register('product', function($breadcrumbs, $product, $category) {
    if($category->count()) {
        $breadcrumbs->parent('categories', $category);
    }
    $breadcrumbs->push($product->name);
});

/**
 * Search
 */
Breadcrumbs::register('search', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(trans('app.Search'), url((App::getLocale() == 'ru' ? '' : '/'.App::getLocale()).'/search'));
});

/**
 * Корзина
 */
Breadcrumbs::register('cart', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(trans('app.basket'));
});

/**
 * Оформление заказа
 */
Breadcrumbs::register('checkout', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(trans('app.checkout'));
});

/**
 * Brands
 */
Breadcrumbs::register('brand', function($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(trans('app.brand'));
});