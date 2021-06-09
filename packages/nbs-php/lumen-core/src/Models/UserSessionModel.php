<?php


namespace NbsPhp\Core\Models;

class UserSessionModel extends AbstractModel
{
    protected $table = 'user_session';

    protected $dates = [
        'expired_at'
    ];
}
