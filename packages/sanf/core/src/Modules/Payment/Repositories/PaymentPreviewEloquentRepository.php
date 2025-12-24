<?php

namespace Sanf\Core\Modules\Payment\Repositories;

use Sanf\Core\Modules\Payment\Models\PaymentPreviewModel;

final class PaymentPreviewEloquentRepository implements PaymentPreviewRepositoryInterface
{
    protected PaymentPreviewModel $model;

    public function __construct(PaymentPreviewModel $model) {
        $this->model = $model;
    }

    public function updateOrCreate(array $attributes, array $data)
    {
        return $this->model->newQuery()->updateOrCreate($attributes, $data);
    }

    public function find(array $filters): ?PaymentPreviewModel
    {
        return $this->model->newQuery()->where($filters)->first();
    }

    public function delete(array $filters): bool
    {
        /**
         * @var ?PaymentPreviewModel
         */
        $model = $this->model->newQuery()->where($filters)->first();

        return (bool) optional($model)->delete();
    }
}
