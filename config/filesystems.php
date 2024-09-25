<?php

return [
    'default' => 'public',

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'tmp' => [
            'driver' => 'local',
            'root'   => storage_path('app') . '/tmp',
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => str_replace('/public', '', env('APP_URL') ? env('APP_URL') : '') . '/public/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => '',
            'secret' => '',
            'region' => '',
            'bucket' => '',
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'visibility' => 'public',
        ],

        'wasabi' => [
            'driver' => 's3',
            'key' => '',
            'secret' => '',
            'region' => '',
            'bucket' => '',
            'root' => '/',
            'visibility' => 'public',
            'endpoint' => env('WASABI_ENDPOINT', 'https://s3.wasabisys.com'),
        ],

        'do_spaces' => [
            'driver' => 's3',
            'key' => env('DO_SPACES_KEY'),
            'secret' => env('DO_SPACES_SECRET'),
            'region' => env('DO_SPACES_REGION'),
            'bucket' => env('DO_SPACES_BUCKET'),
            'endpoint' => env('DO_SPACES_ENDPOINT'),
            'visibility' => 'public',
            'use_path_style_endpoint' => env('DO_USE_PATH_STYLE_ENDPOINT', false),
            'http' => [
                'verify' => false,
            ],
        ],

        'minio' => [
            'driver' => 's3',
            'endpoint' => '',
            'url' => '',
            'key' => '',
            'secret' => '',
            'region' => '',
            'bucket' => '',
            'visibility' => 'public',
            'use_path_style_endpoint' => true,
            #'bucket_endpoint' => true,
        ],

        'pushr' => [
            'driver' => 's3',
            'key' => '',
            'secret' => '',
            'region' => 'us-east-1',
            'bucket' => '',
            'url' => '',
            'endpoint' => '',
            'use_path_style_endpoint' => true,
            'visibility' => 'public',
        ],

    ],
    'defaultFilesystemDriver' => '',
];
