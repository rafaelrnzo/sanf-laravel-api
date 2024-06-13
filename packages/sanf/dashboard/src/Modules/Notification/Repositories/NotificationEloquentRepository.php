<?php

namespace Sanf\Dashboard\Modules\Notification\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Dashboard\Modules\Notification\Models\NotificationModel;

class NotificationEloquentRepository extends AbstractEloquentRepository
{
    private NotificationModel $notificationModel;

    public function __construct(NotificationModel $notificationModel)
    {
        $this->notificationModel = $notificationModel;
    }

    public function create(array $request)
    {
        $model = $this->notificationModel
            ->newQuery()
            ->create($request);

        return $this->stripEloquentModel($model);
    }
}
