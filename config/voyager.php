<?php
$dateToday = date('Y-m-d');

return [

    'user' => [
        'add_default_role_on_register' => true,
        'default_role'                 => 'user',
        'default_cover' => '/img/default-cover.png',
        'default_avatar' => '/img/default-avatar.jpg',
        'redirect' => '/admin?date=' . $dateToday,
    ],

    'controllers' => [
        'namespace' => 'TCG\\Voyager\\Http\\Controllers',
    ],


    'models' => [
        //'namespace' => 'App\\Models\\',
    ],

    'storage' => [
        'disk' => 'public',
    ],

    'hidden_files' => false,


    'database' => [
        'tables' => [
            'hidden' => ['migrations', 'data_rows', 'data_types', 'menu_items', 'password_resets', 'permission_role', 'personal_access_tokens', 'settings'],
        ],
        'autoload_migrations' => true,
    ],

    'multilingual' => [
        /*
         * Set whether or not the multilingual is supported by the BREAD input.
         */
        'enabled' => true,

        /*
         * Select default language
         */
        'default' => 'pt',

        /*
         * Select languages that are supported.
         */
        'locales' => [
            'en',
            'pt',
        ],
    ],


    'dashboard' => [
        // Add custom list items to navbar's dropdown
        'navbar_items' => [
            'voyager::generic.profile' => [
                'route' => 'voyager.profile',
                'classes' => 'class-full-of-rum',
                'icon_class' => 'voyager-person',
            ],
            'voyager::generic.home' => [
                'route'        => '/',
                'icon_class'   => 'voyager-home',
                'target_blank' => true,
            ],
            'voyager::generic.logout' => [
                'route'      => 'voyager.logout',
                'icon_class' => 'voyager-power',
            ],
        ],

        'widgets' => [
            'TCG\\Voyager\\Widgets\\UserDimmer',
            'TCG\\Voyager\\Widgets\\PostDimmer',
            'TCG\\Voyager\\Widgets\\PageDimmer',
        ],

    ],

    'bread' => [
        'add_menu_item' => true,
        'default_menu' => 'admin',
        'add_permission' => true,
        'default_role' => 'admin',
    ],


    'primary_color' => '#673AB7',
    'show_dev_tips' => true,
    'additional_css' => [
        'css/admin-overrides.css',
    ],

    'additional_js' => [
        'js/admin.js',
        'js/admin/metrics.js',
        'libs/chart.js/dist/Chart.min.js'
    ],

    'googlemaps' => [
        'key'    => env('GOOGLE_MAPS_KEY', ''),
        'center' => [
            'lat' => env('GOOGLE_MAPS_DEFAULT_CENTER_LAT', '32.715738'),
            'lng' => env('GOOGLE_MAPS_DEFAULT_CENTER_LNG', '-117.161084'),
        ],
        'zoom' => env('GOOGLE_MAPS_DEFAULT_ZOOM', 11),
    ],

    'settings' => [
        // Enables Laravel cache method for
        // storing cache values between requests
        'cache' => false,
    ],
    'compass_in_production' => false,

    'media' => [
        'allowed_mimetypes' => [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/bmp',
            'video/mp4',
        ],
        'path'                => '/',
        'show_folders'        => true,
        'allow_upload'        => true,
        'allow_move'          => true,
        'allow_delete'        => true,
        'allow_create_folder' => true,
        'allow_rename'        => true,
        /*'watermark'           => [
            'source'         => 'watermark.png',
            'position'       => 'bottom-left',
            'x'              => 0,
            'y'              => 0,
            'size'           => 15,
       ],
       'thumbnails'          => [
           [
                'type'  => 'fit',
                'name'  => 'fit-500',
                'width' => 500,
                'height'=> 500
           ],
       ]*/
    ],
];
