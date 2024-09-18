<?php

namespace Sanf\Core\Modules\Plafond\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Encryptions\SodiumEncryption;
use Sanf\Core\Modules\Plafond\Exceptions\PlafondDisbursementNotFoundException;
use Sanf\Core\Modules\Plafond\Exceptions\PlafondDisbursementSubmissionNotFoundException;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementAllocationEncryptedModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementDocumentModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementEncryptedModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementInvoiceModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementInvoicePhotoModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementSubmissionEncryptedModel;
use Sanf\Core\Modules\Plafond\Queries\BrowsePlafondDisbursementEncryptedEloquentBuilder;

class PlafondDisbursementEncryptedEloquentRepository extends AbstractEloquentRepository implements PlafondDisbursementRepositoryInterface
{
    protected PlafondDisbursementEncryptedModel $disbursementModel;
    protected PlafondDisbursementSubmissionEncryptedModel $submissionModel;
    protected PlafondDisbursementInvoiceModel $invoiceModel;
    protected PlafondDisbursementInvoicePhotoModel $invoicePhotoModel;
    protected PlafondDisbursementAllocationEncryptedModel $allocationModel;
    protected PlafondDisbursementDocumentModel $documentModel;
    protected array $encryptedFieldsDisbursement;
    protected array $encryptedFieldsSubmission;
    protected array $encryptedFieldsAllocation;

    public function __construct(
        PlafondDisbursementEncryptedModel $disbursementModel,
        PlafondDisbursementSubmissionEncryptedModel $submissionModel,
        PlafondDisbursementInvoiceModel $invoiceModel,
        PlafondDisbursementInvoicePhotoModel $invoicePhotoModel,
        PlafondDisbursementAllocationEncryptedModel $allocationModel,
        PlafondDisbursementDocumentModel $documentModel
    ) {
        $this->disbursementModel = $disbursementModel;
        $this->submissionModel = $submissionModel;
        $this->invoiceModel = $invoiceModel;
        $this->invoicePhotoModel = $invoicePhotoModel;
        $this->allocationModel = $allocationModel;
        $this->documentModel = $documentModel;

        $this->encryptedFieldsDisbursement = [
            'client_name',
            'client_mail',
            'customer_name',
            'customer_mail',
        ];
        $this->encryptedFieldsSubmission = [
            'allocation_snapshot',
            'user_updated_by',
        ];
        $this->encryptedFieldsAllocation = [
            'owner',
            'provider',
            'account_no',
            'notes',
        ];
    }

    /**
     * @param BrowsePlafondDisbursementEncryptedEloquentBuilder $builder
     * @return object
     */
    public function query($builder)
    {
        $disbursementCollection = $builder->build($this->disbursementModel)->get();

        return $this->stripEloquentModel($disbursementCollection);
    }

    /**
     * @param BrowsePlafondDisbursementEncryptedEloquentBuilder $builder
     * @return int
     */
    public function count($builder): int
    {
        return $builder->build($this->disbursementModel)->count();
    }

    /**
     * @param array $request
     * @return PlafondDisbursementEncryptedModel
     */
    public function createDisbursement(array $request): PlafondDisbursementEncryptedModel
    {
        $request = SodiumEncryption::encryptor()->encryptMultipleData($request, $this->encryptedFieldsDisbursement);

        $model = $this->disbursementModel->query()->create($request);

        return $model->fresh();
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

        $request = $disbursementRecord->encryptor()->encryptMultipleData($request, $this->encryptedFieldsDisbursement);

        $disbursementRecord->update($request);

        return $disbursementRecord->fresh();
    }

    /**
     * @param array $request
     * @return PlafondDisbursementSubmissionEncryptedModel
     */
    public function createSubmission(array $request): PlafondDisbursementSubmissionEncryptedModel
    {
        $request = SodiumEncryption::encryptor()->encryptMultipleData($request, $this->encryptedFieldsSubmission);

        $model = $this->submissionModel->query()->create($request);

        return $model->fresh();
    }

    /**
     * @param array $request
     * @return PlafondDisbursementSubmissionEncryptedModel
     */
    public function updateSubmission(int $id, array $request): PlafondDisbursementSubmissionEncryptedModel
    {
        $submissionRecord = $this->submissionModel->query()->find($id);
        if (is_null($submissionRecord)) {
            throw new PlafondDisbursementSubmissionNotFoundException();
        }

        $request = $submissionRecord->encryptor()->encryptMultipleData($request, $this->encryptedFieldsSubmission);

        $submissionRecord->update($request);

        return $submissionRecord->fresh();
    }

    /**
     * @param array $request
     * @return PlafondDisbursementInvoiceModel
     */
    public function createInvoice(array $request): PlafondDisbursementInvoiceModel
    {
        return $this->invoiceModel->query()->create($request);
    }

    /**
     * @param array $request
     * @return PlafondDisbursementInvoicePhotoModel
     */
    public function createInvoicePhoto(array $request): PlafondDisbursementInvoicePhotoModel
    {
        return $this->invoicePhotoModel->query()->create($request);
    }

    /**
     * @param array $request
     * @return PlafondDisbursementAllocationEncryptedModel
     */
    public function createAllocation(array $request): PlafondDisbursementAllocationEncryptedModel
    {
        $request = SodiumEncryption::encryptor()->encryptMultipleData($request, $this->encryptedFieldsAllocation);

        $model = $this->allocationModel->query()->create($request);

        return $model->fresh();
    }

    /**
     * @param array $request
     * @return PlafondDisbursementDocumentModel
     */
    public function createDocument(array $request): PlafondDisbursementDocumentModel
    {
        return $this->documentModel->query()->create($request);
    }

    /**
     * @return int
     */
    public function countInMonth($dateTime): int
    {
        return $this->disbursementModel->query()->whereMonth('created_at', $dateTime)->count();
    }
}
