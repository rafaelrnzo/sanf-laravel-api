<?php

namespace NbsPhp\Core;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Hidehalo\Nanoid\Client;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use NbsPhp\Core\Exceptions\ExpiredAccessTokenException;
use NbsPhp\Core\Exceptions\InvalidTokenException;


class JWTHelper
{
    const ENTITY_TYPE_APP = 1;
    const ENTITY_TYPE_USER = 2;

    /**
     * The JWT (decoded).
     *
     * @var object
     */
    protected $token;

    /**
     * The JWT (decoded).
     *
     * @var object
     */
    protected $refresh_token;

    /**
     * The JWT (decoded).
     *
     * @var object
     */
    protected $app_token;

    /**
     * [$decoded description]
     * @var [type]
     */
    protected $decoded;

    /**
     * [$decoded description]
     * @var [type]
     */
    protected $decoded_refresh_token;

    /**
     * [$decoded description]
     * @var [type]
     */
    protected $decoded_app_token;

    /**
     * [$key description]
     * @var string
     */
    protected $key;

    /**
     * [$includes fields in USER instance to include in JWT]
     * @var string[]
     */
    protected $includes;

    /**
     * JWT payload property to use when looking up users by primary key.
     * @var string
     */
    protected $id_field;

    /**
     * Expire (in seconds)
     * @var string
     */
    protected $expire_after;

    /**
     * Expire (in seconds)
     * @var string
     */
    protected $refresh_before;

    /**
     * The JWT Issuer
     * @var string
     */
    protected $issuer;

    /**
     * Delay in seconds before token will be valid
     * @var integer
     */
    protected $notBefore_delay;

    /**
     * Create a new helper.
     */
    public function __construct()
    {
        $this->token = null;

        $this->key = config('jwt.secret');
        if (is_null($this->key)) {
            throw new \RuntimeException("Please set 'JWT_SECRET' in env file.");
        }

        $this->expire_after = config('jwt.ttl');
        if (is_null($this->expire_after)) {
            throw new \RuntimeException("Please set 'JWT_TTL' in env file.");
        }

        $this->refresh_before = config('jwt.refresh_ttl');
        if (is_null($this->refresh_before)) {
            throw new \RuntimeException("Please set 'JWT_REFRESH_TTL' in env file.");
        }

        $this->issuer = config('jwt.issuer');
        if (is_null($this->issuer)) {
            throw new \RuntimeException("Please set 'JWT_ISSUER' in env file.");
        }

        $this->id_field = config('jwt.id_field');
        if (is_null($this->id_field)) {
            $this->id_field = 'id';
        }

        $includes = config('jwt.include');
        $this->includes = is_null($includes) ? [$this->id_field] : explode(",", $includes);
        if (!in_array($this->id_field, $this->includes)) {
            $this->includes[] = $this->id_field; // always add user id
        }
    }

    /**
     * Check if helper has a token.
     * @return boolean
     */
    public function isHealthy()
    {
        return ($this->getDecoded() !== null);
    }

    /**
     * [setToken description]
     *
     * @param String $token
     *
     * @return JWTHelper
     */
    public function setToken(string $token)
    {
        $this->token = $token;
        $this->decoded = null;

        return $this;
    }

    /**
     * Get Token String
     * @return string Token String
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Generate refresh token.
     *
     * @param $sessionId
     *
     * @return string JWT token string.
     */
    public function getRefreshToken($sessionId)
    {
        $decoded = (array) $this->getDecoded();
        $decoded['data'] = (array) $decoded['data'];
        $issuedAt = time();
        $notBefore = $issuedAt;
        $expire = $notBefore + $this->refresh_before;

        $decoded['sub'] = $sessionId;
        $decoded['iat'] = $issuedAt;
        $decoded['nbf'] = $notBefore;
        $decoded['exp'] = $expire;

        return $this->refresh_token = JWT::encode($decoded, $this->key, 'HS512');
    }

    /**
     * Generate app token.
     *
     * @return string JWT token string.
     */
    public function getAppToken()
    {
        $decoded = (array) $this->getDecodedAppToken();
        $issuedAt = time();
        $expire = $issuedAt + 131400; // 3 Month

        $decoded['ent'] = self::ENTITY_TYPE_APP;
        $decoded['iat'] = $issuedAt;
        $decoded['exp'] = $expire;
        $decoded['iss'] = $this->issuer;

        return $this->app_token = JWT::encode($decoded, $this->key, 'HS512');
    }

    /**
     * Get decoded token.
     * @return object JSON Object
     */
    public function getDecoded()
    {
        if (is_null($this->token)) return null;

        if (is_null($this->decoded)) {
            try {
                $this->decoded = JWT::decode($this->token, $this->key, ['HS512']);
            } catch (\Exception $e) {

            }
        }

        return $this->decoded;
    }

    /**
     * Get decoded refresh token.
     * @return object JSON Object
     */
    public function getDecodedRefreshToken()
    {
        if (is_null($this->refresh_token)) return null;

        if (is_null($this->decoded_refresh_token)) {
            try {
                $this->decoded_refresh_token = JWT::decode($this->refresh_token, $this->key, ['HS512']);
            } catch (\Exception $e) {

            }
        }

        return $this->decoded_refresh_token;
    }

