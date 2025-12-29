<?php

namespace Sanf\Core\Modules\Log\Repositories;

use Sanf\Core\Modules\Log\Models\WebhookLogModel;

final class WebhookLogEloquentRepository implements WebhookLogRepositoryInterface
{
    protected WebhookLogModel $webhookLogModel;

    public function __construct(WebhookLogModel $webhookLogModel)
    {
        $this->webhookLogModel = $webhookLogModel;
    }

    public function create(array $data): WebhookLogModel
    {
        /**
         * @var WebhookLogModel
         */
        $model = $this->webhookLogModel->newQuery()->create($data);

        return $model;
    }
}
