<?php

namespace Sanf\Dashboard\Modules\User\Models;

use NbsPhp\Core\Models\AbstractModel;

/**
 * This is a replicate of dashboard: src/Core/Notifications/Data/Models/SanfindFcmNotificationTokenModel.php.
 * serves as FCM Notification Token storage for mobile sanfind users who login at the web partner.
 *
 * @since CR2025
 */
class SanfindUserFcmNotificationTokenModel extends AbstractModel
{
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $connection = 'dashboard_db';

    protected $table = 'SanfindUserFcmNotificationToken';

    protected $casts = [
        'expiresAt' => 'datetime',
    ];
}
