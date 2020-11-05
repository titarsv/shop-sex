<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::get('/xmlsitemapupdate', function () {
    $exitCode = Artisan::call('xmlsitemap', []);
});


/**
 * Admin routing
 */
Route::group(['middleware' => ['admin'], 'prefix' => 'admin'], function(){
    Route::get('/', 'AdminController@dash');
    Route::post('/async-upload', 'MediaController@upload');
    Route::match(['get', 'post'], '/ajax', 'AjaxController@index');
    Route::get('/products', 'ProductsController@index');
    Route::post('/products', 'ProductsController@index');

    Route::get('/settings', 'SettingsController@index');
    Route::post('/settings', 'SettingsController@update');
    Route::get('/delivery-and-payment', 'SettingsController@extraIndex');
    Route::post('/delivery-and-payment', 'SettingsController@extraUpdate');
    Route::get('/seo', 'SettingsController@seoSettings');
    Route::post('/seo', 'SettingsController@seoUpdate');
    Route::get('/delivery-and-payment/newpost-update', 'SettingsController@newpostUpdate');
    Route::post('/upload_attribute_image', 'AttributesController@upload_image');

    Route::get('/cacheflush', function() {
        Cache::flush();
        return redirect()->back()->with('message-success', 'Кэш успешно очищен!');
    });

    Route::group(['prefix' => 'categories'], function(){
        Route::get('/', 'CategoriesController@index');
        Route::get('/create', 'CategoriesController@create');
        Route::post('/create', 'CategoriesController@store');
        Route::get('/delete/{id}', 'CategoriesController@destroy');
        Route::get('/edit/{id}', 'CategoriesController@edit');
        Route::post('/edit/{id}', 'CategoriesController@update');
    });

    Route::group(['prefix' => 'attributes'], function(){
        Route::get('/', 'AttributesController@index');
        Route::post('/create', 'AttributesController@store');
        Route::get('/delete/{id}', 'AttributesController@destroy');
        Route::get('/edit/{id}', 'AttributesController@edit');
        Route::post('/edit/{id}', 'AttributesController@update');
        Route::group(['prefix' => 'values'], function(){
            Route::post('/create', 'AttributesController@adminStoreValueAction');
            Route::post('/delete/{id}', 'AttributesController@adminDestroyValueAction');
        });
    });

    Route::group(['prefix' => 'products'], function(){
        Route::get('/', 'ProductsController@index');
        Route::get('/create', 'ProductsController@create');
        Route::post('/create', 'ProductsController@store');
        Route::get('/delete/{id}', 'ProductsController@destroy');
        Route::get('/edit/{id}', 'ProductsController@edit');
        Route::post('/edit/{id}', 'ProductsController@update');
        Route::get('/getattributevalues', 'ProductsController@getAttributes');
        Route::post('/getattributevalues', 'ProductsController@getAttributeValues');
    });
    Route::match(['get', 'post'], '/upload-products', 'ProductsController@upload');

    Route::group(['prefix' => 'manufacturers'], function(){
        Route::get('/', 'ManufacturersController@index');
        Route::get('/create', 'ManufacturersController@create');
        Route::post('/create', 'ManufacturersController@store');
        Route::get('/edit/{id}', 'ManufacturersController@edit');
        Route::post('/edit/{id}', 'ManufacturersController@update');
        Route::get('/delete/{id}', 'ManufacturersController@destroy');
    });

    Route::group(['prefix' => 'news'], function(){
        Route::get('/', 'NewsController@index');
        Route::get('/create', 'NewsController@create');
        Route::post('/create', 'NewsController@store');
        Route::get('/edit/{id}', 'NewsController@edit');
        Route::post('/edit/{id}', 'NewsController@update');
        Route::get('/delete/{id}', 'NewsController@destroy'); //softDelete
    });

    Route::group(['prefix' => 'users'], function(){
        Route::get('/', 'UserController@index');
        Route::get('/create', 'UserController@create');
        Route::post('/create', 'UserController@store');
        Route::get('/edit/{id}', 'UserController@edit');
        Route::post('/edit/{id}', 'UserController@update');
        Route::get('/stat/{id}', 'UserController@statistic');
        Route::get('/reviews/{id}', 'UserController@reviews');
        Route::get('/wishlist/{id}', 'UserController@adminWishlist');
        Route::get('/delete/{id}', 'UserController@destroy'); //softDelete
    });

    Route::get('/managers', 'UserController@managers');

    Route::group(['prefix' => 'orders'], function(){
        Route::get('/', 'OrdersController@index');
        Route::get('/create', 'OrdersController@create');
        Route::post('/create', 'OrdersController@store');
        Route::get('/edit/{id}', 'OrdersController@edit');
        Route::post('/edit/{id}', 'OrdersController@update');
        Route::post('/stat/{id}', 'OrdersController@statistic');
        Route::get('/delete/{id}', 'OrdersController@destroy'); //softDelete
    });
    Route::group(['prefix' => 'html'], function(){
        Route::get('/', 'HTMLContentController@index');
        Route::get('/create', 'HTMLContentController@create');
        Route::post('/create', 'HTMLContentController@store');
        Route::get('/edit/{id}', 'HTMLContentController@edit');
        Route::post('/edit/{id}', 'HTMLContentController@update');
        Route::get('/delete/{id}', 'HTMLContentController@destroy'); //softDelete
    });

    Route::group(['prefix' => 'modules'], function(){
        Route::get('/', 'ModulesController@index');
        Route::get('/settings/{name}', function($name) {
            $controller = App::make('\App\Http\Controllers\Module' . $name . 'Controller');
            return $controller->callAction('index', []);
        });
        Route::post('/settings/{name}', function($name) {
            $controller = App::make('\App\Http\Controllers\Module' . $name . 'Controller');
            return $controller->callAction('save', []);
        });

    });
    Route::group(['prefix' => 'reviews'], function(){
        Route::get('/', 'ReviewsController@index');
        Route::get('/show/{id}', 'ReviewsController@show');
        Route::post('/show/{id}', 'ReviewsController@update');
        Route::get('/delete/{id}', 'ReviewsController@destroy'); //softDelete
    });

    Route::get('/loadimages', 'ImagesController@loadImages');
    Route::post('/upload', 'ImagesController@uploadImages');

    Route::group(['prefix' => 'images'], function(){
        Route::post('/start_updating', 'ImagesController@startUpdatingImages');
        Route::post('/update_sizes', 'ImagesController@updateImageSize');
        Route::post('/remove_images', 'ImagesController@removeImages');
    });

    Route::group(['prefix' => 'seo'], function(){
        Route::get('/list', 'SeoController@index');
        Route::get('/create', 'SeoController@create');
        Route::post('/create', 'SeoController@store');
        Route::get('/delete/{id}', 'SeoController@destroy');
        Route::get('/edit/{id}', 'SeoController@edit');
        Route::post('/edit/{id}', 'SeoController@update');
        Route::group(['prefix' => 'redirects'], function(){
            Route::any('/', 'SeoController@redirects');
            Route::get('/create', 'SeoController@createRedirect');
            Route::post('/create', 'SeoController@storeRedirect');
            Route::get('/delete/{id}', 'SeoController@destroyRedirect');
            Route::get('/edit/{id}', 'SeoController@editRedirect');
            Route::post('/edit/{id}', 'SeoController@updateRedirect');
        });
    });

    Route::post('/group_action','ProductsController@groupAction');
});

