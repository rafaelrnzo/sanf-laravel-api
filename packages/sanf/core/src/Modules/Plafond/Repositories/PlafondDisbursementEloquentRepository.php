<?php

namespace Sanf\Core\Modules\Plafond\Repositories;

use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementAllocationModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementDocumentModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementInvoiceModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementInvoicePhotoModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementSubmissionModel;

class PlafondDisbursementEloquentRepository implements PlafondDisbursementRepositoryInterface
{
    private PlafondDisbursementModel $disbursementModel;
    private PlafondDisbursementSubmissionModel $submissionModel;
    private PlafondDisbursementInvoiceModel $invoiceModel;
    private PlafondDisbursementInvoicePhotoModel $invoicePhotoModel;
    private PlafondDisbursementAllocationModel $allocationModel;
    private PlafondDisbursementDocumentModel $documentModel;

    public function __construct(
        PlafondDisbursementModel $disbursementModel,
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
    }

    /**
     * @param array $request
     * @return PlafondDisbursementModel
     */
    public function createDisbursement(array $request): PlafondDisbursementModel
    {
        return $this->disbursementModel->query()->create($request);
    }

    /**
     * @param array $request
     * @return PlafondDisbursementSubmissionModel
     */
    public function createSubmission(array $request): PlafondDisbursementSubmissionModel
    {
        return $this->submissionModel->query()->create($request);
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
     * @return PlafondDisbursementAllocationModel
     */
    public function createAllocation(array $request): PlafondDisbursementAllocationModel
    {
        return $this->allocationModel->query()->create($request);
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
