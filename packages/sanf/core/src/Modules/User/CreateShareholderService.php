<?php


namespace Sanf\Core\Modules\User;


use Sanf\Integration\InternalApiClient;

class CreateShareholderService
{

    protected InternalApiClient $client;

    public function __construct(InternalApiClient $client)
    {

        $this->client = $client;
    }

    public function execute($dto)
    {
        $response = $this->client->createShareholder($dto);

        return $response['status'];
    }
}