$prefixes = Config::get('app.locales');
if(in_array(Config::get('app.locale'), $prefixes)){
    unset($prefixes[array_search(Config::get('app.locale'), $prefixes)]);
}
$prefixes[] = '';

foreach($prefixes as $prefix){
    $params = [];
    if(!empty($prefix)){
        $params = ['prefix' => $prefix, 'where' => ['locale' => '[a-zA-Z]{2}'], 'middleware' => 'setlocale'];
    }
    Route::group($params, function(){
        Route::get('/', ['as'=>'home', 'uses'=>'MainController@index']);
        Route::get('/page/{alias}', 'HTMLContentController@show');
        Route::get('/catalog', 'CategoriesController@catalog');
        Route::get('/catalog/{alias}/{filters?}/{page?}', 'CategoriesController@show');
        Route::get('/brands', 'CategoriesController@brands');
        Route::get('/news', 'NewsController@news');
        Route::get('/news/{alias}', 'NewsController@show');
        Route::get('/articles', 'NewsController@articles');
        Route::get('/articles/{alias}', 'NewsController@show');
        Route::get('/handling', 'NewsController@handling');
        Route::get('/handling/{alias}', 'NewsController@show');
        Route::get('/cart', 'CartController@cart');
        Route::get('/checkout', 'CartController@show');
        Route::post('/checkout', 'CartController@show');
        Route::get('/thank_you', 'OrdersController@thank_you');
        Route::match(['get', 'post'], '/search/{page?}', ['as' => 'search', 'uses' => 'ProductsController@search']);

        Route::post('/order/create', 'CheckoutController@createOrder');

        Route::get('/product/{alias}', 'ProductsController@show');
        Route::post('/review/add', 'ReviewsController@add');
        Route::post('/review/add-likes', 'ReviewsController@addLikes');

        Route::get('/reviews', 'ReviewsController@shopReviews');

        /**
         * Authorization routing
         */
        Route::get('/login', 'LoginController@login');
        Route::post('/login', 'LoginController@authenticate');
        Route::get('/logout', 'LoginController@logout');

        //Social Login
        Route::get('/login/{provider?}',[
            'uses' => 'LoginController@getSocialAuth',
            'as'   => 'auth.getSocialAuth'
        ]);
        Route::get('/login/callback/{provider?}',[
            'uses' => 'LoginController@getSocialAuthCallback',
            'as'   => 'auth.getSocialAuthCallback'
        ]);

        Route::get('/forgotten', 'LoginController@forgotten');
        Route::post('/forgotten', 'LoginController@reminder');
        Route::get('/lostpassword', 'LoginController@lostpassword');
        Route::post('/lostpassword', 'LoginController@changePassword');

        Route::post('wishlist/update','WishListController@update');
        Route::post('wishlist/del','WishListController@delWishlist');

        Route::post('cart/update','CartController@updateCart');
        Route::post('cart/updateAll','CartController@update');
        Route::post('cart/get','CartController@getCart');

        /**
         * Users routing
         */
        Route::post('/subscribe', 'UserController@subscribe');
        Route::post('/callback', 'UserController@callback');
        Route::get('/livesearch', 'ProductsController@livesearch');
        Route::post('get_models','ProductsController@getModels');
        Route::post('get_years','ProductsController@getYears');
        Route::post('order/create', 'CheckoutController@createOrder');
        Route::post('checkout/delivery', 'CheckoutController@delivery');
        Route::post('/checkout/cities', 'CheckoutController@getCities');
        Route::post('/checkout/warehouses', 'CheckoutController@getWarehouses');
        Route::post('/checkout/confirm', 'CheckoutController@confirmOrder');
        Route::get('/checkout/complete', 'CheckoutController@orderComplete');
        Route::post('/sendmail', 'UserController@sendMail');
        Route::get('/sync-products', 'ProductsController@syncProducts');


        Route::get('/{p1}/{p2?}/{p3?}/{p4?}/{p5?}/{p6?}/{p7?}/{p8?}', 'MainController@route');
    });
}

