<?php

use Illuminate\Http\Response;

return [
    \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException::class => [
        'status' => Response::HTTP_METHOD_NOT_ALLOWED,
        'code' => (string) Response::HTTP_METHOD_NOT_ALLOWED,
        'message' => __('Method not allowed'),
    ],
    \Illuminate\Validation\ValidationException::class => [
        'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
        'code' => (string) Response::HTTP_UNPROCESSABLE_ENTITY,
        'message' => __('The given data was invalid'),
    ],
    \Illuminate\Database\Eloquent\ModelNotFoundException::class => [
        'status' => Response::HTTP_NOT_FOUND,
        'code' => (string) Response::HTTP_NOT_FOUND,
        'message' => __('Data Not Found'),
    ],
    \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class => [
        'status' => Response::HTTP_NOT_FOUND,
        'code' => (string) Response::HTTP_NOT_FOUND,
        'message' => __('Route Not Found'),
    ],
    \NbsPhp\Core\Exceptions\InvalidCredentialException::class => [
        'status' => Response::HTTP_BAD_REQUEST,
        'code' => 'E_AUTH_1',
        'message' => __('Invalid Credentials'),
    ],
    \NbsPhp\Core\Exceptions\ExpiredAccessTokenException::class => [
        'status' => Response::HTTP_BAD_REQUEST,
        'code' => 'E_AUTH_2',
        'message' => __('Access Token is Expired'),
    ],
    \NbsPhp\Core\Exceptions\InvalidRefreshTokenException::class => [
        'status' => Response::HTTP_BAD_REQUEST,
        'code' => 'E_AUTH_3',
        'message' => __('Invalid Refresh Token'),
    ],
    \NbsPhp\Core\Exceptions\InvalidTokenException::class => [
        'status' => Response::HTTP_UNAUTHORIZED,
        'code' => 'E_AUTH_4',
        'message' => __('Invalid Token'),
    ],
    \NbsPhp\Core\Exceptions\UnauthorizedException::class => [
        'status' => Response::HTTP_UNAUTHORIZED,
        'code' => 'E_AUTH_5',
        'message' => __('Unauthorized'),
    ],
    \NbsPhp\Core\Exceptions\ResetPasswordFailedException::class => [
        'status' => Response::HTTP_BAD_REQUEST,
        'code' => 'E_AUTH_6',
        'message' => __('Password Reset Fail'),
    ],
    \NbsPhp\Core\Exceptions\EmailUnverifiedException::class => [
        'status' => Response::HTTP_BAD_REQUEST,
        'code' => 'E_AUTH_7',
        'message' => __('Email Unverified'),
    ],
    \NbsPhp\Core\Exceptions\EmailAlreadyExistException::class => [
        'status' => Response::HTTP_BAD_REQUEST,
        'code' => 'E_USR_1',
        'message' => __('Email Already Exist'),
    ],
];
