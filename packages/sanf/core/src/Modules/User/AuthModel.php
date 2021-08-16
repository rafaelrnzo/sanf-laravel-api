<?php


namespace Sanf\Core\Modules\User;


class AuthModel extends \NbsPhp\Core\Models\AuthModel
{
    /**
     * @property int                $id
     * @property int                $entity_type_id
     * @property string             $username
     * @property string             $password
     * @property string             $remember_token
     * @property string             $full_name
     * @property string             $landline_number
     * @property string             $phone_number
     * @property int                $status_id
     * @property Carbon|string|null $email_verified_at
     * @property Carbon|string|null $last_login_at
     * @property Carbon|string|null $password_updated_at
     * @property Carbon|string|null $created_at
     * @property Carbon|string|null $updated_at
     * @property string xid
     */
}
