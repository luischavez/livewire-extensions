<?php

return [

    /**
     * Auth configuration.
     */
    'auth' => [
        // Login timeout, 0 = indeterminated.
        'timeout' => 0,

        // Auth layout for non modal pages.
        'layout' => 'layouts.app',

        // Auth pages.
        'pages' => [
            'login'     => Luischavez\Livewire\Extensions\Auth\LoginPage::class,
            'register'  => Luischavez\Livewire\Extensions\Auth\RegisterPage::class,
        ],

        // Redirect after seconds on non modal pages.
        'redirect_after' => 10,
        // Redirect back fallback on non modal pages.
        'redirect_fallback' => 'home',

        // User model for register action.
        'model' => Illuminate\Foundation\Auth\User::class,
    ],

    /**
     * Path configuration.
     */
    'paths' => [
        'actions'   => [
            app_path('Actions'),
        ],
        'grids'     => [
            app_path('Grids'),
        ],
        'inputs'    => [
            app_path('Inputs'),
        ],
        'search'    => [
            app_path('Search'),
        ],
    ],

    /**
     * Icon configuration.
     */
    'icons' => [
        'heroicons',
    ],

    /**
     * Alert configuration.
     */
    'alerts' => [
        // Show alerts on exceptions.
        'show_on_error' => true,

        'options' => [
            'default' => [
                'dimissable' => true,
                'close_after' => 5,
                'icon' => [
                    'name' => 'bell',
                ],
            ],

            'success' => [
                'dimissable' => true,
                'close_after' => 5,
                'icon' => [
                    'name' => 'check-circle',
                ],
            ],

            'warning' => [
                'dimissable' => true,
                'close_after' => 5,
                'icon' => [
                    'name' => 'question-mark-circle',
                ],
            ],

            'info' => [
                'dimissable' => true,
                'close_after' => 5,
                'icon' => [
                    'name' => 'information-circle',
                ],
            ],

            'danger' => [
                'dimissable' => true,
                'close_after' => 5,
                'icon' => [
                    'name' => 'exclamation-circle',
                ],
            ],
        ],
    ],

];
