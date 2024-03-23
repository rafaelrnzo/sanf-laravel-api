<?php

namespace NbsPhp\Core\Services;

use NbsPhp\Core\Jwt\JWTHelper;

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

    public function execute($dto = null)
    {
        $token = $this->jwt->getAppToken();
        $accessTokenExpiredAt = $this->jwt->getDecodedAppToken()->exp;

        //TODO DTO
        return (object) [
            'accessToken' => $token,
            'accessExpiredAt' => $accessTokenExpiredAt,
        ];
    }
}
