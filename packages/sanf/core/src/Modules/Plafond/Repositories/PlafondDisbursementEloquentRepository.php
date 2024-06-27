<?php

namespace Sanf\Core\Modules\Plafond\Repositories;

use NbsPhp\Core\Repositories\AbstractEloquentRepository;
use Sanf\Core\Modules\Plafond\Exceptions\PlafondDisbursementNotFoundException;
use Sanf\Core\Modules\Plafond\Exceptions\PlafondDisbursementSubmissionNotFoundException;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementAllocationModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementDocumentModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementInvoiceModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementInvoicePhotoModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementSubmissionModel;
use Sanf\Core\Modules\Plafond\Queries\BrowsePlafondDisbursementEloquentBuilder;

class PlafondDisbursementEloquentRepository extends AbstractEloquentRepository implements PlafondDisbursementRepositoryInterface
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

    public function query($builder)
    {
        /** @var BrowsePlafondDisbursementEloquentBuilder $builder */
        $disbursementCollection = $builder->build($this->disbursementModel)->get();

        return $this->stripEloquentModel($disbursementCollection);
    }

    public function count($builder): int
    {
        /* @var BrowsePlafondDisbursementEloquentBuilder $builder */
        return $builder->build($this->disbursementModel)->count();
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
     * @return PlafondDisbursementModel
     */
    public function updateDisbursement(int $id, array $request): PlafondDisbursementModel
    {
        $disbursementRecord = $this->disbursementModel->query()->find($id);
        if (is_null($disbursementRecord)) {
            throw new PlafondDisbursementNotFoundException();
        }

        $disbursementRecord->update($request);

        return $disbursementRecord;
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
     * @return PlafondDisbursementSubmissionModel
     */
    public function updateSubmission(int $id, array $request): PlafondDisbursementSubmissionModel
    {
        $submissionRecord = $this->submissionModel->query()->find($id);
        if (is_null($submissionRecord)) {
            throw new PlafondDisbursementSubmissionNotFoundException();
        }

        $submissionRecord->update($request);

        return $submissionRecord;
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
