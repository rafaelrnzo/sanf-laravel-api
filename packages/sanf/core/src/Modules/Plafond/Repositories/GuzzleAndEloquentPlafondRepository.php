<?php

namespace Sanf\Core\Modules\Plafond\Repositories;


use Sanf\Core\Modules\Plafond\Entities\PlafondEntity;
use Sanf\Core\Modules\Plafond\Models\PlafondTypeModel;
use Sanf\Core\Modules\Plafond\PlafondEntityFactory;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\InternalApiClient;

class GuzzleAndEloquentPlafondRepository implements PlafondRepositoryInterface
{
    protected InternalApiClient $client;
    protected PlafondEntityFactory $factory;
    protected PlafondTypeModel $plafondTypeModel;

    public function __construct(
        InternalApiClient $client,
        PlafondEntityFactory $factory,
        PlafondTypeModel $plafondTypeModel
    ) {
        $this->client = $client;
        $this->factory = $factory;
        $this->plafondTypeModel = $plafondTypeModel;
    }

    public function getByProfile($xid): array
    {
        try {
            $response = $this->client->getCustomerPlafonds($xid);
            return array_map(function ($item) {
                $item['type'] = $this->plafondTypeModel->find($item['P_CODE'])->toArray();
                return $this->factory->make($item);
            }, $response['data']);
        } catch (SanfInternalApiDataNotFoundException $exception) {
            return [];
        }
    }

    public function getByProfileAndType($profileXid, $typeId): ?PlafondEntity
    {
        try {
            $response = $this->client->getCustomerPlafondsByType($profileXid, $typeId);
            $plafond = array_merge($response['data']['header'][0]);
            $plafond['type'] = $this->plafondTypeModel->find($plafond['P_CODE'])->toArray();
            $plafond['items'] = $response['data']['items'];
            return $this->factory->make($plafond);
        } catch (SanfInternalApiDataNotFoundException $exception) {
            return null;
        }
    }

    public function submitApplication($profileXid, $typeId, $amount)
    {
        // TODO: Implement submitApplication() method.
    }
}
