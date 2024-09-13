<?php

namespace Sanf\Core\Modules\Plafond\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\Plafond\Models\PaymentAccelarationDocumentEncryptedModel;

class PaymentAccelarationDocumentEncryptedEloquentRepository extends AbstractEloquentRepository implements PaymentAccelarationDocumentRepositoryInterface
{
    private PaymentAccelarationDocumentEncryptedModel $model;
    private array $encryptedFields;

    public function __construct(
        PaymentAccelarationDocumentEncryptedModel $model
    ) {
        $this->model = $model;
        $this->encryptedFields = [
            'company',
            'bowheer',
            'bowheer_email',
            'first_signer_company',
            'first_signer_name',
            'first_signer_position',
            'second_signer_company',
            'second_signer_name',
            'second_signer_position',
            'bowheer_address',
        ];
    }

    /**
     * @param array $request
     * @return PaymentAccelarationDocumentEncryptedModel
     */
    public function create(array $request): PaymentAccelarationDocumentEncryptedModel
    {
        $model = $this->model->query()->create(
            SodiumEncryption::encryptor()->encryptMultipleData($request, $this->encryptedFields)
        );

        return $model->fresh();
    }

    /**
     * @param string $id
     * @param array $request
     * @return PaymentAccelarationDocumentEncryptedModel
     */
    public function update(string $id, array $request): PaymentAccelarationDocumentEncryptedModel
    {
        $model = $this->model->query()->find($id);

        $model->update(
            $model->encryptor()->encryptMultipleData($request, $this->encryptedFields)
        );

        return $model->fresh();
    }

    /**
     * @param string $id
     * @return PaymentAccelarationDocumentEncryptedModel|null
     */
    public function findByPlafondId(string $plafondId): ?PaymentAccelarationDocumentEncryptedModel
    {
        return $this->model->query()->where('plafond_id', '=', $plafondId)->first();
    }
}
