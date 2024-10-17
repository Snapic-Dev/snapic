<?php

return [
    'default' => 'default',
    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'Snapic Swagger UI',
            ],

            'routes' => [
                /*
                 * Route for accessing API documentation interface
                */
                'api' => 'api/documentation',
            ],
            'paths' => [
                /*
                 * Edit to include full URL in UI for assets
                */
                'use_absolute_path' => env('L5_SWAGGER_USE_ABSOLUTE_PATH', true),

                /*
                 * File name of the generated JSON documentation file
                */
                'docs_json' => 'api-docs.json',

                /*
                 * File name of the generated YAML documentation file
                */
                'docs_yaml' => 'api-docs.yaml',

                /*
                * Set this to `json` or `yaml` to determine which documentation file to use in UI
                */
                'format_to_use_for_docs' => env('L5_FORMAT_TO_USE_FOR_DOCS', 'json'),

                /*
                 * Absolute paths to directory containing the Swagger annotations are stored.
                */
                'annotations' => [
                    base_path('app/Swagger'),
                ],

            ],
        ],
    ],
    'defaults' => [
        'routes' => [
            /*
             * Route for accessing parsed Swagger annotations.
            */
            'docs' => 'docs',

            /*
             * Route for OAuth2 authentication callback.
            */
            'oauth2_callback' => 'api/oauth2-callback',

            /*
             * Middleware allows to prevent unexpected access to API documentation
            */
            'middleware' => [
                'api' => [],
                'asset' => [],
                'docs' => [],
                'oauth2_callback' => [],
            ],

            /*
             * Route Group options
            */
            'group_options' => [],
        ],

        'paths' => [
            /*
             * Absolute path to location where parsed annotations will be stored
            */
            'docs' => storage_path('api-docs'),

            /*
             * Absolute path to directory where to export views
            */
            'views' => base_path('resources/views/vendor/l5-swagger'),

            /*
             * Edit to set the API's base path
            */
            'base' => env('L5_SWAGGER_BASE_PATH', null),

            /*
             * Edit to set path where Swagger UI assets should be stored
            */
            'swagger_ui_assets_path' => env('L5_SWAGGER_UI_ASSETS_PATH', 'vendor/swagger-api/swagger-ui/dist/'),

            /*
             * Absolute path to directories that should be excluded from scanning
             * @deprecated Please use `scanOptions.exclude`
             * `scanOptions.exclude` overwrites this
            */
            'excludes' => [],
        ],

        'scanOptions' => [
            'analyser' => null,
            'analysis' => null,
            'processors' => [],
            'pattern' => null,

            /*
             * Absolute path to directories that should be excluded from scanning
             */
            'exclude' => [],

            /*
             * Allows to generate specs for OpenAPI 3.0.0 or 3.1.0.
             */
            'open_api_spec_version' => env('L5_SWAGGER_OPEN_API_SPEC_VERSION', \L5Swagger\Generator::OPEN_API_DEFAULT_SPEC_VERSION),
        ],

        /*
         * API security definitions.
        */
        'securityDefinitions' => [
            'securitySchemes' => [
                /*
                 * Laravel Passport OAuth2 configuration
                */
                'passport' => [
                    'type' => 'oauth2',
                    'description' => 'Laravel Passport OAuth2 security.',
                    'in' => 'header',
                    'scheme' => 'https',
                    'flows' => [
                        'password' => [
                            'authorizationUrl' => config('app.url') . '/oauth/authorize',
                            'tokenUrl' => config('app.url') . '/oauth/token',
                            'refreshUrl' => config('app.url') . '/token/refresh',
                            'scopes' => [],
                        ],
                    ],
                ],
                /*
                 * Sanctum API key security configuration
                */
                'sanctum' => [
                    'type' => 'apiKey',
                    'description' => 'Enter token in format (Bearer <token>)',
                    'name' => 'Authorization',
                    'in' => 'header',
                ],
            ],
            'security' => [
                [
                    'passport' => [],
                ],
            ],
        ],

        /*
         * Enable docs generation in development mode.
        */
        'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', true),

        /*
         * Generate a YAML copy of the documentation.
        */
        'generate_yaml_copy' => env('L5_SWAGGER_GENERATE_YAML_COPY', false),

        /*
         * Proxy IP address configuration.
        */
        'proxy' => false,

        /*
         * Swagger UI configuration.
        */
        'ui' => [
            'display' => [
                'doc_expansion' => env('L5_SWAGGER_UI_DOC_EXPANSION', 'none'),
                'filter' => env('L5_SWAGGER_UI_FILTERS', true),
            ],

            'authorization' => [
                'persist_authorization' => env('L5_SWAGGER_UI_PERSIST_AUTHORIZATION', false),
                'oauth2' => [
                    'use_pkce_with_authorization_code_grant' => false,
                ],
            ],
        ],

        /*
         * Constants used in annotations.
        */
        'constants' => [
            'L5_SWAGGER_CONST_HOST' => env('L5_SWAGGER_CONST_HOST', 'http://my-default-host.com'),
        ],
    ],
];
