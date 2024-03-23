<?php

namespace Sanf\Core\Modules\User\Services;

use function collect;
use Illuminate\Support\Facades\Log;
use NbsPhp\Core\Exceptions\UserNotFoundException;
use NbsPhp\Core\Models\AuthModel;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Integration\Exceptions\SanfInternalApiException;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class GetListCustomerProfileService implements ApplicationServiceInterface
{
    protected $repository;
    protected $internalApiClient;

    public function __construct(AuthModel $repository, SanfCoreApiClient $internalApiClient) //TODO REPOSITORY
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
                return (object) [
                    'xid' => $item['CUST_ID_SANF'],
                    'typeId' => $item['ID_IDENTITY'],
                    'typeName' => $item['DESC_IDENTITY'],
                    'fullName' => $item['IDENTITY_NAME'],
                    'email' => $item['EMAIL_ADDR'], //EMAIL PIC NYA
                    'isActive' => ($user->xid === $item['CUST_ID_SANF']),
                    'isPic' => $this->decidePic($item, $user),
                ];
            });
    }

    protected function decidePic($item, $user)
    {
        return $item['EMAIL_ADDR'] == $user->username && (bool) $item['PIC'];
    }
}
