<?php


namespace Sanf\Core\Modules\User\Services;


use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class CreateShareholderService implements ApplicationServiceInterface
{

    protected SanfCoreApiClient $client;

    public function __construct(SanfCoreApiClient $client)
    {

        $this->client = $client;
    }

    public function execute($dto = null)
    {
        $response = $this->client->createShareholder($dto);

        return $response['status'];
    }
}