$middleware = array_merge(\Config::get('lfm.middlewares'), [
    '\Unisharp\Laravelfilemanager\middlewares\MultiUser',
    '\Unisharp\Laravelfilemanager\middlewares\CreateDefaultFolder',
]);
$prefix = \Config::get('lfm.url_prefix', \Config::get('lfm.prefix', 'laravel-filemanager'));
$as = 'unisharp.lfm.';
$namespace = '\Unisharp\Laravelfilemanager\controllers';

// make sure authenticated
Route::group(compact('middleware', 'prefix', 'as', 'namespace'), function () {

    // Show LFM
    Route::get('/', [
        'uses' => 'LfmController@show',
        'as' => 'show',
    ]);

    // Show integration error messages
    Route::get('/errors', [
        'uses' => 'LfmController@getErrors',
        'as' => 'getErrors',
    ]);

    // upload
    Route::any('/upload', [
        'uses' => 'UploadController@upload',
        'as' => 'upload',
    ]);

    // list images & files
    Route::get('/jsonitems', [
        'uses' => 'ItemsController@getItems',
        'as' => 'getItems',
    ]);

    // folders
    Route::get('/newfolder', [
        'uses' => 'FolderController@getAddfolder',
        'as' => 'getAddfolder',
    ]);
    Route::get('/deletefolder', [
        'uses' => 'FolderController@getDeletefolder',
        'as' => 'getDeletefolder',
    ]);
    Route::get('/folders', [
        'uses' => 'FolderController@getFolders',
        'as' => 'getFolders',
    ]);

    // crop
    Route::get('/crop', [
        'uses' => 'CropController@getCrop',
        'as' => 'getCrop',
    ]);
    Route::get('/cropimage', [
        'uses' => 'CropController@getCropimage',
        'as' => 'getCropimage',
    ]);
    Route::get('/cropnewimage', [
        'uses' => 'CropController@getNewCropimage',
        'as' => 'getCropimage',
    ]);

    // rename
    Route::get('/rename', [
        'uses' => 'RenameController@getRename',
        'as' => 'getRename',
    ]);

    // scale/resize
    Route::get('/resize', [
        'uses' => 'ResizeController@getResize',
        'as' => 'getResize',
    ]);
    Route::get('/doresize', [
        'uses' => 'ResizeController@performResize',
        'as' => 'performResize',
    ]);

    // download
    Route::get('/download', [
        'uses' => 'DownloadController@getDownload',
        'as' => 'getDownload',
    ]);

    // delete
    Route::get('/delete', [
        'uses' => 'DeleteController@getDelete',
        'as' => 'getDelete',
    ]);

    // Route::get('/demo', 'DemoController@index');
});

Route::group(compact('prefix', 'as', 'namespace'), function () {
    // Get file when base_directory isn't public
    $images_url = '/' . \Config::get('lfm.images_folder_name') . '/{base_path}/{image_name}';
    $files_url = '/' . \Config::get('lfm.files_folder_name') . '/{base_path}/{file_name}';
    Route::get($images_url, 'RedirectController@getImage')
        ->where('image_name', '.*');
    Route::get($files_url, 'RedirectController@getFile')
        ->where('file_name', '.*');
});