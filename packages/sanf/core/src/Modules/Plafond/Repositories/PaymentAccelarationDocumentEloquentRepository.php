<?php

namespace Sanf\Core\Modules\Plafond\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\Plafond\Models\PaymentAccelarationDocumentModel;

class PaymentAccelarationDocumentEloquentRepository extends AbstractEloquentRepository implements PaymentAccelarationDocumentRepositoryInterface
{
    private PaymentAccelarationDocumentModel $model;

    public function __construct(
        PaymentAccelarationDocumentModel $model
    ) {
        $this->model = $model;
    }

    /**
     * @param array $request
     * @return PaymentAccelarationDocumentModel
     */
    public function create(array $request): PaymentAccelarationDocumentModel
    {
        return $this->model->query()->create($request);
    }

    /**
     * @param string $id
     * @param array $request
     * @return PaymentAccelarationDocumentModel
     */
    public function update(string $id, array $request): PaymentAccelarationDocumentModel
    {
        $model = $this->model->query()->find($id);
        $model->update($request);

        return $model;
    }

    /**
     * @param string $plafondId
     * @return PaymentAccelarationDocumentModel|null
     */
    public function findByPlafondId(string $plafondId): ?PaymentAccelarationDocumentModel
    {
        return $this->model->query()->where('plafond_id', '=', $plafondId)->first();
    }

    /**
     * @param string $clientId
     * @param string $plafondId
     * @return PaymentAccelarationDocumentModel|null
     */
    public function findByClientIdAndPlafondId(string $clientId, string $plafondId): ?PaymentAccelarationDocumentModel
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
