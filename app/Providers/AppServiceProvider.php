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
use App\Models\Image;
use Illuminate\Http\Request;
use Config;

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

            view()->composer(['admin.media.assets'], function ($view){
                $view->with($this->mediaVariables());
            });

            view()->composer(['admin.products.create'], function ($view){
                $view->with('languages', Config::get('app.locales_names'));
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


        
        view()->composer(['public.category'], function ($view) use ($categories) {
            $root_categories = $categories->get_root_categories();
            $view->with('categories', $root_categories);
        });

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

        view()->composer([
            'public.layouts.header-main'
        ], function($view) use ($request, $categories) {
            $cat = $categories->where('name', 'Акции')->first();
            $cats = array_merge([$cat->id], $categories->get_children_categories($cat->id));
            $isset = false;
            foreach($cats as $cat){
                if($categories->where('id', $cat)->first()->products()->where('stock', 1)->count()){
                    $isset = true;
                    break;
                }
            }
            $view->with('isset_actions', $isset)->with('isset_new', $categories->where('name', 'Новинки')->first()->products()->where('stock', 1)->count());
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

    public function convert_hr_to_bytes( $value ) {
        $value = strtolower( trim( $value ) );
        $bytes = (int) $value;

        if ( false !== strpos( $value, 'g' ) ) {
            $bytes *= 1024*1024*1024;
        } elseif ( false !== strpos( $value, 'm' ) ) {
            $bytes *= 1024*1024;
        } elseif ( false !== strpos( $value, 'k' ) ) {
            $bytes *= 1024;
        }

        // Deal with large (float) values which run into the maximum integer size.
        return min( $bytes, PHP_INT_MAX );
    }

    public function sizeFormat($bytes, $decimals = 0){
        $quant = array(
            'TB' => 1024*1024*1024*1024,
            'GB' => 1024*1024*1024,
            'MB' => 1024*1024,
            'KB' => 1024,
            'B'  => 1,
        );

        if ( 0 === $bytes ) {
            return number_format( 0, abs(intval( $decimals )) ) . ' B';
        }

        foreach ( $quant as $unit => $mag ) {
            if ( doubleval( $bytes ) >= $mag ) {
                return number_format( $bytes / $mag, $decimals ) . ' ' . $unit;
            }
        }

        return false;
    }

    public function mediaVariables(){
        $files = new Image();
        $u_bytes = $this->convert_hr_to_bytes(ini_get('upload_max_filesize'));
        $p_bytes = $this->convert_hr_to_bytes(ini_get('post_max_size'));

        $max_upload_size = min($u_bytes, $p_bytes);

        if(!$max_upload_size){
            $max_upload_size = 0;
        }

        $max_size = $this->sizeFormat($max_upload_size);
        $commonL10n = json_encode([
            'warnDelete' => 'Вы собираетесь навсегда удалить эти элементы с сайта./nЭто действие не может быть отменено./n\'Отмена\' для отмены, \'OK\' для удаления.',
            'dismiss' => 'Скрыть это уведомление',
            'collapseMenu' => 'Свернуть главное меню',
            'expandMenu' => 'Развернуть главное меню'
        ]);
        $pluploadL10n = json_encode([
            'queue_limit_exceeded' => 'Вы поставили в очередь слишком много файлов.',
            'file_exceeds_size_limit' => 'Размер файла «%s» превышает максимальный для этого сайта.',
            'zero_byte_file' => 'Файл пуст. Пожалуйста, выберите другой.',
            'invalid_filetype' => 'Извините, этот тип файла недопустим по соображениям безопасности.',
            'not_an_image' => 'Файл не является изображением. Пожалуйста, выберите другой.',
            'image_memory_exceeded' => 'Превышен лимит памяти. Пожалуйста, выберите файл поменьше.',
            'image_dimensions_exceeded' => 'Размеры изображения превышают максимальные. Пожалуйста, выберите другое.',
            'default_error' => 'Во время загрузки произошла ошибка. Пожалуйста, повторите попытку позже.',
            'missing_upload_url' => 'Ошибка конфигурации. Пожалуйста, свяжитесь с администратором сервера.',
            'upload_limit_exceeded' => 'Вы можете загрузить только 1 файл.',
            'http_error' => 'Ошибка HTTP.',
            'upload_failed' => 'Загрузка не удалась.',
            'big_upload_failed' => 'Попробуйте загрузить этот файл через %1$sзагрузчик браузера%2$s.',
            'big_upload_queued' => 'Размер файла «%s» превышает максимальный для многофайлового загрузчика в сочетании с вашим браузером.',
            'io_error' => 'Ошибка ввода/вывода.',
            'security_error' => 'Ошибка безопасности.',
            'file_cancelled' => 'Загрузка отменена.',
            'upload_stopped' => 'Загрузка остановлена.',
            'dismiss' => 'Закрыть',
            'crunching' => 'Обработка…',
            'deleted' => 'перемещён в корзину.',
            'error_uploading' => 'Файл «%s» загрузить не удалось.'
        ]);
        $quicktagsL10n = json_encode([
            'closeAllOpenTags' => 'Закрыть все открытые теги',
            'closeTags' => 'закрыть теги',
            'enterURL' => 'Введите адрес (URL)',
            'enterImageURL' => 'Введите адрес (URL) картинки',
            'enterImageDescription' => 'Введите описание изображения',
            'textdirection' => 'направление текста',
            'toggleTextdirection' => 'Переключить направление текста в редакторе',
            'dfw' => 'Полноэкранный режим',
            'strong' => 'Жирный',
            'strongClose' => 'Закрыть тег жирного шрифта',
            'em' => 'Курсив',
            'emClose' => 'Закрыть тег курсива',
            'link' => 'Вставить ссылку',
            'blockquote' => 'Цитата',
            'blockquoteClose' => 'Закрыть тег цитаты',
            'del' => 'Удаленный (перечёркнутый) текст',
            'delClose' => 'Закрыть тег удалённого текста',
            'ins' => 'Вставленный текст',
            'insClose' => 'Закрыть тег вставленного текста',
            'image' => 'Вставить изображение',
            'ul' => 'Маркированный список',
            'ulClose' => 'Закрыть тег маркированного списка',
            'ol' => 'Нумерованный список',
            'olClose' => 'Закрыть тег нумерованного списка',
            'li' => 'Элемент списка',
            'liClose' => 'Закрыть тег элемента списка',
            'code' => 'Код',
            'codeClose' => 'Закрыть тег кода',
            'more' => 'Вставить тег «Далее»',
        ]);
        $thickboxL10n = json_encode([
            'next' => 'Далее →',
            'prev' => '← Назад',
            'image' => 'Изображение',
            'of' => 'из',
            'close' => 'Закрыть',
            'noiframes' => 'Эта функция требует поддержки плавающих фреймов. У вас отключены теги iframe, либо ваш браузер их не поддерживает.',
            'loadingAnimation' => '/images/larchik/loadingAnimation.gif'
        ]);
        $_wpMediaViewsL10n = [
            'url' => 'URL',
            'addMedia' => 'Добавить медиафайл',
            'search' => 'Поиск',
            'select' => 'Выбрать',
            'cancel' => 'Отмена',
            'update' => 'Обновить',
            'replace' => 'Заменить',
            'remove' => 'Удалить',
            'back' => 'Назад',
            'selected' => 'Выбрано: %d',
            'dragInfo' => 'Отсортируйте медиафайлы путём перетаскивания.',
            'uploadFilesTitle' => 'Загрузить файлы',
            'uploadImagesTitle' => 'Загрузить изображения',
            'mediaLibraryTitle' => 'Библиотека файлов',
            'insertMediaTitle' => 'Добавить медиафайл',
            'createNewGallery' => 'Создать новую галерею',
            'createNewPlaylist' => 'Создать плей-лист',
            'createNewVideoPlaylist' => 'Создать плей-лист видео',
            'returnToLibrary' => '← Вернуться в библиотеку',
            'allMediaItems' => 'Все медиафайлы',
            'allDates' => 'Все даты',
            'noItemsFound' => 'Элементов не найдено.',
            'insertIntoPost' => 'Вставить в запись',
            'unattached' => 'Неприкреплённые',
            'mine' => 'Моё',
            'trash' => 'Корзина',
            'uploadedToThisPost' => 'Загруженные для этой записи',
            'warnDelete' => "Вы собираетесь навсегда удалить этот элемент с сайта.\nЭто действие не может быть отменено.\n'Отмена' для отмены, 'OK' для удаления.",
            'warnBulkDelete' => "Вы собираетесь навсегда удалить эти элементы с сайта.\nЭто действие не может быть отменено.\n'Отмена' для отмены, 'OK' для удаления.",
            'warnBulkTrash' => "Вы собираетесь переместить эти элементы в корзину.\n«Отмена» — оставить, «OK» — удалить.",
            'bulkSelect' => 'Множественный выбор',
            'cancelSelection' => 'Снять выделение',
            'trashSelected' => 'Удалить выбранные',
            'untrashSelected' => 'Восстановить выбранные',
            'deleteSelected' => 'Удалить выбранные',
            'deletePermanently' => 'Удалить навсегда',
            'apply' => 'Применить',
            'filterByDate' => 'Фильтр по дате',
            'filterByType' => 'Фильтр по типу',
            'searchMediaLabel' => 'Поиск медиафайлов',
            'searchMediaPlaceholder' => 'Поиск медиафайлов...',
            'noMedia' => 'Медиафайлов не найдено.',
            'attachmentDetails' => 'Параметры файла',
            'insertFromUrlTitle' => 'Вставить с сайта',
            'setFeaturedImageTitle' => 'Изображение записи',
            'setFeaturedImage' => 'Установить изображение записи',
            'createGalleryTitle' => 'Создать галерею',
            'editGalleryTitle' => 'Редактировать галерею',
            'cancelGalleryTitle' => '← Отменить создание галереи',
            'insertGallery' => 'Вставить галерею',
            'updateGallery' => 'Обновить галерею',
            'addToGallery' => 'Добавить в галерею',
            'addToGalleryTitle' => 'Добавить в галерею',
            'reverseOrder' => 'В обратном порядке',
            'imageDetailsTitle' => 'Параметры изображения',
            'imageReplaceTitle' => 'Заменить изображение',
            'imageDetailsCancel' => 'Отменить редактирование',
            'editImage' => 'Редактировать',
            'chooseImage' => 'Выбрать изображение',
            'selectAndCrop' => 'Выбрать и обрезать',
            'skipCropping' => 'Не обрезать',
            'cropImage' => 'Обрезать изображение',
            'cropYourImage' => 'Обрезать изображение',
            'cropping' => 'Обработка…',
            'suggestedDimensions' => 'Предлагаемый размер изображения: %1$s на %2$s пикселов.',
            'cropError' => 'При обрезке изображения произошла ошибка.',
            'audioDetailsTitle' => 'Параметры аудиофайла',
            'audioReplaceTitle' => 'Заменить аудиофайл',
            'audioAddSourceTitle' => 'Добавить источник аудио',
            'audioDetailsCancel' => 'Отменить редактирование',
            'videoDetailsTitle' => 'Параметры видеофайла',
            'videoReplaceTitle' => 'Заменить видеофайл',
            'videoAddSourceTitle' => 'Добавить источник видео',
            'videoDetailsCancel' => 'Отменить редактирование',
            'videoSelectPosterImageTitle' => 'Добавить постер',
            'videoAddTrackTitle' => 'Добавить субтитры',
            'playlistDragInfo' => 'Отсортируйте треки путём перетаскивания.',
            'createPlaylistTitle' => 'Создать плей-лист аудио',
            'editPlaylistTitle' => 'Изменить плей-лист',
            'cancelPlaylistTitle' => '← Отменить создание плей-листа',
            'insertPlaylist' => 'Вставить плей-лист аудио',
            'updatePlaylist' => 'Обновить плей-лист аудио',
            'addToPlaylist' => 'Добавить в плей-лист аудио',
            'addToPlaylistTitle' => 'Добавить в плей-лист',
            'videoPlaylistDragInfo' => 'Отсортируйте видеофайлы путём перетаскивания.',
            'createVideoPlaylistTitle' => 'Создать плей-лист видео',
            'editVideoPlaylistTitle' => 'Изменить плей-лист',
            'cancelVideoPlaylistTitle' => '← Отменить создание плей-листа',
            'insertVideoPlaylist' => 'Вставить плей-лист видео',
            'updateVideoPlaylist' => 'Обновить плей-лист видео',
            'addToVideoPlaylist' => 'Добавить в плей-лист видео',
            'addToVideoPlaylistTitle' => 'Добавить в плей-лист',
            'settings' => [
                'tabs' => [],
                'tabUrl' => '/admin/media-upload?chromeless=1',
                'mimeTypes' => [
                    'image' => 'Изображения',
                    'audio' => 'Аудио',
                    'video' => 'Видео',
                ],
                'captions' => '1',
                'nonce' => [
                    'sendToEditor' => '091a1773c8',
                ],
                'post' => [
                    'id' => '0',
                ],
                'defaultProps' => [
                    'link' => 'none',
                    'align' => '',
                    'size' => '',
                ],
                'attachmentCounts' => [
                    'audio' => '1',
                    'video' => '1',
                ],
                'oEmbedProxyUrl' => '/wp-json/oembed/1.0/proxy',
                'embedExts' => [
                    'mp3',
                    'ogg',
                    'flac',
                    'm4a',
                    'wav',
                    'mp4',
                    'm4v',
                    'webm',
                    'ogv',
                    'flv',
                ],
                'embedMimes' => [
                    'mp3' => 'audio/mpeg',
                    'ogg' => 'audio/ogg',
                    'flac' => 'audio/flac',
                    'm4a' => 'audio/mpeg',
                    'wav' => 'audio/wav',
                    'mp4' => 'video/mp4',
                    'm4v' => 'video/mp4',
                    'webm' => 'video/webm',
                    'ogv' => 'video/ogg',
                    'flv' => 'video/x-flv',
                ],
                'contentWidth' => '',
                'months' => [
                    [
                        'year' => date('Y'),
                        'month' => date('m'),
                        'text' => trans('date.month_declensions.'.date('F')).date(' Y'),
                    ]
                ],
                'mediaTrash' => '0',
            ]
        ];

//        $months_data = $files->select(DB::raw('created_at, YEAR( created_at ) AS year, MONTH( created_at ) AS month'))->distinct()->orderBy('created_at', 'DESC')->get();
        if (!empty($months_data)) {
            $months_names = [
                1 => 'Январь',
                2 => 'Февраль',
                3 => 'Март',
                4 => 'Апрель',
                5 => 'Май',
                6 => 'Июнь',
                7 => 'Июль',
                8 => 'Август',
                9 => 'Сентябрь',
                10 => 'Октябрь',
                11 => 'Ноябрь',
                12 => 'Декабрь'
            ];
            $months = [];
            foreach ($months_data as $month_year) {
                if (isset($months_names[$month_year->month])) {
                    $months[$month_year->month.'.'.$month_year->year] = [
                        'year' => $month_year->year,
                        'month' => $month_year->month,
                        'text' => sprintf(__('%1$s %2$d'), $months_names[$month_year->month], $month_year->year)
                    ];
                }
            }
            $_wpMediaViewsL10n['settings']['months'] = $months;
        }

        $wpUtilSettings = json_encode(['ajax' => ['url' => '/admin/ajax']]);

        $wpMediaModelsL10n = json_encode([
            'settings' => [
                'ajaxurl' => '\/admin\/ajax',
                'post' => ['id' => 0]
            ]
        ]);

        $uiAutocompleteL10n = json_encode([
            'noResults' => 'Результатов не найдено.',
            'oneResult' => 'Найден 1 результат. Для перемещения используйте клавиши вверх/вниз.',
            'manyResults' => 'Найдено результатов: %d. Для перемещения используйте клавиши вверх/вниз.',
            'itemSelected' => 'Объект выбран.'
        ]);

        $wpLinkL10n = json_encode([
            'title' => 'Вставить/изменить ссылку',
            'update' => 'Обновить',
            'save' => 'Добавить ссылку',
            'noTitle' => '(без названия)',
            'noMatchesFound' => 'Результатов не найдено.',
            'linkSelected' => 'Ссылка выбрана.',
            'linkInserted' => 'Ссылка вставлена.'
        ]);

        $wpColorPickerL10n = json_encode([
            'clear' => 'Сброс',
            'clearAriaLabel' => 'Очистить цвет',
            'defaultString' => 'По умолчанию',
            'defaultAriaLabel' => 'Выбрать цвет по умолчанию',
            'pick' => 'Выбрать цвет',
            'defaultLabel' => 'Значение цвета'
        ]);

        $authcheckL10n = json_encode([
            'beforeunload' => 'Ваша сессия истекла. Вы можете войти снова с этой страницы или перейти на страницу входа.',
            'interval' => 180
        ]);

        $attachMediaBoxL10n = json_encode([
            'error' => 'Произошла ошибка. Пожалуйста, обновите страницу и повторите попытку.'
        ]);

        $imageEditL10n = json_encode([
            'error' => 'Не удалось загрузить изображение для просмотра. Пожалуйста, обновите страницу и повторите попытку.'
        ]);

        $mceViewL10n = json_encode([
            'shortcodes' => [
                'wp_caption',
                'caption',
                'gallery',
                'playlist',
                'audio',
                'video',
                'embed',
                'acf',
                'toc',
                'no_toc',
                'sitemap',
                'sitemap_pages',
                'sitemap_categories',
                'sitemap_posts',
                'ratings',
                'contact-form-7',
                'contact-form',
                'wpseo_breadcrumb',
                'companies',
                'theme_of_the_week',
                'fav_company',
                'alert',
                'badge',
                'breadcrumb',
                ' breadcrumb-item',
                'button',
                'button-group',
                'button-toolbar',
                ' caret',
                'carousel',
                'carousel-item',
                'code',
                'collapse',
                'collapsibles',
                'column',
                'container',
                'container-fluid',
                'divider',
                'dropdown',
                'dropdown-header',
                'dropdown-item',
                'emphasis',
                'icon',
                'img',
                'embed-responsive',
                'jumbotron',
                'label',
                'lead',
                'list-group',
                'list-group-item',
                'list-group-item-heading',
                'list-group-item-text',
                'media',
                'media-body',
                'media-object',
                'modal',
                'modal-footer',
                'nav',
                'nav-item',
                'page-header',
                'panel',
                'popover',
                'progress',
                'progress-bar',
                'responsive',
                'row',
                'span',
                'tab',
                'table',
                'table-wrap',
                'tabs',
                'thumbnail',
                'tooltip',
                'well',
                'avatar',
                'avatar_upload',
            ]
        ]);

        $tinymce = json_encode([
            'New document' => 'Новый документ',
            'Formats' => 'Форматы',
            'Headings' => 'Заголовки',
            'Heading 1' => 'Заголовок 1',
            'Heading 2' => 'Заголовок 2',
            'Heading 3' => 'Заголовок 3',
            'Heading 4' => 'Заголовок 4',
            'Heading 5' => 'Заголовок 5',
            'Heading 6' => 'Заголовок 6',
            'Blocks' => 'Блоки',
            'Paragraph' => 'Абзац',
            'Blockquote' => 'Цитата',
            'Div' => 'Слой',
            'Preformatted' => 'Форматированный',
            'Address' => 'Адрес',
            'Inline' => 'Строки',
            'Underline' => 'Подчёркнутый',
            'Strikethrough' => 'Перечёркнутый',
            'Subscript' => 'Нижний индекс',
            'Superscript' => 'Верхний индекс',
            'Clear formatting' => 'Очистить форматирование',
            'Bold' => 'Жирный',
            'Italic' => 'Курсив',
            'Code' => 'Код',
            'Source code' => 'Исходный код',
            'Font Family' => 'Семейство шрифтов',
            'Font Sizes' => 'Размеры шрифтов',
            'Align center' => 'По центру',
            'Align right' => 'По правому краю',
            'Align left' => 'По левому краю',
            'Justify' => 'По ширине',
            'Increase indent' => 'Увеличить отступ',
            'Decrease indent' => 'Уменьшить отступ',
            'Cut' => 'Вырезать',
            'Copy' => 'Копировать',
            'Paste' => 'Вставить',
            'Select all' => 'Выделить всё',
            'Undo' => 'Отменить',
            'Redo' => 'Повторить',
            'Ok' => 'OK',
            'Cancel' => 'Отмена',
            'Close' => 'Закрыть',
            'Visual aids' => 'Визуальные подсказки',
            'Bullet list' => 'Маркированный список',
            'Numbered list' => 'Нумерованный список',
            'Square' => 'Квадрат',
            'Default' => 'По умолчанию',
            'Circle' => 'Кружок',
            'Disc' => 'Точка',
            'Lower Greek' => 'Строчные греческие буквы',
            'Lower Alpha' => 'Строчные латинские буквы',
            'Upper Alpha' => 'Заглавные латинские буквы',
            'Upper Roman' => 'Заглавные римские буквы',
            'Lower Roman' => 'Строчные римские буквы',
            'Name' => 'Имя',
            'Anchor' => 'Якорь',
            'Anchors' => 'Якоря',
            'Id should start with a letter, followed only by letters, numbers, dashes, dots, colons or underscores.' => 'Id должен начинаться с буквы, и содержать только буквы, цифры, тире, точки, запятые или знак подчеркивания.',
            'Document properties' => 'Свойства документа',
            'Robots' => 'Роботы',
            'Title' => 'Заголовок',
            'Keywords' => 'Ключевые слова',
            'Encoding' => 'Кодировка',
            'Description' => 'Описание',
            'Author' => 'Автор',
            'Image' => 'Изображение',
            'Insert/edit image' => 'Вставить/изменить картинку',
            'General' => 'Общие',
            'Advanced' => 'Дополнительно',
            'Source' => 'Источник',
            'Border' => 'Рамка',
            'Constrain proportions' => 'Сохранять пропорции',
            'Vertical space' => 'Отступ (V)',
            'Image description' => 'Описание',
            'Style' => 'Стиль',
            'Dimensions' => 'Размеры',
            'Insert image' => 'Вставить изображение',
            'Date/time' => 'Дата/время',
            'Insert date/time' => 'Вставить дату/время',
            'Table of Contents' => 'Оглавление',
            'Insert/Edit code sample' => 'Вставить/изменить фрагмент кода',
            'Language' => 'Язык',
            'Media' => 'Медиафайлы',
            'Insert/edit media' => 'Вставить/Изменить медиа',
            'Poster' => 'Постер',
            'Alternative source' => 'Альтернативный источник',
            'Paste your embed code below:' => 'Вставьте код объекта:',
            'Insert video' => 'Вставить видеофайл',
            'Embed' => 'Объект',
            'Special character' => 'Произвольный символ',
            'Right to left' => 'Справа налево',
            'Left to right' => 'Слева направо',
            'Emoticons' => 'Иконки Emoticons',
            'Nonbreaking space' => 'Неразрывный пробел',
            'Page break' => 'Разрыв страницы',
            'Paste as text' => 'Вставить как текст',
            'Preview' => 'Просмотреть',
            'Print' => 'Печать',
            'Save' => 'Сохранить',
            'Fullscreen' => 'На весь экран',
            'Horizontal line' => 'Горизонтальная линия',
            'Horizontal space' => 'Отступ (H)',
            'Restore last draft' => 'Восстановить последний черновик',
            'Insert/edit link' => 'Вставить/изменить ссылку',
            'Remove link' => 'Удалить ссылку',
            'Link' => 'Ссылка',
            'Insert link' => 'Вставить ссылку',
            'Target' => 'Цель',
            'New window' => 'Новое окно',
            'Text to display' => 'Показываемый текст',
            'Url' => 'URL',
            'The URL you entered seems to be an email address. Do you want to add the required mailto: prefix?' => 'Введённый вами адрес похож на e-mail, добавить mailto: в начало?',
            'The URL you entered seems to be an external link. Do you want to add the required http:// prefix?' => 'Введённый вами адрес похож на внешнюю ссылку, добавить http:// в начало?',
            'Color' => 'Цвет',
            'Custom color' => 'Произвольный цвет',
            'Custom...' => 'Произвольный...',
            'No color' => 'Без цвета',
            'Could not find the specified string.' => 'Не удалось найти указанную строку.',
            'Replace' => 'Заменить',
            'Next' => 'Далее',
            'Prev' => 'Назад',
            'Whole words' => 'Целые слова',
            'Find and replace' => 'Найти и заменить',
            'Replace with' => 'Замена',
            'Find' => 'Найти',
            'Replace all' => 'Заменить все',
            'Match case' => 'С учётом регистра',
            'Spellcheck' => 'Проверка орфографии',
            'Finish' => 'Завершить',
            'Ignore all' => 'Пропустить все',
            'Ignore' => 'Пропустить',
            'Add to Dictionary' => 'Добавить в словарь',
            'Insert table' => 'Вставить таблицу',
            'Delete table' => 'Удалить таблицу',
            'Table properties' => 'Свойства таблицы',
            'Row properties' => 'Свойства строки таблицы',
            'Cell properties' => 'Свойства ячейки таблицы',
            'Border color' => 'Цвет границы',
            'Row' => 'Строка',
            'Rows' => 'Строки',
            'Column' => 'Столбец',
            'Cols' => 'Столбцы',
            'Cell' => 'Ячейка',
            'Header cell' => 'Ячейка заголовка',
            'Header' => 'Заголовок',
            'Body' => 'Основная часть',
            'Footer' => 'Нижняя часть',
            'Insert row before' => 'Вставить строку до',
            'Insert row after' => 'Вставить строку после',
            'Insert column before' => 'Вставить столбец до',
            'Insert column after' => 'Вставить столбец после',
            'Paste row before' => 'Вставить строку таблицы до',
            'Paste row after' => 'Вставить строку таблицы после',
            'Delete row' => 'Удалить строку',
            'Delete column' => 'Удалить столбец',
            'Cut row' => 'Вырезать строку таблицы',
            'Copy row' => 'Копировать строку таблицы',
            'Merge cells' => 'Объединить ячейки таблицы',
            'Split cell' => 'Разделить ячейку таблицы',
            'Height' => 'Высота',
            'Width' => 'Ширина',
            'Caption' => 'Подпись',
            'Alignment' => 'Выравнивание',
            'H Align' => 'Выравнивание по горизонтали',
            'Left' => 'Слева',
            'Center' => 'По центру',
            'Right' => 'Справа',
            'None' => 'Нет',
            'V Align' => 'Выравнивание по вертикали',
            'Top' => 'Сверху',
            'Middle' => 'Посередине',
            'Bottom' => 'Снизу',
            'Row group' => 'Группа строк',
            'Column group' => 'Группа столбцов',
            'Row type' => 'Тип строки',
            'Cell type' => 'Тип ячейки',
            'Cell padding' => 'Отступы в ячейках',
            'Cell spacing' => 'Отступы между ячейками',
            'Scope' => 'Атрибут scope',
            'Insert template' => 'Вставить шаблон',
            'Templates' => 'Шаблоны',
            'Background color' => 'Цвет фона',
            'Text color' => 'Цвет текста',
            'Show blocks' => 'Показать блоки',
            'Show invisible characters' => 'Показать невидимые символы',
            'Words: {0}' => 'Слов: {0}',
            'Paste is now in plain text mode. Contents will now be pasted as plain text until you toggle this option off.' => 'Выбран режим вставки простого текста. Содержимое будет вставляться в виде простого текста, пока вы не отключите этот режим. Если вы хотите вставить текст с форматированием из Microsoft Word, попробуйте отключить этот режим. Редактор автоматически очистит текст, скопированный из Word.',
            'Rich Text Area. Press ALT-F9 for menu. Press ALT-F10 for toolbar. Press ALT-0 for help' => 'Область редактирования. Нажмите Alt-Shift-H, чтобы получить больше информации.',
            'Rich Text Area. Press Control-Option-H for help.' => 'Область редактирования. Нажмите Control-Option-H, чтобы получить больше информации.',
            'You have unsaved changes are you sure you want to navigate away?' => 'Сделанные вами изменения будут отменены, если вы уйдёте с этой страницы.',
            'Your browser doesn\'t support direct access to the clipboard. Please use the Ctrl+X/C/V keyboard shortcuts instead.' => 'Ваш браузер не поддерживает прямой доступ к буферу обмена. Используйте горячие клавиши или меню «Правка» вашего браузера.',
            'Insert' => 'Вставить',
            'File' => 'Файл',
            'Edit' => 'Изменить',
            'Tools' => 'Инструменты',
            'View' => 'Просмотр',
            'Table' => 'Таблица',
            'Format' => 'Формат',
            'Toolbar Toggle' => 'Показать/скрыть панель инструментов',
            'Insert Read More tag' => 'Вставить тег «Далее»',
            'Insert Page Break tag' => 'Вставить тег разрыва страницы',
            'Read more...' => 'Тег «Далее»',
            'Distraction-free writing mode' => 'Полноэкранный режим',
            'No alignment' => 'Без выравнивания',
            'Remove' => 'Удалить',
            'Edit ' => 'Изменить',
            'Paste URL or type to search' => 'Введите URL или слово для поиска',
            'Apply' => 'Применить',
            'Link options' => 'Настройки ссылки',
            'Visual' => 'Визуально',
            'Text' => 'Текст',
            'Keyboard Shortcuts' => 'Горячие клавиши',
            'Default shortcuts,' => 'Стандартные комбинации,',
            'Additional shortcuts,' => 'Дополнительные комбинации,',
            'Focus shortcuts:' => 'Клавиши фокуса:',
            'Inline toolbar (when an image, link or preview is selected)' => 'Всплывающая панель (при выборе изображения, ссылки или объекта)',
            'Editor menu (when enabled)' => 'Меню редактора (если включено)',
            'Editor toolbar' => 'Панель редактора',
            'Elements path' => 'Пути элементов',
            'Ctrl + Alt + letter:' => 'Ctrl + Alt + буква:',
            'Shift + Alt + letter:' => 'Shift + Alt + буква:',
            'Cmd + letter:' => 'Cmd + буква:',
            'Ctrl + letter:' => 'Ctrl + буква:',
            'Letter' => 'Буква',
            'Action' => 'Действие',
            'Warning: the link has been inserted but may have errors. Please test it.' => 'Внимание: ссылка добавлена, но может содержать ошибки. Пожалуйста, проверьте её.',
            'To move focus to other buttons use Tab or the arrow keys. To return focus to the editor press Escape or use one of the buttons.' => 'Чтобы переместить фокус на другие кнопки, используйте Tab или клавиши со стрелками. Чтобы вернуть фокус в редактор, нажмите Escape или одну из кнопок.',
            'When starting a new paragraph with one of these formatting shortcuts followed by a space, the formatting will be applied automatically. Press Backspace or Escape to undo.' => 'Если новый абзац начинается с одной из этих комбинаций и пробела, произойдёт автоматическое форматирование. Нажмите Backspace или Escape, чтобы отменить.',
            'The following formatting shortcuts are replaced when pressing Enter. Press Escape or the Undo button to undo.' => 'Следующие комбинации заменяются при нажатии Enter. Нажмите Escape или кнопку отмены, чтобы отменить.',
            'The next group of formatting shortcuts are applied as you type or when you insert them around plain text in the same paragraph. Press Escape or the Undo button to undo.' => 'Следующая группа комбинаций заменяется по мере набора или при обрамлении простого текста в том же параграфе. Нажмите Escape или кнопку отмены, чтобы отменить.'
        ]);

        return [
            'max_size' => $max_size,
            'commonL10n' => $commonL10n,
            'wpUtilSettings' => $wpUtilSettings,
            'wpMediaModelsL10n' => $wpMediaModelsL10n,
            'pluploadL10n' => $pluploadL10n,
            'thickboxL10n' => $thickboxL10n,
            'quicktagsL10n' => $quicktagsL10n,
            'uiAutocompleteL10n' => $uiAutocompleteL10n,
            'wpLinkL10n' => $wpLinkL10n,
            'wpColorPickerL10n' => $wpColorPickerL10n,
            'authcheckL10n' => $authcheckL10n,
            'attachMediaBoxL10n' => $attachMediaBoxL10n,
            'imageEditL10n' => $imageEditL10n,
            'mceViewL10n' => $mceViewL10n,
            '_wpMediaViewsL10n' => json_encode($_wpMediaViewsL10n),
            'tinymce' => $tinymce
        ];
    }

    private function compactCategory($category){
        $data =  [
            'id' => $category->id,
            'name' => $category->name,
            'link' => $category->link()
        ];

        if($category->children_count){
            $children = [];
            foreach($category->children as $child){
                $children[] = $this->compactCategory($child);
            }

            $data['children'] = $children;
        }

        return collect($data);
    }
}
