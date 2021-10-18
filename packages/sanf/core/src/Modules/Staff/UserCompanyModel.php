<?php


namespace Sanf\Core\Modules\Staff;


use NbsPhp\Core\Models\AbstractModel;
use Sanf\Core\Modules\User\AuthModel;

class UserCompanyModel extends AbstractModel
{
    protected $table = 'user_company';

    public function user()
    {
        return $this->belongsTo(AuthModel::class, 'user_id');
    }
}
