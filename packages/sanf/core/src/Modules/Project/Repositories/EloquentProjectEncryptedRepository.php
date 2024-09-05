<?php

namespace Sanf\Core\Modules\Project\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\Project\Models\ProjectEncryptedModel;

class EloquentProjectEncryptedRepository extends AbstractEloquentRepository implements ProjectRepositoryInterface
{
    protected $model;
    protected $encryptedFields;
    protected $encryptedJsonFields;

    public function __construct(ProjectEncryptedModel $model)
    {
        $this->model = $model;
        $this->encryptedFields = [
            'phone_number',
            'whatsapp_number',
            'business_email',
            'city_name',
            'province_name',
        ];
        $this->encryptedJsonFields = [
            'location_metadata',
            'modified_by',
        ];
    }

    public function findById($id)
    {
        $model = $this->model->newQuery()->with(['user', 'status'])->find($id);

        return $this->stripEloquentModel($model);
    }

    public function findByXid($xid)
    {
        $model = $this->model->newQuery()->where('xid', $xid)->with(['user', 'status'])->first();

        return $this->stripEloquentModel($model);
    }

    public function query($specification)
    {
        $models = $specification->buildQuery($this->model)->get();

        return $this->stripEloquentModel($models);
    }

    public function add($fields)
    {
        $model = $this->model->newQuery()->forceCreate($this->encryptBeforeCreate($fields));

        return $this->stripEloquentModel($model->fresh());
    }

    private function encryptBeforeCreate(array $data): array
    {
        $encryptor = SodiumEncryption::encryptor();

        foreach ($data as $key => $value) {
            if (in_array($key, $this->encryptedFields)) {
                $data[$key] = $encryptor->encrypt($value);
                continue;
            }

            if (in_array($key, $this->encryptedJsonFields)) {
                $data[$key] = $encryptor->encryptForJson($value);
                continue;
            }
        }

        $data['nonce'] = $encryptor->nonce()->getNonceHex();

        return $data;
    }

    public function update($fields, $specification = null)
    {
        $fields = $this->encryptBeforeUpdate($fields, $fields['id']);

        if (!is_null($specification)) {
            $model = $specification->buildQuery($this->model)->update($fields);

            return $this->stripEloquentModel($model);
        }

        $model = $this->model->newQuery()->where('id', $fields['id'])->update($fields);

        return $this->stripEloquentModel($model);
    }

    private function encryptBeforeUpdate(array $data, $id): array
    {
        $model = $this->model->newQuery()->find($id);

        $encryptor = $model->encryptor();

        foreach ($data as $key => $value) {
            if (in_array($key, $this->encryptedFields)) {
                $data[$key] = $encryptor->encrypt($value);
                continue;
            }

            if (in_array($key, $this->encryptedJsonFields)) {
                $data[$key] = $encryptor->encryptForJson($value);
                continue;
            }
        }

        return $data;
    }

    public function remove($fields, $specification = null)
    {
        if (!is_null($specification)) {
            return $specification->buildQuery($this->model)->delete();
        }

        return $this->model->newQuery()->where('id', $fields['id'])->delete();
    }

    public function removeById($id)
    {
        return $this->model->newQuery()->where('id', $id)->delete();
    }

    public function removeByXid($xid)
    {
        return $this->model->newQuery()->where('xid', $xid)->delete();
    }

    public function size($specification = null)
    {
        if (!is_null($specification)) {
            return $specification->buildQuery($this->model)->count();
        }

        return $this->model->newQuery()->select('id')->count();
    }
}
