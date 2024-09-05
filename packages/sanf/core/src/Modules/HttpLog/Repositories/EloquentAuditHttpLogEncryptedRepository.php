<?php

namespace Sanf\Core\Modules\HttpLog\Repositories;

use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\HttpLog\Models\AuditHttpLogEncryptedModel;

class EloquentAuditHttpLogEncryptedRepository implements AuditHttpLogRepositoryInterface
{
    protected $model;
    protected $encryptedFields;
    protected $encryptedJsonFields;

    public function __construct(AuditHttpLogEncryptedModel $model)
    {
        $this->model = $model;
        $this->encryptedFields = [
            'ip_address',
        ];
        $this->encryptedJsonFields = [
            'query',
            'body',
            'response',
        ];
    }

    public function create(array $data)
    {
        $model = $this->model->newQuery()
            ->forceCreate($this->encryptBeforeCreate($data));

        return $model->fresh();
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
}
