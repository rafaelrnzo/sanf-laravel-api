<?php

namespace Sanf\Core\Modules\Financing\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class UploadFinancingDocumentService implements ApplicationServiceInterface
{
    private SanfCoreApiClient $client;

    public function __construct(SanfCoreApiClient $client)
    {
        $this->client = $client;
    }

    public function execute($dto = null)
    {
        $data = $this->client->uploadFinancingAsset($dto);

        return (bool) ($data['status'] ?? null);
    }
}
