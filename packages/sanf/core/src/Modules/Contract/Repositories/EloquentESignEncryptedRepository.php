<?php

namespace Sanf\Core\Modules\Contract\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\Contract\Models\ESignDocumentAssigneeEncryptedModel;
use Sanf\Core\Modules\Contract\Models\ESignDocumentEncryptedModel;
use Sanf\Core\Modules\Contract\Models\UserTekenAjaEncryptedModel;

class EloquentESignEncryptedRepository extends AbstractEloquentRepository implements ESignRepositoryInterface
{
    protected UserTekenAjaEncryptedModel $userTekenAjaModel;
    protected ESignDocumentEncryptedModel $eSignDocumentModel;
    protected ESignDocumentAssigneeEncryptedModel $eSignDocumentAssigneeModel;
    protected array $userTekenajaEncryptedFields;
    protected array $userTekenajaEncryptedJsonFields;
    protected array $esignDocumentEncryptedFields;
    protected array $esignDocumentEncryptedJsonFields;
    protected array $esignDocumentAssigneeEncryptedFields;

    public function __construct(
        UserTekenAjaEncryptedModel $userTekenAjaModel,
        ESignDocumentEncryptedModel $eSignDocumentModel,
        ESignDocumentAssigneeEncryptedModel $eSignDocumentAssigneeModel
    ) {
        $this->userTekenAjaModel = $userTekenAjaModel;
        $this->eSignDocumentModel = $eSignDocumentModel;
        $this->eSignDocumentAssigneeModel = $eSignDocumentAssigneeModel;
        $this->setupEncryptedFields();
    }

    private function setupEncryptedFields()
    {
        $this->userTekenajaEncryptedFields = [
            'email',
            'msisdn',
            'nik',
            'full_name',
            'dob',
            'pob',
            'gender',
            'address',
            'postal_code',
        ];
        $this->userTekenajaEncryptedJsonFields = [
            'selfie_file',
            'identity_file',
        ];
        $this->esignDocumentEncryptedFields = [
            'document_id',
            'document_name',
            'reference_no',
        ];
        $this->esignDocumentEncryptedJsonFields = [
            'document_file',
            'modified_by',
        ];
        $this->esignDocumentAssigneeEncryptedFields = [
            'document_id',
            'document_name',
            'reference_no',
        ];
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
        $sodiumQuery = SodiumEncryption::query();

        $model = $this->userTekenAjaModel
            ->newQuery()
            ->where($sodiumQuery->selectRaw('email'), '=', $email)
            ->first();

        return $this->stripEloquentModel($model);
    }

    public function createUser(array $data)
    {
        $data = SodiumEncryption::encryptor()->encryptMultipleData($data, $this->userTekenajaEncryptedFields, $this->userTekenajaEncryptedJsonFields);

        $model = $this->userTekenAjaModel
            ->newQuery()
            ->forceCreate($data);

        return $this->stripEloquentModel($model->fresh());
    }

    public function updateUser(int $id, array $data)
    {
        $model = $this->findUserById($id);

        if (is_null($model)) {
            return null;
        }

        $data = $model->encryptor()->encryptMultipleData($data, $this->userTekenajaEncryptedFields, $this->userTekenajaEncryptedJsonFields);

        $model->update($data);

        return $this->stripEloquentModel($model->fresh());
    }

    public function findDocumentById(int $id)
    {
        return $this->eSignDocumentModel->newQuery()->find($id);
    }

    public function findDocumentByDocId(string $documentId)
    {
        $sodiumQuery = SodiumEncryption::query();

        $model = $this->eSignDocumentModel
            ->newQuery()
            ->where($sodiumQuery->selectRaw('document_id'), '=', $documentId)
            ->first();

        return $this->stripEloquentModel($model);
    }

    public function createDocument(array $data)
    {
        $data = SodiumEncryption::encryptor()->encryptMultipleData($data, $this->esignDocumentEncryptedFields, $this->esignDocumentEncryptedJsonFields);

        $model = $this->eSignDocumentModel
            ->newQuery()
            ->forceCreate($data);

        return $this->stripEloquentModel($model->fresh());
    }

    public function updateDocument(int $id, array $data)
    {
        $model = $this->findDocumentById($id);

        if (is_null($model)) {
            return null;
        }

        $data = $model->encryptor()->encryptMultipleData($data, $this->esignDocumentEncryptedFields, $this->esignDocumentEncryptedJsonFields);

        $model->update($data);

        return $this->stripEloquentModel($model->fresh());
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
        $sodiumQuery = SodiumEncryption::query();

        $model = $this->eSignDocumentAssigneeModel
            ->newQuery()
            ->where('user_id', '=', $userId)
            ->where($sodiumQuery->selectRaw('document_id'), '=', $documentId)
            ->first();

        return $this->stripEloquentModel($model);
    }

    public function createDocumentAssignee(array $data)
    {
        $data = SodiumEncryption::encryptor()->encryptMultipleData($data, $this->esignDocumentAssigneeEncryptedFields);

        $model = $this->eSignDocumentAssigneeModel
            ->newQuery()
            ->forceCreate($data);

        return $this->stripEloquentModel($model->fresh());
    }

    public function updateDocumentAssignee(int $id, array $data)
    {
        $model = $this->findDocumentAssigneeById($id);

        if (is_null($model)) {
            return null;
        }

        $data = $model->encryptor()->encryptMultipleData($data, $this->esignDocumentAssigneeEncryptedFields);

        $model->update($data);

        return $this->stripEloquentModel($model->fresh());
    }
}
