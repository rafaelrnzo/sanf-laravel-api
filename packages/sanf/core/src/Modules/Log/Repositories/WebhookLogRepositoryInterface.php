<?php

namespace Sanf\Core\Modules\Log\Repositories;

use Sanf\Core\Modules\Log\Models\WebhookLogModel;

interface WebhookLogRepositoryInterface
{
    public function create(array $data): WebhookLogModel;
}
