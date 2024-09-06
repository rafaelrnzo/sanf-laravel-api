<?php

namespace Sanf\Core\Modules\Scanina\Services;

use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Scanina\Dtos\PostUserRegisterRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\ScaninaUserRegisterRequestDto;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaUserRegistrationRepositoryInterface;
use Sanf\Core\Modules\Scanina\Repositories\ScaninaUserRepositoryInterface;
use Sanf\Core\Modules\Scanina\Specifications\ScaninaUserSpecificationInterface;
use Sanf\Core\Modules\User\Enums\ProfileType;
use Sanf\Core\Modules\User\Repositories\ProfileRepositoryInterface;
use Sanf\Core\Modules\User\Repositories\UserRepositoryInterface;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class GuzzleUserRegisterService implements ApplicationServiceInterface
{
    public const PERSONAL = 1;
    public const COMPANY = 2;

    private UserRepositoryInterface $repository;
    private ScaninaUserRepositoryInterface $scaninaRepository;
    private ScaninaUserSpecificationInterface $specification;
    private ProfileRepositoryInterface $profileRepository;
    private SanfCoreApiClient $internalApiClient;
    private ScaninaUserRegistrationRepositoryInterface $scaninaUserRegistrationRepository;

    public function __construct(
        UserRepositoryInterface $repository,
        ProfileRepositoryInterface $profileRepository,
        SanfCoreApiClient $internalApiClient,
        ScaninaUserRepositoryInterface $scaninaRepository,
        ScaninaUserSpecificationInterface $specification,
        ScaninaUserRegistrationRepositoryInterface $scaninaUserRegistrationRepository
    ) {
        $this->repository = $repository;
        $this->profileRepository = $profileRepository;
        $this->specification = $specification;
        $this->scaninaRepository = $scaninaRepository;
        $this->internalApiClient = $internalApiClient;
        $this->scaninaUserRegistrationRepository = $scaninaUserRegistrationRepository;
    }

    public function execute($dto = null)
    {
        /** @var PostUserRegisterRequestDto $dto */
        $user = $this->repository->findById($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }

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
            'passwordConfirmation' => $dto->passwordConfirmation,
        ]);

        if ($profile->getTypeId() == ProfileType::PERSONAL) {
            $requestBody->picName = null;
            $requestBody->picPhoneNumber = null;
            $requestBody->position = null;
        }

        $response = $this->scaninaRepository->post(
            $this->specification->register($requestBody)
        );

        $requestBodyDto = $requestBody->toArray();
        unset($requestBodyDto['password']);
        unset($requestBodyDto['passwordConfirmation']);

        $this->scaninaUserRegistrationRepository->create([
            'xid' => nano_id(),
            'profile_xid' => $dto->xid,
            'email' => $user->username,
            'snapshot_request_body' => $requestBodyDto,
            'snapshot_response_body' => $response->data,
        ]);

        return $response->data;
    }
}
