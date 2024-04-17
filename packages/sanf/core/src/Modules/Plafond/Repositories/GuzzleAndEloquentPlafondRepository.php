<?php

namespace Sanf\Core\Modules\Plafond\Repositories;

use Sanf\Core\Modules\Plafond\Entities\PlafondEntityFactory;
use Sanf\Core\Modules\Plafond\Entities\PlafondEntityHistoryFactory;
use Sanf\Core\Modules\Plafond\Entities\PlafondEntityInterface;
use Sanf\Core\Modules\Plafond\Models\PlafondTypeModel;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class GuzzleAndEloquentPlafondRepository implements PlafondRepositoryInterface
{
    protected SanfCoreApiClient $client;
    protected PlafondEntityFactory $factory;
    protected PlafondEntityHistoryFactory $historyFactory;
    protected PlafondTypeModel $plafondTypeModel;

    public function __construct(
        SanfCoreApiClient $client,
        PlafondEntityFactory $factory,
        PlafondEntityHistoryFactory $historyFactory,
        PlafondTypeModel $plafondTypeModel
    ) {
        $this->client = $client;
        $this->factory = $factory;
        $this->historyFactory = $historyFactory;
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

    public function getHistoryByProfile($xid): array
    {
        try {
            $response = $this->client->getCustomerPlafondHistories($xid);

            return array_map(function ($item) {
                $item['type'] = $this->plafondTypeModel->find($item['P_CODE'])->toArray();

                return $this->historyFactory->make($item);
            }, $response['data']);
        } catch (SanfInternalApiDataNotFoundException $exception) {
            return [];
        }
    }

    public function getByProfileAndType($profileXid, $typeId): ?PlafondEntityInterface
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

    public function submitApplication($profileXid, $typeId, $amount = 0, $notes = null)
    {
        $response = $this->client->requestPlafond($profileXid, $typeId, $amount, $notes);

        return $response['status'];
    }
}
