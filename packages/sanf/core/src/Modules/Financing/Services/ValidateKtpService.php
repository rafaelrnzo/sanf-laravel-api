<?php

namespace Sanf\Core\Modules\Financing\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\InternalApiClient;

class ValidateKtpService implements ApplicationServiceInterface
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
            $data = $this->client->validateKtp($dto->xid);
        } catch (SanfInternalApiDataNotFoundException $exception) {
            return false;
        }
        $code = $data['code'] ?? null;
        return ($code === 'S_GetData');
    }
}
