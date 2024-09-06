<?php

namespace Sanf\Core\Modules\Plafond\Repositories;

use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\Plafond\Exceptions\PlafondDisbursementNotFoundException;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementAllocationModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementDocumentModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementEncryptedModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementInvoiceModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementInvoicePhotoModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementSubmissionModel;
use Sanf\Core\Modules\Plafond\Queries\BrowsePlafondDisbursementEncryptedEloquentBuilder;

class PlafondDisbursementEncryptedEloquentRepository extends PlafondDisbursementEloquentRepository
{
    protected array $encryptedFields;

    public function __construct(
        PlafondDisbursementEncryptedModel $disbursementModel,
        PlafondDisbursementSubmissionModel $submissionModel,
        PlafondDisbursementInvoiceModel $invoiceModel,
        PlafondDisbursementInvoicePhotoModel $invoicePhotoModel,
        PlafondDisbursementAllocationModel $allocationModel,
        PlafondDisbursementDocumentModel $documentModel
    ) {
        $this->disbursementModel = $disbursementModel;
        $this->submissionModel = $submissionModel;
        $this->invoiceModel = $invoiceModel;
        $this->invoicePhotoModel = $invoicePhotoModel;
        $this->allocationModel = $allocationModel;
        $this->documentModel = $documentModel;
        $this->encryptedFields = [
            'client_name',
            'client_mail',
            'customer_name',
            'customer_mail',
        ];
    }

    public function query($builder)
    {
        /** @var BrowsePlafondDisbursementEncryptedEloquentBuilder $builder */
        $disbursementCollection = $builder->build($this->disbursementModel)->get();

        return $this->stripEloquentModel($disbursementCollection);
    }

    public function count($builder): int
    {
        /** @var BrowsePlafondDisbursementEncryptedEloquentBuilder $builder */
        return $builder->build($this->disbursementModel)->count();
    }

    /**
     * @param array $request
     * @return PlafondDisbursementEncryptedModel
     */
    public function createDisbursement(array $request): PlafondDisbursementEncryptedModel
    {
        $model = $this->disbursementModel->query()->create($this->encryptBeforeCreate($request));

        return $model->fresh();
    }

    private function encryptBeforeCreate(array $data): array
    {
        $encryptor = SodiumEncryption::encryptor();

        foreach ($data as $key => $value) {
            if (in_array($key, $this->encryptedFields)) {
                $data[$key] = $encryptor->encrypt($value);
            }
        }

        $data['nonce'] = $encryptor->nonce()->getNonceHex();

        return $data;
    }

    /**
     * @param array $request
     * @return PlafondDisbursementEncryptedModel
     */
    public function updateDisbursement(int $id, array $request): PlafondDisbursementEncryptedModel
    {
        $disbursementRecord = $this->disbursementModel->query()->find($id);
        if (is_null($disbursementRecord)) {
            throw new PlafondDisbursementNotFoundException();
        }

        $disbursementRecord->update($this->encryptBeforeUpdate($request, $disbursementRecord));

        return $disbursementRecord->fresh();
    }

    private function encryptBeforeUpdate(array $data, $model): array
    {
        $encryptor = $model->encryptor();

        foreach ($data as $key => $value) {
            if (in_array($key, $this->encryptedFields)) {
                $data[$key] = $encryptor->encrypt($value);
            }
        }

        return $data;
    }

    /**
     * @return int
     */
    public function countInMonth($dateTime): int
    {
        return $this->disbursementModel->query()->whereMonth('created_at', $dateTime)->count();
    }
}
