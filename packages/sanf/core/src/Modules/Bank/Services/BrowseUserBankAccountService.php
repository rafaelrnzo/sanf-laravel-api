<?php

namespace Sanf\Core\Modules\Bank\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Core\Modules\Bank\Dtos\BrowseUserBankAccountRequestDto;
use Sanf\Core\Modules\Bank\Entities\GuzzleUserBankAccountEntity;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

final class BrowseUserBankAccountService implements ApplicationServiceInterface
{
    private SanfCoreApiClient $coreClient;

    public function __construct(SanfCoreApiClient $coreClient)
    {
        $this->coreClient = $coreClient;
    }

    /**
     * @param BrowseUserBankAccountRequestDto $dto
     */
    public function execute($dto = null)
    {
        // $response = $this->coreClient->getUserBankAccount($dto->profileXid);
        $response = json_decode('{"status":true,"code":"S_GetData","message":"Success","count":15,"data":[{"ID":"003","OWNER":"PT ABC","PROVIDER":"BCA","ACCOUNT_NUMBER":"123abc","IS_DEFAULT":true,"DATE_UPDATE":1683676800}]}', true);

        $data = array_map(function ($bankAccount) {
            return new GuzzleUserBankAccountEntity($bankAccount);
        }, $response['data']);

        return (object) [
            'data' => $data,
            'paginate' => (object) [
                'total' => count($data),
                'count' => count($data),
                'skip' => (int) $dto->skip,
                'limit' => (int) $dto->limit,
                'sort_by' => $dto->sortBy,
            ],
        ];
    }
}
