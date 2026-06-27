<?php

namespace Sanf\Core\Modules\Plafond\Repositories;

use Sanf\Core\Modules\Plafond\Entities\GuzzlePlafondBowheerV2Entity;
use Sanf\Core\Modules\Plafond\Entities\GuzzlePlafondFactoringV2Entity;
use Sanf\Core\Modules\Plafond\Entities\PlafondEntityFactoringFactory;
use Sanf\Core\Modules\Plafond\Entities\PlafondEntityFactory;
use Sanf\Core\Modules\Plafond\Entities\PlafondEntityHistoryFactory;
use Sanf\Core\Modules\Plafond\Entities\PlafondEntityInterface;
use Sanf\Core\Modules\Plafond\Enums\PlafondTypeEnum;
use Sanf\Core\Modules\Plafond\Models\PlafondTypeModel;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClient;

class GuzzleAndEloquentPlafondRepository implements PlafondRepositoryInterface
{
    protected SanfCoreApiClient $client;
    protected PlafondEntityFactory $factory;
    protected PlafondEntityHistoryFactory $historyFactory;
    protected PlafondEntityFactoringFactory $factoringFactory;
    protected PlafondTypeModel $plafondTypeModel;

    public function __construct(
        SanfCoreApiClient $client,
        PlafondEntityFactory $factory,
        PlafondEntityHistoryFactory $historyFactory,
        PlafondEntityFactoringFactory $factoringFactory,
        PlafondTypeModel $plafondTypeModel
    ) {
        $this->client = $client;
        $this->factory = $factory;
        $this->historyFactory = $historyFactory;
        $this->factoringFactory = $factoringFactory;
        $this->plafondTypeModel = $plafondTypeModel;
    }

    public function getByProfile($xid): array
    {
        try {
            $response = $this->client->getCustomerPlafonds($xid);

            return array_map(function ($item) {
                $type = $this->plafondTypeModel->find($item['P_CODE']);
                $item['type'] = $type ? $type->toArray() : ['id' => $item['P_CODE'], 'name' => 'Unknown', 'title' => 'Unknown'];

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
                $item['P_CODE'] = '0' . substr($item['P_CODE'], 1);
                $type = $this->plafondTypeModel->find($item['P_CODE']);
                $item['type'] = $type ? $type->toArray() : ['id' => $item['P_CODE'], 'name' => 'Unknown', 'title' => 'Unknown'];

                return $this->historyFactory->make($item);
            }, $response['data']);
        } catch (SanfInternalApiDataNotFoundException $exception) {
            return [];
        }
    }

    public function getPlafondFactoringByProfile($xid): array
    {
        try {
            $response = $this->client->getPlafondFactoring($xid);

            return array_map(function ($item) {
                return $this->factoringFactory->make($item);
            }, $response['data']);
        } catch (SanfInternalApiDataNotFoundException $exception) {
            return [];
        }
    }

    public function getPlafondFactoringV2ByProfile($xid, $plafondCode = PlafondTypeEnum::FACTORING): array
    {
        try {
            $response = $this->client->getPlafondFactoringV2($xid, $plafondCode);

            return array_map(function ($item) {
                return new GuzzlePlafondFactoringV2Entity($item);
            }, $response['data']);
        } catch (SanfInternalApiDataNotFoundException $exception) {
            return [];
        }
    }

    public function getPlafondBowheerV2($customerId, $bowheerCode): ?GuzzlePlafondBowheerV2Entity
    {
        try {
            $response = $this->client->getPlafondBowheerV2($customerId, $bowheerCode);

            return new GuzzlePlafondBowheerV2Entity((array) $response['data']);
        } catch (SanfInternalApiDataNotFoundException $exception) {
            return null;
        }
    }

    public function getByProfileAndType($profileXid, $typeId): ?PlafondEntityInterface
    {
        try {
            $response = $this->client->getCustomerPlafondsByType($profileXid, $typeId);
            $plafond = array_merge($response['data']['header'][0]);
            $type = $this->plafondTypeModel->find($plafond['P_CODE']);
            $plafond['type'] = $type ? $type->toArray() : ['id' => $plafond['P_CODE'], 'name' => 'Unknown', 'title' => 'Unknown'];
            $plafond['items'] = $response['data']['items'];

            return $this->factory->make($plafond);
        } catch (SanfInternalApiDataNotFoundException $exception) {
            return null;
        }
    }

    public function submitApplication($profileXid, $typeId, $code, $amount = 0, $plafondId = null, $notes = null)
    {
        $response = $this->client->requestPlafond($profileXid, $typeId, $code, $amount, $plafondId, $notes);

        return $response['status'];
    }
}
