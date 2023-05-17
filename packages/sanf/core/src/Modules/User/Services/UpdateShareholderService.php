<?php


namespace Sanf\Core\Modules\User\Services;


use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class UpdateShareholderService
{

    protected SanfCoreApiClient $client;

    public function __construct(SanfCoreApiClient $client)
    {

        $this->client = $client;
    }

    public function execute($dto)
    {
        $response = $this->client->updateShareholder($dto);

        return $response['status'];
    }
}
