<?php


namespace Sanf\Core\Modules\User;


use Sanf\Integration\InternalApiClient;

class UpdateShareholderService
{

    protected InternalApiClient $client;

    public function __construct(InternalApiClient $client)
    {

        $this->client = $client;
    }

    public function execute($dto)
    {
        $response = $this->client->updateShareholder($dto);

        return $response['status'];
    }
}