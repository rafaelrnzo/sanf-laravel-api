<?php

namespace Sanf\Dashboard\Modules\Notification\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Dashboard\Modules\User\Models\FcmNotificationTokenModel;

class FcmNotificationEloquentRepository extends AbstractEloquentRepository
{
    private FcmNotificationTokenModel $fcmSessionModel;

    public function __construct(FcmNotificationTokenModel $fcmSessionModel)
    {
        $this->fcmSessionModel = $fcmSessionModel;
    }

    public function deleteByToken(string $token)
    {
        $fcmSession = $this->fcmSessionModel->query()->where('token', '=', $token)->first();

        if ($fcmSession) {
            $fcmSession->delete();
        }

        return true;
    }
}
