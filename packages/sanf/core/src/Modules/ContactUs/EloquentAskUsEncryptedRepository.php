<?php

namespace Sanf\Core\Modules\ContactUs;

use Sanf\Core\Encryptions\SodiumEncryption;

class EloquentAskUsEncryptedRepository implements AskUsRepositoryInterface
{
    /** @var AskUsEncryptedModel */
    protected $model;
    protected $encryptedFields;
    protected $encryptedJsonFields;

    public function __construct(AskUsEncryptedModel $model)
    {
        $this->model = $model;
        $this->encryptedFields = [
            'name',
            'phone_number',
            'email',
        ];
        $this->encryptedJsonFields = [
            'modified_by',
        ];
    }

    public function save($data)
    {
        return $this->model
            ->newQuery()
            ->forceCreate($this->encryptBeforeCreate($data));
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
