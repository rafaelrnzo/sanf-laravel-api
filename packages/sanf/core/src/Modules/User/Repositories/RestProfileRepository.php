<?php

namespace Sanf\Core\Modules\User\Repositories;

use Sanf\Core\Modules\User\Entities\ProfileEntityInterface;
use Sanf\Core\Modules\User\Entities\RestProfileEntityFactory;
use Sanf\Core\Modules\User\Enums\ProfileType;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\InternalApiClient;

class RestProfileRepository implements ProfileRepositoryInterface
{
    protected InternalApiClient $client;
    protected RestProfileEntityFactory $factory;

    public function __construct(
        InternalApiClient $client,
        RestProfileEntityFactory $factory
    ) {
        $this->client = $client;
        $this->factory = $factory;
    }

    public function findById($customerId): ?ProfileEntityInterface
    {
        try {
            $response = $this->client->findCustomerById($customerId);
            if (empty($response['data'])) {
                return null;
            }
            return $this->factory->make($response['data'][0]);
        } catch (SanfInternalApiDataNotFoundException $exception) {
            return null;
        }
    }

    public function findByEmail($email): array
    {
        try {
            $response = $this->client->findCustomerByEmail($email);
            return array_map(function ($item) {
                return $this->factory->make($item);
            }, $response['data']);
        } catch (SanfInternalApiDataNotFoundException $exception) {
            return [];
        }
    }

    public function findPersonalProfileByEmail($email): ?ProfileEntityInterface
    {
        try {
            $response = $this->client->findCustomerByEmail($email);
            $profile = (collect($response['data'])->where('ID_IDENTITY', ProfileType::PERSONAL)->first());
            if (empty($response['data'])) {
                return null;
            }
            return $this->factory->make($profile);

        } catch (SanfInternalApiDataNotFoundException $exception) {
            return null;
        }
    }
}
