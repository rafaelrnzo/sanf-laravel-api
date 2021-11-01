<?php

namespace Sanf\Core\Modules\Financing\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Integration\InternalApiClient;

class UploadFinancingDocumentService implements ApplicationServiceInterface
{

    private InternalApiClient $client;

    public function __construct(InternalApiClient $client)
    {
        $this->client = $client;
    }

    public function execute($dto = null)
    {
        $data = $this->client->uploadFinancingAsset($dto);

        return (bool)($data['status'] ?? null);
    }
}