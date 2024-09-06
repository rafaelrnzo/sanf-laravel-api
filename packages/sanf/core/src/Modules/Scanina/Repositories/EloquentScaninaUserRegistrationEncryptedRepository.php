<?php

namespace Sanf\Core\Modules\Scanina\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\Scanina\Models\ScaninaUserRegistrationEncryptedModel;

class EloquentScaninaUserRegistrationEncryptedRepository extends AbstractEloquentRepository implements ScaninaUserRegistrationRepositoryInterface
{
    private ScaninaUserRegistrationEncryptedModel $model;
    private array $encryptedJsonFields;
    private array $encryptedFields;

    public function __construct(ScaninaUserRegistrationEncryptedModel $model)
    {
        $this->model = $model;
        $this->encryptedFields = [
            'email',
        ];
        $this->encryptedJsonFields = [
            'snapshot_request_body',
            'snapshot_response_body',
        ];
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
            if (in_array($key, $this->encryptedFields)) {
                $data[$key] = $encryptor->encrypt($value);
                continue;
            }

            if (in_array($key, $this->encryptedJsonFields)) {
                $data[$key] = $encryptor->encryptForJson($value);
            }
        }

        $data['nonce'] = $encryptor->nonce()->getNonceHex();

        return $data;
    }
}
