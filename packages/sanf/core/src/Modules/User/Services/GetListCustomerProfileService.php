<?php


namespace Sanf\Core\Modules\User\Services;


use Illuminate\Support\Facades\Log;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Models\AuthModel;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Integration\Exceptions\SanfInternalApiException;
use Sanf\Integration\InternalApiClient;
use function collect;

class GetListCustomerProfileService implements ApplicationServiceInterface
{
    protected $repository;
    protected $internalApiClient;

    public function __construct(AuthModel $repository, InternalApiClient $internalApiClient) //TODO REPOSITORY
    {
        $this->repository = $repository;
        $this->internalApiClient = $internalApiClient;
    }

    public function execute($dto = null)// email, userId
    {
        $user = $this->repository->newQuery()->where('username', $dto->email)->first();
        if (!$user) {
            throw new UserNotFoundException();
        }
        try {
            $response = $this->internalApiClient->findCustomerByEmail($dto->email);
        } catch (SanfInternalApiException $exception) {
            Log::info('profile: sanf data notfound for email ' . $dto->email);
            return [];
        }

        return collect($response['data'])
            ->map(function ($item) use ($user) {
                return (object)[
                    "xid" => $item['CUST_ID_SANF'],
                    "typeId" => $item['ID_IDENTITY'],
                    "typeName" => $item['DESC_IDENTITY'],
                    "fullName" => $item['IDENTITY_NAME'],
                    "email" => $item['EMAIL_ADDR'],
                    "isActive" => ($user->xid === $item['CUST_ID_SANF']),
                    "isPic" => (bool)$item['PIC']
                ];
            });
    }
}
