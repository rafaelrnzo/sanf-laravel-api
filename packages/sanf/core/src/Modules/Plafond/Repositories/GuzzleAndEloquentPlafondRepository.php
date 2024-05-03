<?php

namespace Sanf\Core\Modules\Plafond\Repositories;

use Sanf\Core\Modules\Plafond\Entities\PlafondEntityFactoringFactory;
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
                $item['P_CODE'] = '0' . substr($item['P_CODE'], 1);
                $item['type'] = $this->plafondTypeModel->find($item['P_CODE'])->toArray();

                return $this->historyFactory->make($item);
            }, $response['data']);
        } catch (SanfInternalApiDataNotFoundException $exception) {
            return [];
        }
    }

    public function getPlafondFactoringByProfile($xid): array
    {
        try {
            // $response = $this->client->getPlafondFactoring($xid);
            $response = json_decode('{"status":true,"code":"S_GetData","message":"Success","count":15,"data":[{"P_CODE":"003","PLAFONDHEADER_ID":"PH6","CUST_ID":"7198PROSM","P_CURRENT":"0", "P_SUBMIT":"100000000","P_USED":"100000000","P_SISA":"100000000","CUSTOMER_REVIEW":true,"CUSTOMERS":[{"NAME":"PT. Emas Perkasa Gemilang","CODE":"ABC123","EMAIL":"emas@mail.com"},{"NAME":"PT. Dominika Permata Digital","CODE":"ZXC789","EMAIL":"dominika@mail.com"}],"DATE_EXPIRED":"11-05-2023","DATE_UPDATE":"11-05-2023"}]}', true);

            return array_map(function ($item) {
                return $this->factoringFactory->make($item);
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
