<?php

namespace Sanf\Core\Modules\Scanina\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\Scanina\Models\ScaninaProductCartEncryptedModel;

class EloquentProductCartEncryptedRepository extends AbstractEloquentRepository implements ProductCartRepositoryInterface
{
    private ScaninaProductCartEncryptedModel $model;
    private array $encryptedJsonFields;

    public function __construct(ScaninaProductCartEncryptedModel $model)
    {
        $this->model = $model;
        $this->encryptedJsonFields = [
            'snapshot_request_body',
            'snapshot_response_body',
        ];
    }

    public function query($specification): array
    {
        $records = $specification->buildQuery($this->model)->get();

        return $this->stripEloquentModel($records);
    }

    public function size($specification): int
    {
        return $specification->buildQuery($this->model)->count();
    }

    public function create(array $request)
    {
        $model = $this->model->newQuery()->forceCreate($this->encryptBeforeCreate($request));

        return $model->fresh();
    }

    private function encryptBeforeCreate(array $data): array
    {
        $encryptor = SodiumEncryption::encryptor();

        foreach ($data as $key => $value) {
            if (in_array($key, $this->encryptedJsonFields)) {
                $data[$key] = $encryptor->encryptForJson($value);
            }
        }

        $data['nonce'] = $encryptor->nonce()->getNonceHex();

        return $data;
    }

    public function findByXid(string $xid)
    {
        return $this->model->newQuery()->where('xid', '=', $xid)->first();
    }
}
