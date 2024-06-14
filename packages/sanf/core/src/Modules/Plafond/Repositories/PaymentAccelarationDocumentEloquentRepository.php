<?php

namespace Sanf\Core\Modules\Plafond\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\Plafond\Models\PaymentAccelarationDocumentModel;

class PaymentAccelarationDocumentEloquentRepository extends AbstractEloquentRepository implements PaymentAccelarationDocumentRepositoryInterface
{
    private PaymentAccelarationDocumentModel $model;

    public function __construct(
        PaymentAccelarationDocumentModel $model
    ) {
        $this->model = $model;
    }

    /**
     * @param array $request
     * @return PaymentAccelarationDocumentModel
     */
    public function create(array $request): PaymentAccelarationDocumentModel
    {
        return $this->model->query()->create($request);
    }

    /**
     * @param string $id
     * @param array $request
     * @return PaymentAccelarationDocumentModel
     */
    public function update(string $id, array $request): PaymentAccelarationDocumentModel
    {
        $model = $this->model->query()->find($id);
        $model->update($request);

        return $model;
    }

    /**
     * @param string $id
     * @return PaymentAccelarationDocumentModel|null
     */
    public function findByPlafondId(string $plafondId): ?PaymentAccelarationDocumentModel
    {
        return $this->model->query()->where('plafond_id', '=', $plafondId)->first();
    }
}
