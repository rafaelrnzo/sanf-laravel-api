<?php

namespace NbsPhp\Core\Models;

use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Notifications\Notifiable;
use Laravel\Lumen\Auth\Authorizable;
use NbsPhp\Core\Models\NeedSetupPasswordInterface as NeedSetupPasswordContract;
use NbsPhp\Core\Traits\CanResetPassword;
use NbsPhp\Core\Traits\MustVerifyEmail;
use NbsPhp\Core\Traits\NeedSetupPassword;

/**
 * @property int                $id
 * @property string             $name
 * @property string             $username
 * @property Carbon|string|null $email_verified_at
 * @property string             $password
 * @property int                $status_id
 * @property string             $remember_token
 * @property Carbon|string|null $created_at
 * @property Carbon|string|null $updated_at
 */
class AuthModel extends AbstractModel implements
    AuthenticatableContract,
    AuthorizableContract,
    MustVerifyEmailContract,
    CanResetPasswordContract,
    NeedSetupPasswordContract
{
    use Authenticatable, Authorizable, MustVerifyEmail, NeedSetupPassword, CanResetPassword, Notifiable;

    protected $table = 'user_auth'; //overridden in constructor from config auth

    protected $fillable = [
        'name',
        'username',
        'password',
        'status_id',
        'last_login_at',
        'entity_type_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password_updated_at' => 'datetime',
    ];

    public function __construct(array $attributes = [])
    {
        $this->table = config('auth.table_names.user_auth');
        parent::__construct($attributes);
    }

    public function routeNotificationForMail()
    {
        return $this->username;
    }

    public function entityType()
    {
        return $this->belongsTo(EntityTypeModel::class, 'entity_type_id');
    }
}
