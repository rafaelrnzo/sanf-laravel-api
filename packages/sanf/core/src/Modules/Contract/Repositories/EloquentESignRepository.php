<?php

namespace Sanf\Core\Modules\Contract\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\Contract\Models\UserTekenAjaModel;

class EloquentESignRepository extends AbstractEloquentRepository implements ESignRepositoryInterface
{
    protected UserTekenAjaModel $userTekenAjaModel;

    public function __construct(UserTekenAjaModel $userTekenAjaModel)
    {
        $this->userTekenAjaModel = $userTekenAjaModel;
    }

    public function findUserByEmail(string $email)
    {
        $model = $this->userTekenAjaModel
            ->newQuery()
            ->where('email', '=', $email)
            ->first();

        return $this->stripEloquentModel($model);
    }
}
