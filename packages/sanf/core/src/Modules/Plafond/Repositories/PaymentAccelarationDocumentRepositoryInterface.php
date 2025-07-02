<?php

namespace Sanf\Core\Modules\Plafond\Repositories;

use Sanf\Core\Modules\Plafond\Models\PaymentAccelarationDocumentEncryptedModel;
use Sanf\Core\Modules\Plafond\Models\PaymentAccelarationDocumentModel;

interface PaymentAccelarationDocumentRepositoryInterface
{
    /**
     * @param array $request
     * @return PaymentAccelarationDocumentModel|PaymentAccelarationDocumentEncryptedModel
     */
    public function create(array $request);

    /**
     * @param string $id
     * @param array $request
     * @return PaymentAccelarationDocumentModel|PaymentAccelarationDocumentEncryptedModel
     */
    public function update(string $id, array $request);

    /**
     * @param string $id
     * @return PaymentAccelarationDocumentModel|PaymentAccelarationDocumentEncryptedModel|null
     */
    public function findByPlafondId(string $plafondId);

    /**
     * @param string $clientId
     * @param string $plafondId
     * @return PaymentAccelarationDocumentModel|PaymentAccelarationDocumentEncryptedModel|null
     */
    public function findByClientIdAndPlafondId(string $clientId, string $plafondId);

    /**
     * @param string $clientId
     * @param string $plafondId
     * @return array|null
     */
    public function getCompanyInfoForEmail(string $clientId, string $plafondId): ?array;
}
