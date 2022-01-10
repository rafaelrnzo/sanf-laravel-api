<?php

return [
    'repositories' => [
        'profile' => \NbsPhp\Core\Repositories\MockProfileRepository::class,
        'user' => \NbsPhp\Core\Repositories\EloquentUserRepository::class,
    ],

    'services' => [
        'register-by-email' => \NbsPhp\Core\Services\RegisterByEmailService::class,
        'register-by-google' => \NbsPhp\Core\Services\RegisterByGoogleService::class,
        'register-by-apple' => \NbsPhp\Core\Services\RegisterByAppleService::class,
        'activate-user' => \NbsPhp\Core\Services\ActivateUserService::class,
        'verify-email' => \NbsPhp\Core\Services\VerifyEmailService::class,
    ],

    'transformers' => [
        'login' => \Sanf\Api\Modules\User\Transformers\LoginTransformer::class,
        'logout' => \NbsPhp\Core\Transformers\LogoutTransformer::class,
        'profile' => \Sanf\Api\Modules\User\Transformers\ProfileSimpleTransformer::class,
    ],

    'notifications' => [
        'reset-password' => \Sanf\Core\Modules\User\Notifications\ResetPasswordNotification::class,
        'verify-email' => \Sanf\Core\Modules\User\Notifications\VerifyEmailNotification::class,
        'user-activation' => \Sanf\Core\Modules\User\Notifications\UserActivationNotification::class,
    ],

    'views' => [
//        'reset-password' => 'core::auth.reset-password',
        'reset-password' => 'core::pages.install-mobile-app',
        'verify-email' => 'core::layouts.email-verified',
        'user-activation' => 'core::pages.install-mobile-app',
    ],

    'features' => [
        'strict-reset-password' => false
    ],

    'table_names' => [
        'user_auth' => 'user_auth',
        'password_reset' => 'password_reset',
        'status' => 'user_status',
        'device_platform' => 'device_platform',
        'auth_provider' => 'auth_provider',
        'user_session' => 'user_session',
        'entity_type' => 'user_entity_type',
    ],

    'urls' => [
        'email_verify' => env('AUTH_EMAIL_VERIFY_URL'),
        'email_verify_ios' => env('AUTH_EMAIL_VERIFY_IOS_URL', env('AUTH_EMAIL_VERIFY_URL')),
        'reset_password' => env('AUTH_RESET_PASS_URL'),
        'reset_password_ios' => env('AUTH_RESET_PASS_IOS_URL', env('AUTH_RESET_PASS_URL')),
        'user_activation' => env('AUTH_ACTIVATION_URL'),
        'user_activation_ios' => env('AUTH_ACTIVATION_IOS_URL', env('AUTH_ACTIVATION_URL')),
    ],

    'input_validations' => [
        'password' => [
            'rule' => $passwordValidationRule = ['required', 'min:8', 'regex:/^(?=.*\d)(?=.*[a-zA-Z])/'],
            'messages' => $passwordValidationMessage = [
                'regex' => 'Password must be alphanumeric'
            ],
        ],

        'login' => [
            'rules' => [
                'username' => 'required',
                'password' => 'required'
            ],
            'messages' => []
        ],
        'change_password' => [
            'rules' => [
                'password' => $passwordValidationRule
            ],
            'messages' => [
                'password.regex' => $passwordValidationMessage
            ]
        ],
        'reset_password' => [
            'rules' => [
                'token' => 'required',
                'password' => $passwordValidationRule
            ],
            'messages' => [
                'password.regex' => $passwordValidationMessage
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
            'provider' => 'mobile-user-provider'
        ],
//        'external' => [
//            'driver' => 'basic-auth',
//            'provider' => 'mobile-auth'
//        ],
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
        'mobile-user-provider' => [
            'driver' => 'eloquent-mobile-user-provider',
            'model' => \NbsPhp\Core\Models\AuthModel::class,
        ],
        'mobile-client-user-provider' => [
            'client_id' => env('APP_CLIENT_ID'),
            'client_secret' => env('APP_CLIENT_SECRET'),
        ],
        'core-h2h-user-provider' => [
            'client_id' => env('CORE_H2H_CLIENT_ID'),
            'client_secret' => env('CORE_H2H_CLIENT_SECRET'),
        ],
//        'api-user-provider' => [
//            'driver' => 'eloquent-api-user-provider',
//            'model' => \NbsPhp\Core\Models\ApiAuthModel::class,
//        ],
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
                'uri' => "v1/auth/user-app",
                'name' => 'auth.user-app',
                'action' => "{$namespace}AuthController@loginApp",
                'middleware' => ['basic-auth-config:mobile-client-user-provider'],
            ],
            [
                'method' => 'post',
                'uri' => "{$routePrefix}/refresh-session",
                'name' => 'token.refresh',
                'action' => "{$namespace}AuthController@refreshToken",
                'middleware' => ['auth'],
            ],
            [
                'method' => 'post',
                'uri' => "{$routePrefix}",
                'name' => 'register.email',
                'action' => "Sanf\Api\Modules\User\Controllers\AuthController@register",
                'middleware' => ['auth'],
            ],
            [
                'method' => 'post',
                'uri' => "{$routePrefix}/log-in",
                'name' => 'login.email',
                'action' => "{$namespace}AuthController@login",
                'middleware' => ['auth'],
            ],
            [
                'method' => 'post',
                'uri' => "{$routePrefix}/request-forgot-password",
                'name' => 'password.email',
                'action' => "{$namespace}ForgotPasswordController@sendResetLinkEmail",
                'middleware' => ['auth'],
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
                'uri' => "pages/reset-password",
                'name' => 'password.request',
                'action' => "{$namespace}ForgotPasswordController@showLinkRequestForm",
                'middleware' => [],
            ],
            [
                'method' => 'get',
                'uri' => "{$routePrefix}/reset-password",
                'name' => 'password.reset',
                'action' => "{$namespace}ResetPasswordController@showResetForm",
                'middleware' => ['auth'],
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
                'method' => 'post',
                'uri' => "{$routePrefix}/request-email-verification",
                'name' => 'password.email',
                'action' => "{$namespace}AuthController@requestEmailVerification",
                'middleware' => ['auth'],
            ],
            [
                'method' => 'post',
                'uri' => "{$routePrefix}/email-verification",
                'name' => 'email.verify-from-app',
                'action' => "Sanf\Api\Modules\User\Controllers\AuthController@verifyEmailByApp",
                'middleware' => [],
            ],
            [
                'method' => 'get',
                'uri' => "pages/verify-email",
                'name' => 'email.verify',
                'action' => "Sanf\Api\Modules\User\Controllers\AuthController@verifyEmailPage",
                'middleware' => [],
            ],
            [
                'method' => 'post',
                'uri' => "{$routePrefix}/request-activation",
                'name' => 'user.request-activation',
                'action' => "{$namespace}AuthController@requestActivation",
                'middleware' => ['auth'],
            ],
            [
                'method' => 'post',
                'uri' => "{$routePrefix}/activation",
                'name' => 'user.activate-from-app',
                'action' => "Sanf\Api\Modules\User\Controllers\AuthController@userActivationByApp",
                'middleware' => [],
            ],
            [
                'method' => 'get',
                'uri' => "pages/activation",
                'name' => 'user.activate',
                'action' => "{$namespace}AuthController@userActivationPage",
                'middleware' => [],
            ],
            [
                'method' => 'get',
                'uri' => "{$routePrefix}/has-registered",
                'name' => 'user.has-registered',
                'action' => "{$namespace}AuthController@emailHasRegistered",
                'middleware' => ['auth'],
            ],
            [
                'method' => 'get',
                'uri' => "{$routePrefix}/me",
                'name' => 'user.profile',
                'action' => "Sanf\Api\Modules\User\Controllers\ProfileController@getMyProfile",
                'middleware' => ['auth'],
            ],
            //TODO MOVE TO OAUTH PACKAGE
            [
                'method' => 'post',
                'uri' => "{$routePrefix}/log-in/google",
                'name' => 'login.google',
                'action' => "{$namespace}OAuthController@loginGoogle",
                'middleware' => ['auth'],
            ],
            [
                'method' => 'post',
                'uri' => "{$routePrefix}/log-in/apple",
                'name' => 'login.apple',
                'action' => "{$namespace}OAuthController@loginApple",
                'middleware' => ['auth'],
            ],
            [
                'method' => 'post',
                'uri' => "{$routePrefix}/register/google",
                'name' => 'register.google',
                'action' => "Sanf\Api\Modules\User\Controllers\OAuthController@registerGoogle",
                'middleware' => ['auth'],
            ],
            [
                'method' => 'post',
                'uri' => "{$routePrefix}/register/apple",
                'name' => 'register.apple',
                'action' => "Sanf\Api\Modules\User\Controllers\OAuthController@registerApple",
                'middleware' => ['auth'],
            ],
        ],
    ],
];
