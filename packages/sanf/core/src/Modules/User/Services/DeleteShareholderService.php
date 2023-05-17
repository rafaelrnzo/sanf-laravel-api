<?php


namespace Sanf\Core\Modules\User\Services;


use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class DeleteShareholderService
{

    protected SanfCoreApiClient $client;

    public function __construct(SanfCoreApiClient $client)
    {

        $this->client = $client;
    }

    public function execute($dto)
    {
        $response = $this->client->deleteShareholder($dto->id, $dto->no);

        return $response['status'];
    }
}
