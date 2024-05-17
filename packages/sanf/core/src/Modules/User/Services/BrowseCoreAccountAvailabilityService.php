<?php

namespace Sanf\Core\Modules\User\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\AuthModel;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

final class BrowseCoreAccountAvailabilityService implements ApplicationServiceInterface
{
    protected AuthModel $userModel;
    protected SanfCoreApiClient $coreClient;

    public function __construct(AuthModel $userModel, SanfCoreApiClient $coreClient)
    {
        $this->userModel = $userModel;
        $this->coreClient = $coreClient;
    }

    public function execute($email = null)
    {
        $user = $this->userModel->newQuery()->where('username', $email)->first();
        $hasMobileAccount = is_null($user) === false;

        $hasCoreAccount = false;
        $coreAccountDataResponse = null;
        try {
            $coreAccountResponse = $this->coreClient->findCustomerByEmail($email);
            $hasCoreAccount = is_null($coreAccountResponse) === false;

            $coreAccountDataResponse = $coreAccountResponse['data'];
        } catch (Exception $exception) {
            Log::info('profile: sanf data notfound for email ' . $email);
        }

        $coreAccounts = collect($coreAccountDataResponse)
            ->map(function ($account) use ($email, $hasMobileAccount, $user) {
                return (object) [
                    'xid' => $account['CUST_ID_SANF'],
                    'typeId' => $account['ID_IDENTITY'],
                    'typeName' => $account['DESC_IDENTITY'],
                    'fullName' => $account['IDENTITY_NAME'],
                    'email' => $account['EMAIL_ADDR'],
                    'isActive' => $hasMobileAccount && ($user->xid === $account['CUST_ID_SANF']),
                    'isPic' => $this->decidePic($account, $email),
                ];
            });

        return (object) [
            'email' => $email,
            'hasMobileAccount' => $hasMobileAccount,
            'hasCoreAccount' => $hasCoreAccount,
            'coreAccounts' => $coreAccounts,
        ];
    }

    /**
     * @param array $account
     * @param string $email
     * @return bool
     */
    protected function decidePic(array $account, string $email = null): bool
    {
        return $account['EMAIL_ADDR'] == $email && (bool) $account['PIC'];
    }
}
