<?php

namespace Sanf\Core\Modules\Payment\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\Payment\Models\MidtransTransactionModel;
use Sanf\Core\Modules\Payment\Models\PaymentModel;

final class PaymentEloquentRepository implements PaymentRepositoryInterface
{
    protected PaymentModel $model;
    protected MidtransTransactionModel $midtransTransactionModel;

    public function __construct(
        PaymentModel $model,
        MidtransTransactionModel $midtransTransactionModel
    ) {
        $this->model = $model;
        $this->midtransTransactionModel = $midtransTransactionModel;
    }

    public function listQuery(object $params)
    {
        $userAuthId = $params->userAuthId;
        $userProfileXid = $params->userProfileXid;
        $contractNo = $params->contractNo;
        $status = $params->status;

        return $this->model->newQuery()
            ->where('user_auth_id', '=', $userAuthId)
            ->where('user_profile_xid', '=', $userProfileXid)
            ->when($status, function ($query, $value) {
                return $query->where('status_id', $value);
            })
            ->when($contractNo, function ($query, $value) {
                $query->whereHas('installments', fn ($q) => $q->where('contract_no', $value));
            });
    }

    /**
     * @param object $params
     * @return Collection<PaymentModel>
     */
    public function list(object $params): Collection
    {
        $sortBy = $params->sortBy;
        $skip = $params->skip;
        $limit = $params->limit;

        switch ($sortBy) {
            case 'earliest':
            case 'oldest':
                $orderBy = 'created_at';
                $orderDirection = 'ASC';
                break;
            case 'latest':
            case 'newest':
            default:
                $orderBy = 'created_at';
                $orderDirection = 'DESC';
        }

        return $this->listQuery($params)
            ->with(['installments'])
            ->when($skip, function ($query, $skip) {
                return $query->skip($skip);
            })
            ->when($limit, function ($query, $limit) {
                return $query->limit($limit);
            })
            ->orderBy($orderBy, $orderDirection)
            ->get();
    }

    public function listCount(object $params): int
    {
        return $this->listQuery($params)->count();
    }

    public function find(array $filters): ?PaymentModel
    {
        return $this->model->newQuery()->where($filters)->first();
    }

    public function create(array $data): PaymentModel
    {
        $data = SodiumEncryption::encryptor()->encryptMultipleData($data, [], ['user_snapshot']);

        /**
         * @var PaymentModel
         */
        $model = $this->model->newQuery()->create($data);

        return $model;
    }

    public function countByStatus(array $filters)
    {
        return $this->model->newQuery()
            ->select([
                'status',
                DB::raw('COUNT(*) as total'),
            ])
            ->where($filters)
            ->groupBy('status')
            ->get();
    }

    public function createInstallments(array $filters, array $installments): void
    {
        /**
         * @var PaymentModel
         */
        $model = $this->model->newQuery()->where($filters)->first();

        if ($model) {
            $model->installments()->attach($installments);
        }
    }

    public function createMidtransTransaction(array $data): MidtransTransactionModel
    {
        /**
         * @var MidtransTransactionModel
         */
        $model = $this->midtransTransactionModel->newQuery()->create($data);

        return $model;
    }

    public function updateMidtransTransaction(array $filters, array $data): bool
    {
        $model = $this->midtransTransactionModel->newQuery()->where($filters)->first();

        if ($model === null) {
            return false;
        }

        return (bool) $model->update($data);
    }

    public function updatePayment(array $filters, array $data): bool
    {
        $model = $this->model->newQuery()->where($filters)->first();

        if ($model === null) {
            return false;
        }

        return (bool) $model->update($data);
    }
}
