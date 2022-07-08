<?php

namespace Sanf\Core\Modules\Contract\Services;

use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Contract\Dto\GetESignUserResponseDto;
use Sanf\Core\Modules\Contract\Enums\UserRegistrationStatusEnum;
use Sanf\Core\Modules\Contract\Repositories\ESignRepositoryInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Integration\InternalApiClient;

final class GetESignUserService implements ApplicationServiceInterface
{
    protected AuthModel $userRepository;
    protected ESignRepositoryInterface $eSignRepository;
    protected InternalApiClient $client;

    public function __construct(
        ESignRepositoryInterface $eSignRepository,
        AuthModel $userRepository,
        InternalApiClient $client
    ) {
        $this->eSignRepository = $eSignRepository;
        $this->userRepository = $userRepository;
        $this->client = $client;
    }

    /**
     * @param null $dto
     * @return object
     * @throws UserNotFoundException
     */
    public function execute($dto = null): object
    {
        $user = $this->userRepository->newQuery()->find($dto->user_id);
        if (!$user) {
            throw new UserNotFoundException();
        }

        $response = $this->client->getAvailableESignUser($user->username);
        $result = array_map(function ($item) {
            return [
                'email' => $item['EMAIL'] ?? null,
                'msisdn' => $item['MOBILE'] ?? null,
                'nik' => $item['NIK'] ?? null,
                'fullName' => $item['NAME'] ?? null,
                'dob' => $item['DOB'] ?? null,
                'pob' => $item['POB'] ?? null,
                'gender' => (int) $item['GENDER'] ?? null,
                'address' => $item['ADDRESS'] ?? null,
                'postalCode' => (int) $item['ZIP_CODE'] ?? null,
                'statusId' => UserRegistrationStatusEnum::AVAILABLE,
            ];
        }, $response['data'])[0];

        $userTekenAja = $this->eSignRepository->findUserByEmail($user->username);
        if ($userTekenAja) {
            $result = $this->mapping($result, $userTekenAja);
        }

        return new GetESignUserResponseDto($result);
    }

    private function mapping(array $data, object $regression): array
    {
        return $data;
    }
}
