<?php

namespace Sanf\Core\Modules\Contract\Services;

use Carbon\Carbon;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Integration\Enums\TekenAjaRegistrationErrorCodeEnum;
use Sanf\Integration\Exceptions\TekenAjaExternalApiException;
use Sanf\Integration\Exceptions\TekenAjaInvalidParameterRegistrationException;
use Sanf\Integration\TekenAjaInternalApiClient;

final class GenerateSignUrlService implements ApplicationServiceInterface
{
    protected AuthModel $userRepository;
    protected TekenAjaInternalApiClient $client;

    public function __construct(
        TekenAjaInternalApiClient $client,
        AuthModel $userRepository
    ) {
        $this->userRepository = $userRepository;
        $this->client = $client;
    }

    /**
     * @param null $dto
     * @return object
     * @throws TekenAjaExternalApiException
     * @throws TekenAjaInvalidParameterRegistrationException
     * @throws UserNotFoundException
     */
    public function execute($dto = null): object
    {
        $user = $this->userRepository->newQuery()->find($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $result = $this->client->generateSignUrl($dto->documentId, $dto->email);

        if ($result['code']) {
            $this->errorHandle($result['code'], $result['message']);
        }

        return (object)[
            'url' => $result['data']['url'] ?? null,
            'createdAt' => Carbon::now(),
        ];
    }

    private function errorHandle(string $code, $messages)
    {
        switch ($code) {
            case TekenAjaRegistrationErrorCodeEnum::INVALID_PARAMETER:
                $response = array_map(function ($item) {
                    return $item[0];
                }, $messages);
                throw new TekenAjaInvalidParameterRegistrationException(implode('|', $response));
                break;
            case TekenAjaRegistrationErrorCodeEnum::SYSTEM_FAILURE:
            default:
                throw new TekenAjaExternalApiException($messages);
        }
    }
}
