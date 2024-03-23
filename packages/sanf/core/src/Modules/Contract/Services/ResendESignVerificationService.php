<?php

namespace Sanf\Core\Modules\Contract\Services;

use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Integration\Enums\TekenAjaApiResponseErrorCodeEnum;
use Sanf\Integration\Exceptions\TekenAjaExternalApiException;
use Sanf\Integration\Exceptions\TekenAjaInvalidParameterRegistrationException;
use Sanf\Integration\Exceptions\TekenAjaRegisterCheckException;
use Sanf\Integration\Modules\TekenAja\TekenAjaApiClient;

final class ResendESignVerificationService implements ApplicationServiceInterface
{
    protected AuthModel $userRepository;
    protected TekenAjaApiClient $client;

    public function __construct(
        TekenAjaApiClient $client,
        AuthModel $userRepository
    ) {
        $this->userRepository = $userRepository;
        $this->client = $client;
    }

    /**
     * @param null $dto
     * @return bool
     * @throws UserNotFoundException
     */
    public function execute($dto = null): bool
    {
        $user = $this->userRepository->newQuery()->find($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $result = $this->client->sendVerificationMail([
            ['name' => 'nik', 'contents' => $dto->nik],
            ['name' => 'email', 'contents' => $dto->email],
        ]);

        if ($result['code'] && $result['code'] !== TekenAjaApiResponseErrorCodeEnum::USER_EXISTS_VERIFIED) {
            $this->errorHandle($result['code'], $result['message']);
        }

        return true;
    }

    private function errorHandle(string $code, $messages)
    {
        switch ($code) {
            case TekenAjaApiResponseErrorCodeEnum::INVALID_PARAMETER:
                $response = array_map(function ($item) {
                    return $item[0];
                }, $messages);
                throw new TekenAjaInvalidParameterRegistrationException(implode('|', $response));
                break;
            case TekenAjaApiResponseErrorCodeEnum::USER_DO_NOT_EXISTS:
            case TekenAjaApiResponseErrorCodeEnum::USER_EXISTS_UNVERIFIED:
            case TekenAjaApiResponseErrorCodeEnum::USER_EXISTS_CERTIFICATE_EXPIRED:
            case TekenAjaApiResponseErrorCodeEnum::NIK_EMAIL_UNMATCH:
                throw new TekenAjaRegisterCheckException($messages);
                break;
            case TekenAjaApiResponseErrorCodeEnum::SYSTEM_FAILURE:
            default:
                throw new TekenAjaExternalApiException($messages);
        }
    }
}
