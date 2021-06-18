<?php

return [
    'profile_repository' => \NbsPhp\Core\Repositories\MockProfileRepository::class,

    'login_transformer' => \Sanf\Api\Modules\User\LoginTransformer::class,

    'logout_transformer' => \NbsPhp\Core\Transformers\LogoutTransformer::class,

    'profile_transformer' => \Sanf\Api\Modules\User\ProfileTransformer::class,

    'table_names' => [
        'user_auth' => 'user_auth',
        'password_reset' => 'password_reset',
        'status' => 'user_status',
        'device_platform' => 'device_platform',
        'auth_provider' => 'auth_provider',
        'user_session' => 'user_session',
        'entity_type' => 'user_entity_type',
    ],

    'rules' => [
        'login' => [
            'username' => 'required',
            'password' => 'required'
        ]
    ],

    'services' => [
        'login' => []
    ],

    'email_verify_url' => env('AUTH_EMAIL_VERIFY_URL'),
    'email_verify_ios_url' => env('AUTH_EMAIL_VERIFY_IOS_URL', env('AUTH_EMAIL_VERIFY_URL')),
    'reset_password_url' => env('AUTH_RESET_PASS_URL'),
    'reset_password_ios_url' => env('AUTH_RESET_PASS_IOS_URL', env('AUTH_RESET_PASS_URL')),

    'input_validations' => [
        'change_password' => [
            'rules' => [
                'password' => ['required', 'min:8', 'regex:/^(?=.*\d)(?=.*[a-zA-Z])/']
            ],
            'messages' => [
                'password.regex' => 'Password must be alphanumeric'
            ]
        ],
        'reset_password' => [
            'rules' => [
                'token' => 'required',
                'password' => ['required', 'min:8', 'regex:/^(?=.*\d)(?=.*[a-zA-Z])/']
            ],
            'messages' => [
                'password.regex' => 'Password must be alphanumeric'
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'api'),
        'passwords' => 'mobile-password-reset',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the Eloquent user provider.
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | Supported: "token"
    |
    */

    'guards' => [
        'api' => [
            'driver' => 'jwt-auth',
            'provider' => 'mobile-auth'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | If you have multiple user tables or models you may configure multiple
    | sources which represent each model / table. These sources may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    'providers' => [
        'mobile-auth' => [
            'driver' => 'mobile-user',
            'model' => \NbsPhp\Core\Models\AuthModel::class,
        ],
        'app-auth' => [
            'client_id' => env('APP_CLIENT_ID'),
            'client_secret' => env('APP_CLIENT_SECRET'),
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | Here you may set the options for resetting passwords including the view
    | that is your password reset e-mail. You may also set the name of the
    | table that maintains all of the reset tokens for your application.
    |
    | You may specify multiple password reset configurations if you have more
    | than one user table or model in the application and you want to have
    | separate password reset settings based on the specific user types.
    |
    | The expire time is the number of minutes that the reset token should be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    */

    'passwords' => [
        'mobile-password-reset' => [
            'provider' => 'mobile-auth',
            'table' => 'password_reset',
            'expire' => 60,
            'throttle' => 1,
        ],
    ],

    'routes' => [
        'prefix' => $routePrefix = 'v1/users',

        'namespace' => $namespace = "NbsPhp\\Core\\Controllers\\",

        'list' => [
            [
                'method' => 'post',
                'uri' => "auth/user-app",
                'name' => 'auth.user-app',
                'action' => "{$namespace}AuthController@loginApp",
                'middleware' => [],
            ],
            [
                'method' => 'post',
                'uri' => "{$routePrefix}/refresh-session",
                'name' => 'token.refresh',
                'action' => "{$namespace}AuthController@refreshToken",
                'middleware' => [],
            ],
            [
                'method' => 'post',
                'uri' => "{$routePrefix}",
                'name' => 'user.register',
                'action' => "{$namespace}AuthController@register",
                'middleware' => [],
            ],
            [
                'method' => 'post',
                'uri' => "{$routePrefix}/log-in",
                'name' => 'login.post',
                'action' => "{$namespace}AuthController@login",
                'middleware' => [],
            ],
            [
                'method' => 'post',
                'uri' => "{$routePrefix}/request-forgot-password",
                'name' => 'password.email',
                'action' => "{$namespace}ForgotPasswordController@sendResetLinkEmail",
                'middleware' => [],
            ],
            [
                'method' => 'post',
                'uri' => "{$routePrefix}/reset-password",
                'name' => 'password.update',
                'action' => "{$namespace}ResetPasswordController@reset",
                'middleware' => [],
            ],
            [
                'method' => 'get',
                'uri' => "{$routePrefix}/password/new",
                'name' => 'password.request',
                'action' => "{$namespace}ForgotPasswordController@showLinkRequestForm",
                'middleware' => [],
            ],
            [
                'method' => 'get',
                'uri' => "{$routePrefix}/reset-password",
                'name' => 'password.reset',
                'action' => "{$namespace}ResetPasswordController@showResetForm",
                'middleware' => [],
            ],
            [
                'method' => 'put',
                'uri' => "{$routePrefix}/change-password",
                'name' => 'password.change',
                'action' => "{$namespace}AuthController@changePassword",
                'middleware' => ['auth'],
            ],
            [
                'method' => 'delete',
                'uri' => "{$routePrefix}/log-out",
                'name' => 'logout',
                'action' => "{$namespace}AuthController@logout",
                'middleware' => ['auth'],
            ],
            [
                'method' => 'get',
                'uri' => "pages/verify-email/{id}/{token}",
                'name' => 'email.verify',
                'action' => "{$namespace}AuthController@verifyEmail",
                'middleware' => [],
            ],
            [
                'method' => 'get',
                'uri' => "{$routePrefix}/has-registered",
                'name' => 'user.has-registered',
                'action' => "{$namespace}AuthController@emailHasRegistered",
                'middleware' => [],
            ],
            [
                'method' => 'get',
                'uri' => "{$routePrefix}/me",
                'name' => 'user.profile',
                'action' => "{$namespace}UserController@getProfile",
                'middleware' => ['auth'],
            ],
            [
                'method' => 'patch',
                'uri' => "{$routePrefix}/me",
                'name' => 'user.profile-update',
                'action' => "{$namespace}UserController@updateProfile",
                'middleware' => ['auth'],
            ],
        ],
    ],
];