    /**
     * Get decoded app token.
     * @return object JSON Object
     */
    public function getDecodedAppToken()
    {
        if (is_null($this->app_token)) return null;

        if (is_null($this->decoded_app_token)) {
            try {
                $this->decoded_app_token = JWT::decode($this->app_token, $this->key, ['HS512']);
            } catch (\Exception $e) {

            }
        }

        return $this->decoded_app_token;
    }

    /**
     * Generate new token.
     *
     * @param AuthenticatableContract $user .
     *
     * @return string JWT token string.
     */
    public function newToken(AuthenticatableContract $user)
    {
        $this->decoded = null;

        $issuer = $this->issuer;
        $tokenId = (new Client)->formattedId('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', 8);
        $issuedAt = time();
        $notBefore = $issuedAt;
        $expire = $notBefore + $this->expire_after;
        $jwt_key = $this->key;
        $subject = $user->{$this->id_field};

        $data = [];
        foreach ($this->includes as $k) {
            $data[$k] = $user->{$k};
        }

        $token = [
            'iss' => $issuer,
            'ent' => self::ENTITY_TYPE_USER,
            'jti' => $tokenId,
            'iat' => $issuedAt,
            'nbf' => $notBefore,
            'exp' => $expire,
            'sub' => $subject,
            'data' => $data,
        ];

        return $this->token = JWT::encode($token, $jwt_key, 'HS512');
    }

    /**
     * Generate new token.
     *
     * @param string $email
     * @param string $token
     *
     * @return string JWT token string.
     */
    public function newResetPasswordToken(string $email, string $token)
    {
        $this->decoded = null;

        $tokenId = md5(uniqid());
        $issuedAt = time();
        $notBefore = $issuedAt;
        $expire = $notBefore + $this->expire_after;
        $jwt_key = $this->key;
        $issuer = $this->issuer;

        $jwt_payload = [
            "iss" => $issuer,
            "jti" => $tokenId,
            "iat" => $issuedAt,
            "nbf" => $notBefore,
            "exp" => $expire,
            "email" => $email,
            "token" => $token,
        ];

        return $this->token = JWT::encode($jwt_payload, $jwt_key, 'HS512');
    }

    /**
     * Get value stored in token id field
     * @return string
     */
    public function getId()
    {
        $decoded = $this->getDecoded();

        if (is_null($decoded)) return null;

        if (!isset($decoded->sub)) return null;

        return isset($decoded->data)
            ? $decoded->data->{$this->id_field}
            : $decoded->sub;
    }

    /**
     * Get value stored in token id field
     * @return string
     */
    public function getEnt()
    {
        $decoded = $this->getDecoded();

        if (is_null($decoded)) return null;

        if (!isset($decoded->ent)) return null;

        return $decoded->ent;
    }

    /**
     * Refresh token
     * @return string New Token
     * @throws ExpiredAccessTokenException
     */
    public function refresh()
    {
        try {
            $decoded = (array) JWT::decode($this->token, $this->key, ['HS512']);
        } catch (\Exception $e) {
            if ($e->getMessage() == 'Expired token') {
                [$header, $payload, $signature] = explode(".", $this->token);

                $decoded = json_decode(base64_decode($payload), true);
            } else {
                throw new ExpiredAccessTokenException;
            }
        }

        $decoded['data'] = (array) $decoded['data'];

        $this->decoded = null;
        $issuedAt = time();
        $notBefore = $issuedAt;
        $expire = $notBefore + $this->expire_after;

        $decoded['iat'] = $issuedAt;
        $decoded['nbf'] = $notBefore;
        $decoded['exp'] = $expire;

        return $this->token = JWT::encode($decoded, $this->key, 'HS512');
    }

    public function invalidateToken()
    {
        // TODO INVALIDATE TOKEN
    }

    public static function verifyGoogleToken(string $token)
    {
        if (is_null($clientId = env('GOOGLE_CLIENT_ID'))) {
            throw new \RuntimeException("Please set 'GOOGLE_CLIENT_ID' in env file.");
        }

        try {
            $client = (new \GuzzleHttp\Client())->get('https://www.googleapis.com/oauth2/v3/certs');

            $jwks = json_decode($client->getBody()->getContents(), true);
        } catch (\Exception $e) {
            report($e);

            throw $e;
        }

        try {
            $payload = (array) JWT::decode($token, JWK::parseKeySet($jwks), ['RS256']);
        } catch (\Exception $e) {
            throw new InvalidTokenException();
        }

        if ($payload['aud'] !== $clientId) {
            throw new InvalidTokenException();
        }

        return $payload;
    }

    public static function verifyAppleIdToken(string $token)
    {
        if (is_null($clientId = env('APPLE_CLIENT_ID'))) {
            throw new \RuntimeException("Please set 'APPLE_CLIENT_ID' in env file.");
        }

        try {
            $client = (new \GuzzleHttp\Client())->get('https://appleid.apple.com/auth/keys');

            $jwks = json_decode($client->getBody()->getContents(), true);
        } catch (\Exception $e) {
            report($e);

            throw $e;
        }

        try {
            $payload = (array) JWT::decode($token, JWK::parseKeySet($jwks), ['RS256']);
        } catch (\Exception $e) {
            throw new InvalidTokenException();
        }

        if ($payload['aud'] !== $clientId) {
            throw new InvalidTokenException();
        }

        return $payload;
    }
}
