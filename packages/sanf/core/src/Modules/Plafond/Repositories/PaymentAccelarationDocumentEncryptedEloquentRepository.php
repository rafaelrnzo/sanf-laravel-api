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
     * @param string $plafondId
     * @return PaymentAccelarationDocumentEncryptedModel|null
     */
    public function findByPlafondId(string $plafondId): ?PaymentAccelarationDocumentEncryptedModel
    {
        return $this->model->query()->where('plafond_id', '=', $plafondId)->first();
    }

    /**
     * @param string $clientId
     * @param string $plafondId
     * @return PaymentAccelarationDocumentEncryptedModel|null
     */
    public function findByClientIdAndPlafondId(string $clientId, string $plafondId): ?PaymentAccelarationDocumentEncryptedModel
    {
        return $this->model->query()
            ->where('client_id', '=', $clientId)
            ->where('plafond_id', '=', $plafondId)
            ->first();
    }

    /**
     * Get company information for disbursement email.
     * @param string $clientId
     * @param string $plafondId
     * @return array|null
     */
    public function getCompanyInfoForEmail(string $clientId, string $plafondId): ?array
    {
        $document = $this->findByClientIdAndPlafondId($clientId, $plafondId);

        if (!$document) {
            return null;
        }

        return [
            'company_name' => $document->company,
            'bowheer_name' => $document->bowheer,
            'bowheer_address' => $document->bowheer_address,
            'first_signer' => [
                'company' => $document->first_signer_company,
                'name' => $document->first_signer_name,
                'position' => $document->first_signer_position,
            ],
            'second_signer' => [
                'company' => $document->second_signer_company,
                'name' => $document->second_signer_name,
                'position' => $document->second_signer_position,
            ],
        ];
    }
}
