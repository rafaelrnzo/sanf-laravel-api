<?php

namespace Sanf\Core\Modules\Financing\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\InternalApiClient;

class ValidateNpwpService implements ApplicationServiceInterface
{

    private InternalApiClient $client;

    public function __construct(InternalApiClient $client)
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
        return ($code === 'S_GetData');
    }
}
