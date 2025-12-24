<?php

namespace Sanf\Core\Modules\Payment\Repositories;

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
}
