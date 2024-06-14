<?php

namespace Sanf\Core\Modules\Plafond\Repositories;

use Sanf\Core\Modules\Plafond\Models\PaymentAccelarationDocumentModel;

interface PaymentAccelarationDocumentRepositoryInterface
{
    /**
     * @param array $request
     * @return PaymentAccelarationDocumentModel
     */
    public function create(array $request): PaymentAccelarationDocumentModel;

    /**
     * @param string $id
     * @param array $request
     * @return PaymentAccelarationDocumentModel
     */
    public function update(string $id, array $request): PaymentAccelarationDocumentModel;

    /**
     * @param string $id
     * @return PaymentAccelarationDocumentModel|null
     */
    public function findByPlafondId(string $plafondId): ?PaymentAccelarationDocumentModel;
}
