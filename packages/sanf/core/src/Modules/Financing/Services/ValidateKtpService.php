<?php

namespace Sanf\Core\Modules\Financing\Services;

use NbsPhp\Core\Services\ApplicationServiceInterface;
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
        $data = $this->client->validateKtp($dto->user_id);

        $isValid = $data['data'] ?? null;

        return ($isValid === 'DATA EXIST');
    }
}