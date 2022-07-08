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

    public function findUserById(int $id)
    {
        return $this->userTekenAjaModel->newQuery()->find($id);
    }

    public function findUserByEmail(string $email)
    {
        $model = $this->userTekenAjaModel
            ->newQuery()
            ->where('email', '=', $email)
            ->first();

        return $this->stripEloquentModel($model);
    }

    public function createUser(array $data)
    {
        $model = $this->userTekenAjaModel
            ->newQuery()
            ->forceCreate($data);

        return $this->stripEloquentModel($model);
    }

    public function updateUser(int $id, array $data)
    {
        $this->findUserById($id)->update($data);
        $model = $this->findUserById($id);

        return $this->stripEloquentModel($model);
    }
}
