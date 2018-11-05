<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Image Driver
    |--------------------------------------------------------------------------
    |
    | Intervention Image supports "GD Library" and "Imagick" to process images
    | internally. You may choose one of them according to your PHP
    | configuration. By default PHP's "GD Library" implementation is used.
    |
    | Supported: "gd", "imagick"
    |
    */

    'driver' => 'gd',

    /**
     *  Размеры изображений
     */
    'sizes' => [
        'product_list' => [
            'description' => 'Размер изображения товара в категории',
            'width' => 288,
            'height' => 288
        ],
        'product' => [
            'description' => 'Размер главного изображения в карточке товара',
            'width' => 456,
            'height' => 456
        ],
//        'article' => [
//            'description' => 'Размер изображения блога',
//            'width' => 555,
//            'height' => 370
//        ],
    ],

    /**
     *  Типы изображений
     */
    'types' => [
        'default' => [
            'description' => 'Тип по умолчанию',
        ],
        'product' => [
            'description' => 'Изображение для продукта',
            'sizes' => [
                'product',
                'product_list',
                'product_thumb'
            ]
        ],
        'news' => [
            'description' => 'Изображение для постов',
            'sizes' => [
                'news'
            ]
        ],
        'article' => [
            'description' => 'Изображение для постов',
            'sizes' => [
                'article'
            ]
        ],
        'slide' => [
            'description' => 'Изображение для слайда',
            'sizes' => [
                'slide'
            ]
        ],
        'banner'  => [
            'description' => 'Изображение баннера',
            'sizes' => [
                'banner'
            ]
        ],
        'scheme'  => [
            'description' => 'Изображение схемы',
            'sizes' => [
                'scheme'
            ]
        ]
    ]

);
