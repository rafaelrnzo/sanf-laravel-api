<?php


namespace Sanf\Core\Modules\User\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Integration\InternalApiClient;

class CreateShareholderService implements ApplicationServiceInterface
{

    protected InternalApiClient $client;

    public function __construct(InternalApiClient $client)
    {

        $this->client = $client;
    }

    public function execute($dto = null)
    {
        $response = $this->client->createShareholder($dto);

        return $response['status'];
    }
}
