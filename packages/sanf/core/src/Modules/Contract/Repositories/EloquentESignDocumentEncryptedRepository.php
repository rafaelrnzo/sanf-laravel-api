<?php

namespace Sanf\Core\Modules\Contract\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\Contract\Models\ESignDocumentAssigneeEncryptedModel;
use Sanf\Core\Modules\Contract\Models\ESignDocumentEncryptedModel;
use Sanf\Core\Modules\Contract\Models\ESignOTPEncryptedModel;
use Sanf\Core\Modules\Contract\Models\UserAdInsEncryptedModel;

class EloquentESignDocumentEncryptedRepository extends AbstractEloquentRepository implements ESignRepositoryInterface
{
    protected UserAdInsEncryptedModel $userAdInsModel;
    protected ESignDocumentEncryptedModel $eSignDocumentModel;
    protected ESignDocumentAssigneeEncryptedModel $eSignDocumentAssigneeModel;
    protected ESignOTPEncryptedModel $eSignOTPModel;
    protected array $userAdinsEncryptedFields;
    protected array $userAdinsEncryptedJsonFields;
    protected array $esignDocumentEncryptedFields;
    protected array $esignDocumentEncryptedJsonFields;
    protected array $esignDocumentAssigneeEncryptedFields;
    protected array $eSignOTPEncryptedFields;

    public function __construct(
        UserAdInsEncryptedModel $userAdInsModel,
        ESignDocumentEncryptedModel $eSignDocumentModel,
        ESignDocumentAssigneeEncryptedModel $eSignDocumentAssigneeModel,
        ESignOTPEncryptedModel $eSignOTPModel
    ) {
        $this->userAdInsModel = $userAdInsModel;
        $this->eSignDocumentModel = $eSignDocumentModel;
        $this->eSignDocumentAssigneeModel = $eSignDocumentAssigneeModel;
        $this->eSignOTPModel = $eSignOTPModel;
        $this->setupEncryptedFields();
    }

    private function setupEncryptedFields()
    {
        $this->userAdinsEncryptedFields = [
            'email',
            'msisdn',
            'identity_no',
            'full_name',
            'date_of_birth',
            'place_of_birth',
            'gender',
            'address',
            'postal_code',
            'province',
            'city',
            'district',
            'sub_district',
        ];
        $this->userAdinsEncryptedJsonFields = [
            'selfie_file',
            'identity_file',
        ];
        $this->esignDocumentEncryptedFields = [
            'document_name',
            'reference_no',
        ];
        $this->esignDocumentEncryptedJsonFields = [
            'document_file',
            'modified_by',
        ];
        $this->esignDocumentAssigneeEncryptedFields = [
            'document_name',
            'reference_no',
        ];
        $this->eSignOTPEncryptedFields = [
            'reference_no',
            'email',
            'msisdn',
        ];
    }

    public function findUserById(int $id)
    {
        return $this->userAdInsModel->newQuery()->find($id);
    }

    public function findUserBySanfId(string $id)
    {
        $model = $this->userAdInsModel
            ->newQuery()
            ->where('sanf_id', '=', $id)
            ->first();

        if (is_null($model) === true) {
            return null;
        }

        $record = $model->toArray();
        $record['code'] = $model->password_decrypt;

        return $this->stripEloquentModel($record);
    }

    public function findUserByEmail(string $email)
    {
        $sodiumQuery = SodiumEncryption::query();

        $model = $this->userAdInsModel
            ->newQuery()
            ->where($sodiumQuery->selectRaw('email'), '=', $email)
            ->first();

        return $this->stripEloquentModel($model);
    }

    public function findUserBySanfIdAndIdentityNo(string $sanfId, string $identityNo)
    {
        $sodiumQuery = SodiumEncryption::query();

        $model = $this->userAdInsModel
            ->newQuery()
            ->where('sanf_id', '=', $sanfId)
            ->where($sodiumQuery->selectRaw('identity_no'), '=', $identityNo)
            ->first();

        return $this->stripEloquentModel($model);
    }

    public function createUser(array $data)
    {
        $data = SodiumEncryption::encryptor()->encryptBulkData($data, $this->userAdinsEncryptedFields, $this->userAdinsEncryptedJsonFields);

        $model = $this->userAdInsModel
            ->newQuery()
            ->forceCreate($data);

        return $this->stripEloquentModel($model->fresh());
    }

    public function updateUser(int $id, array $data)
    {
        $model = $this->findUserById($id);

        $data = $model->encryptor()->encryptBulkData($data, $this->userAdinsEncryptedFields, $this->userAdinsEncryptedJsonFields);

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
            ->where('document_id', '=', $documentId)
            ->first();

        return $this->stripEloquentModel($model);
    }

    public function createDocument(array $data)
    {
        $data = SodiumEncryption::encryptor()->encryptBulkData($data, $this->esignDocumentEncryptedFields, $this->esignDocumentEncryptedJsonFields);

        $model = $this->eSignDocumentModel
            ->newQuery()
            ->forceCreate($data);

        return $this->stripEloquentModel($model->fresh());
    }

    public function updateDocument(int $id, array $data)
    {
        $model = $this->findDocumentById($id);

        $data = $model->encryptor()->encryptBulkData($data, $this->esignDocumentEncryptedFields, $this->esignDocumentEncryptedJsonFields);

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
            ->where('document_id', '=', $documentId)
            ->first();

        return $this->stripEloquentModel($model->fresh());
    }

    public function createDocumentAssignee(array $data)
    {
        $data = SodiumEncryption::encryptor()->encryptBulkData($data, $this->esignDocumentAssigneeEncryptedFields);

        $model = $this->eSignDocumentAssigneeModel
            ->newQuery()
            ->forceCreate($data);

        return $this->stripEloquentModel($model->fresh());
    }

    public function updateDocumentAssignee(int $id, array $data)
    {
        $model = $this->findDocumentAssigneeById($id);

        $data = $model->encryptor()->encryptBulkData($data, $this->esignDocumentAssigneeEncryptedFields);

        $model->update($data);

        return $this->stripEloquentModel($model->fresh());
    }

    public function findOTPRequestById(int $id)
    {
        return $this->eSignOTPModel->newQuery()->find($id);
    }

    public function findOTPRequestBySanfIdAndRefNoWhereCodeIsNull(string $sanfId, string $referenceNo)
    {
        $sodiumQuery = SodiumEncryption::query();

        $model = $this->eSignOTPModel
            ->newQuery()
            ->where('sanf_id', '=', $sanfId)
            ->where($sodiumQuery->selectRaw('reference_no'), '=', $referenceNo)
            ->whereNull('code')
            ->first();

        return $this->stripEloquentModel($model);
    }

    public function findOTPRequestBySanfIdWhereCodeIsNull(string $sanfId)
    {
        $model = $this->eSignOTPModel
            ->newQuery()
            ->where('sanf_id', '=', $sanfId)
            ->whereNull('code')
            ->orderByDesc('created_at')
            ->first();

        return $this->stripEloquentModel($model);
    }

    public function createOTPRequest(array $data)
    {
        $data = SodiumEncryption::encryptor()->encryptBulkData($data, $this->eSignOTPEncryptedFields);

        $model = $this->eSignOTPModel->newQuery()->forceCreate($data);

        return $this->stripEloquentModel($model->fresh());
    }

    public function updateOTPRequest(int $id, array $data)
    {
        $model = $this->findOTPRequestById($id);

        $data = $model->encryptor()->encryptBulkData($data, $this->eSignOTPEncryptedFields);

        $model->update($data);

        return $this->stripEloquentModel($model->fresh());
    }
}
