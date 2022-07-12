<?php

namespace Sanf\Core\Modules\Contract\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\Contract\Models\ESignDocumentModel;
use Sanf\Core\Modules\Contract\Models\UserTekenAjaModel;

class EloquentESignRepository extends AbstractEloquentRepository implements ESignRepositoryInterface
{
    protected UserTekenAjaModel $userTekenAjaModel;
    protected ESignDocumentModel $eSignDocumentModel;

    public function __construct(
        UserTekenAjaModel $userTekenAjaModel,
        ESignDocumentModel $eSignDocumentModel
    ) {
        $this->userTekenAjaModel = $userTekenAjaModel;
        $this->eSignDocumentModel = $eSignDocumentModel;
    }

    public function findUserById(int $id)
    {
        return $this->userTekenAjaModel->newQuery()->find($id);
    }

    public function findUserByUserId(int $id)
    {
        $model = $this->userTekenAjaModel
            ->newQuery()
            ->where('user_id', '=', $id)
            ->first();

        return $this->stripEloquentModel($model);
    }

    public function findUserByEmail(string $email)
    {
        $model = $this->userTekenAjaModel
            ->newQuery()
            ->where('email', '=', $email)
            ->first();

        return $this->stripEloquentModel($model);
    }

    public function documentQuery($specification)
    {
        $models = $specification->buildQuery($this->eSignDocumentModel)->get();

        return $this->stripEloquentModel($models);
    }

    public function documentSize($specification = null)
    {
        if (!is_null($specification)) {
            return $specification->buildQuery($this->eSignDocumentModel)->count();
        }
        return $this->$this->eSignDocumentModel->newQuery()->select('id')->count();
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
