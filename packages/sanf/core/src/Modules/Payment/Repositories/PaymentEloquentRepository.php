<?php

namespace Sanf\Core\Modules\Payment\Repositories;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\Payment\Enums\PaymentStatusEnum;
use Sanf\Core\Modules\Payment\Models\MidtransTransactionModel;
use Sanf\Core\Modules\Payment\Models\PaymentModel;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClientV2;

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
                return $query->where('status', $value);
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
        $contractNo = $params->contractNo;

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
            ->with([
                'midtransTransaction',
                'installments' => function ($query) use ($contractNo) {
                    $query->when($contractNo, fn ($q) => $q->where('contract_no', $contractNo));
                },
            ])
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

    public function findLatestInsallmentPayment(int $installmentId, array $filters = []): ?PaymentModel
    {
        return $this->model->newQuery()
            ->where($filters)
            ->whereHas('installments', fn ($q) => $q->whereId($installmentId))
            ->orderByDesc('created_at')
            ->first();
    }

    public function findActivePendingPaymentByContract(
        int $userAuthId,
        string $userProfileXid,
        string $contractNo
    ): ?PaymentModel
    {
        return $this->model->newQuery()
            ->where('user_auth_id', $userAuthId)
            ->where('user_profile_xid', $userProfileXid)
            ->where('status', PaymentStatusEnum::PENDING)
            ->where('expired_at', '>', Carbon::now())
            ->whereHas('installments', fn ($q) => $q->where('contract_no', $contractNo))
            ->orderByDesc('created_at')
            ->first();
    }

    public function findActivePendingPaymentByContractAndDueDate(
        int $userAuthId,
        string $userProfileXid,
        string $contractNo,
        string $dueDate
    ): ?PaymentModel {
        $targetTimezone = SanfCoreApiClientV2::DEFAULT_TIMEZONE;

        $dueDateObject = Carbon::parse($dueDate, $targetTimezone);
        $startOfDay = $dueDateObject->copy()->startOfDay()->setTimezone($targetTimezone);
        $endOfDay = $dueDateObject->copy()->endOfDay()->setTimezone($targetTimezone);

        return $this->model->newQuery()
            ->where('user_auth_id', $userAuthId)
            ->where('user_profile_xid', $userProfileXid)
            ->where('status', PaymentStatusEnum::PENDING)
            ->where(function ($query) {
                $query->whereNull('expired_at')
                    ->orWhere('expired_at', '>', Carbon::now());
            })
            ->whereHas('installments', function ($query) use ($contractNo, $startOfDay, $endOfDay) {
                $query->where('contract_no', $contractNo)
                    ->whereBetween('due_date', [$startOfDay, $endOfDay]);
            })
            ->orderByDesc('created_at')
            ->first();
    }

    public function findByInstallmentDetail(string $contractNo, string $dueDate, array $filters = []): ?PaymentModel
    {
        $targetTimezone = SanfCoreApiClientV2::DEFAULT_TIMEZONE;

        $dueDateObject = Carbon::parse($dueDate, $targetTimezone);
        $startOfDay = $dueDateObject->copy()->startOfDay()->setTimezone($targetTimezone);
        $endOfDay = $dueDateObject->copy()->endOfDay()->setTimezone($targetTimezone);

        return $this->model->newQuery()
            ->where($filters)
            ->whereHas('installments', function ($q) use ($contractNo, $startOfDay, $endOfDay) {
                $q->where('contract_no', $contractNo)
                    ->whereBetween('due_date', [$startOfDay, $endOfDay]);
            })
            ->orderByDesc('created_at')
            ->first();
    }

    public function findByMidtransOrder(string $midtransOrderId, array $filters = []): ?PaymentModel
    {
        return $this->model->newQuery()
            ->where($filters)
            ->whereHas('midtransTransaction', fn ($q) => $q->where('midtrans_order_id', $midtransOrderId))
            ->first();
    }

    public function findByMidtransTransaction(string $midtransTransactionId, array $filters = []): ?PaymentModel
    {
        return $this->model->newQuery()
            ->where($filters)
            ->whereHas('midtransTransaction', fn ($q) => $q->where('midtrans_transaction_id', $midtransTransactionId))
            ->first();
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

    public function countByStatus(array $filters): Collection
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
        $encryptor = SodiumEncryption::encryptor();

        if (isset($data['raw_payload'])) {
            $data['raw_payload'] = $encryptor->encryptForJson($data['raw_payload']);
            $data['nonce'] = $encryptor->nonce()->getNonceHex();
        }

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

        $encryptor = $model->encryptor();

        if (isset($data['raw_payload'])) {
            $data['raw_payload'] = $encryptor->encryptForJson($data['raw_payload']);
        }

        return (bool) $model->update($data);
    }

    public function deleteMidtransTransaction(array $filters): bool
    {
        $model = $this->midtransTransactionModel->newQuery()->where($filters)->first();

        if ($model === null) {
            return false;
        }

        return (bool) $model->delete();
    }

    public function findMidtransTransaction(array $filters): ?MidtransTransactionModel
    {
        return $this->midtransTransactionModel->newQuery()->where($filters)->first();
    }

    public function updatePayment(array $filters, array $data): bool
    {
        $model = $this->model->newQuery()->where($filters)->first();

        if ($model === null) {
            return false;
        }

        return (bool) $model->update($data);
    }

    /**
     * @return Collection<PaymentModel>
     */
    public function getByStatuses(array $statuses, array $select = ['*'], array $filters = []): Collection
    {
        return $this->model->newQuery()
            ->select($select)
            ->whereIn('status', $statuses)
            ->where($filters)
            ->get();
    }

}
