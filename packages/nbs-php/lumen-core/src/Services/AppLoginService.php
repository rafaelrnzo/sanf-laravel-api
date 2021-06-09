<?php


namespace NbsPhp\Core\Services;


use NbsPhp\Core\Exceptions\InvalidCredentialException;
use NbsPhp\Core\JWTHelper;

class AppLoginService implements ApplicationServiceInterface
{
    protected $jwt;

    /**
     * AppLoginService constructor.
     * @param $jwt
     */
    public function __construct(JWTHelper $jwt)
    {
        $this->jwt = $jwt;
    }

    public function execute($dto)
    {
        if ($dto->clientId !== config('auth.providers.app-auth.client_id')
            || $dto->clientSecret !== config('auth.providers.app-auth.client_secret')) {
            throw new InvalidCredentialException();
        }

        $token = $this->jwt->getAppToken();
        $accessTokenExpiredAt = $this->jwt->getDecodedAppToken()->exp;

        //TODO DTO
        return (object)[
            'accessToken' => $token,
            'accessExpiredAt' => $accessTokenExpiredAt,
        ];
    }

}
