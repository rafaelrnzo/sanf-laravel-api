<?php

namespace Sanf\Dashboard\Modules\User\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Dashboard\Modules\User\Models\UserAuthModel;

class UserEloquentRepository extends AbstractEloquentRepository
{
    protected UserAuthModel $userAuthModel;

    public function __construct(UserAuthModel $userAuthModel)
    {
        $this->userAuthModel = $userAuthModel;
    }

    public function findUserAuthByBowheerId(string $bowheerId)
    {
        $userAuthModel = $this->userAuthModel
            ->newQuery()
            ->with([
                'bindingAccount' => function ($query) use ($bowheerId) {
                    return $query->where('BowheerId', '=', $bowheerId);
                },
            ])
            ->has('bindingAccount')
            ->first();

        return $this->stripEloquentModel($userAuthModel);
    }
}
