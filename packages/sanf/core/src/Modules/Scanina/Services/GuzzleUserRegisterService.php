<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Models\AuthModel;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\PostUserRegisterRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\ScaninaUserRegisterRequestDto;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaUserRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaUserSpecificationInterface;
use Sanf\Core\Modules\User\Enums\ProfileType;
use Sanf\Core\Modules\User\Repositories\ProfileRepositoryInterface;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class GuzzleUserRegisterService implements ApplicationServiceInterface
{
    public const PERSONAL = 1;
    public const COMPANY = 2;

    private AuthModel $repository;
    private ScaninaUserRepositoryInterface $scaninaRepository;
    private ScaninaUserSpecificationInterface $specification;
    private ProfileRepositoryInterface $profileRepository;
    private SanfCoreApiClient $internalApiClient;

    public function __construct(
        AuthModel $repository,
        ProfileRepositoryInterface $profileRepository,
        SanfCoreApiClient $internalApiClient,
        ScaninaUserRepositoryInterface $scaninaRepository,
        ScaninaUserSpecificationInterface $specification
    ) {
        $this->repository = $repository;
        $this->profileRepository = $profileRepository;
        $this->specification = $specification;
        $this->scaninaRepository = $scaninaRepository;
        $this->internalApiClient = $internalApiClient;
    }

    public function execute($dto = null)
    {
        /** @var PostUserRegisterRequestDto $dto */

        $user = $this->repository->findOrFail($dto->userId);
        if (empty($user->xid) || empty($user->personal_xid)) {
            $profile = $this->profileRepository->findPersonalProfileByEmail($user->username);
            if (is_null($profile)) {
                throw new UserNotFoundException('Personal Profile Not Found By Email');
            }
        } else {
            $profile = $this->profileRepository->findById($user->personal_xid);
            if (is_null($profile)) {
                throw new UserNotFoundException('Personal Profile Not Found By Id');
            }
        }

        $response = $this->internalApiClient->findCustomerById($dto->xid);
        $pic = array_filter($response['data'], function ($data) {
            return !is_null($data['PIC']) && $data['PIC'];
        });

        $requestBody = new ScaninaUserRegisterRequestDto([
            'accountTypeId' => $profile->getTypeId() == ProfileType::PERSONAL ? self::PERSONAL : self::COMPANY,
            'email' => $user->username,
            'fullName' => $dto->name,
            'phoneNumber' => $dto->msisdn,
            'picName' => $pic[0]['PIC_NAME'] ?? null,
            'picPhoneNumber' => $dto->phoneNumber,
            'position' => $dto->position,
            'countryId' => $dto->countryId,
            'cityId' => $dto->cityId ?? $dto->cityName,
            'businessSectorId' => $dto->businessSectorId,
            'password' => $dto->password,
        ]);

        $response = $this->scaninaRepository->post(
            $this->specification->register($requestBody)
        );

        return $response->data;
    }
}
