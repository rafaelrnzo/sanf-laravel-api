<?php


namespace Sanf\Core\Modules\User\Services;


use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Integration\InternalApiClient;

class GetPersonalAssistantUserService extends UserService implements ApplicationServiceInterface
{
    protected InternalApiClient $internalApiClient;

    public function __construct(AuthModel $userRepository, InternalApiClient $internalApiClient)
    {
        parent::__construct($userRepository);
        $this->internalApiClient = $internalApiClient;
    }

    public function execute($dto = null)
    {
        $user = $this->userRepository->newQuery()->find($dto->userId);
        if (!$user) {
            throw new UserNotFoundException();
        }
        $response = $this->internalApiClient->findCustomerById($user->personal_xid);
        $profile = collect($response['data'])
            ->map(function ($item) {
                return (object)[
                    'msisdn' => $item['NO_AE'],
                    'has_contract' => !is_null($item['F_KONTRAK'])
                ];
            })->first();

        $profile->message = ''; //TODO CONFIGURABLE MESSAGE
        return $profile;
    }
}
