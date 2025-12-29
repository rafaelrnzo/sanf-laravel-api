<?php

namespace Sanf\Core\Modules\Log\UseCases;

use Sanf\Core\Modules\Log\Payloads\CreateWebhookLogPayload;
use Sanf\Core\Modules\Log\Repositories\WebhookLogRepositoryInterface;

final class WebhookLogUseCase
{
    protected WebhookLogRepositoryInterface $repository;

    public function __construct(WebhookLogRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function create(CreateWebhookLogPayload $payload)
    {
        return $this->repository->create($payload->toArray());
    }
}
