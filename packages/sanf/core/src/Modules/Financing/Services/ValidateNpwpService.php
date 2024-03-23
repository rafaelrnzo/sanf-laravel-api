<?php

namespace Sanf\Core\Modules\Financing\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class ValidateNpwpService implements ApplicationServiceInterface
{
    private SanfCoreApiClient $client;

    public function __construct(SanfCoreApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * @throw Exception
     */
    public function execute($dto = null)
    {
        try {
            $data = $this->client->validateNpwp($dto->user_id);
        } catch (SanfInternalApiDataNotFoundException $exception) {
            return false;
        }
        $code = $data['code'] ?? null;

        return $code === 'S_GetData';
    }
}
