<?php

namespace Sanf\Core\Modules\Contract\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\Contract\Models\ESignDocumentAssigneeModel;
use Sanf\Core\Modules\Contract\Models\ESignDocumentModel;
use Sanf\Core\Modules\Contract\Models\UserTekenAjaModel;

class EloquentESignRepository extends AbstractEloquentRepository implements ESignOldRepositoryInterface
{
    protected UserTekenAjaModel $userTekenAjaModel;
    protected ESignDocumentModel $eSignDocumentModel;
    protected ESignDocumentAssigneeModel $eSignDocumentAssigneeModel;

    public function __construct(
        UserTekenAjaModel $userTekenAjaModel,
        ESignDocumentModel $eSignDocumentModel,
        ESignDocumentAssigneeModel $eSignDocumentAssigneeModel
    ) {
        $this->userTekenAjaModel = $userTekenAjaModel;
        $this->eSignDocumentModel = $eSignDocumentModel;
        $this->eSignDocumentAssigneeModel = $eSignDocumentAssigneeModel;
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

    public function findDocumentById(int $id)
    {
        return $this->eSignDocumentModel->newQuery()->find($id);
    }

    public function findDocumentByDocId(string $documentId)
    {
        $model = $this->eSignDocumentModel
            ->newQuery()
            ->where('document_id', '=', $documentId)
            ->first();

        return $this->stripEloquentModel($model);
    }

    public function createDocument(array $data)
    {
        $model = $this->eSignDocumentModel
            ->newQuery()
            ->forceCreate($data);

        return $this->stripEloquentModel($model);
    }

    public function updateDocument(int $id, array $data)
    {
        $this->findDocumentById($id)->update($data);
        $model = $this->findDocumentById($id);

        return $this->stripEloquentModel($model);
    }

    public function documentAssigneeQuery($specification)
    {
        $models = $specification->buildQuery($this->eSignDocumentAssigneeModel)->get();

        return $this->stripEloquentModel($models);
    }

    public function documentAssigneeSize($specification = null)
    {
        if (!is_null($specification)) {
            return $specification->buildQuery($this->eSignDocumentAssigneeModel)->count();
        }

        return $this->$this->eSignDocumentAssigneeModel->newQuery()->select('id')->count();
    }

    public function findDocumentAssigneeById(int $id)
    {
        return $this->eSignDocumentAssigneeModel->newQuery()->find($id);
    }

    public function findDocumentAssigneeByDocId(int $userId, string $documentId)
    {
        $model = $this->eSignDocumentAssigneeModel
            ->newQuery()
            ->where('user_id', '=', $userId)
            ->where('document_id', '=', $documentId)
            ->first();

        return $this->stripEloquentModel($model);
    }

    public function createDocumentAssignee(array $data)
    {
        $model = $this->eSignDocumentAssigneeModel
            ->newQuery()
            ->forceCreate($data);

        return $this->stripEloquentModel($model);
    }

    public function updateDocumentAssignee(int $id, array $data)
    {
        $this->findDocumentAssigneeById($id)->update($data);
        $model = $this->findDocumentAssigneeById($id);

        return $this->stripEloquentModel($model);
    }
}
