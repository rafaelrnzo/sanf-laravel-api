<?php


namespace Sanf\Core\Modules\User;


use Sanf\Integration\InternalApiClient;

class DeleteShareholderService
{

    protected InternalApiClient $client;

    public function __construct(InternalApiClient $client)
    {

        $this->client = $client;
    }

    public function execute($dto)
    {
        $response = $this->client->deleteShareholder($dto->id, $dto->no);

        return $response['status'];
    }
}