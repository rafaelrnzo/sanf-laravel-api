<?php

namespace Sanf\Core\Modules\User\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\User\Enums\ProfileType;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

final class RegisterCoreAccountService implements ApplicationServiceInterface
{
    private SanfCoreApiClient $coreClient;

    public function __construct(SanfCoreApiClient $coreClient)
    {
        $this->coreClient = $coreClient;
    }

    public function execute($dto = null)
    {
        /** @var RegisterCoreAccountRequest $dto */
        $coreAccountDataResponse = null;
        try {
            $coreAccountDataResponse = $this->coreClient->findCustomerByEmail($dto->email);
        } catch (SanfInternalApiDataNotFoundException $exception) {
            //
        }

        if (is_null($coreAccountDataResponse)) {
            $this->coreClient->registerPersonal(
                $dto->fullName,
                $dto->email,
                $dto->telephone,
                $dto->handphone,
            );

            $coreAccountDataResponse = $this->coreClient->findCustomerByEmail($dto->email);
        }

        $coreAccount = (collect($coreAccountDataResponse['data'])->where('ID_IDENTITY', ProfileType::PERSONAL)->first());

        return (object) [
            'xid' => $coreAccount['CUST_ID_SANF'],
            'typeId' => $coreAccount['ID_IDENTITY'],
            'typeName' => $coreAccount['DESC_IDENTITY'],
            'fullName' => $coreAccount['IDENTITY_NAME'],
            'email' => $coreAccount['EMAIL_ADDR'],

        ];
    }
}
