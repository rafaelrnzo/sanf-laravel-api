<?php

namespace Sanf\Core\Modules\Plafond\Repositories;

use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementAllocationModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementDocumentModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementInvoiceModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementInvoicePhotoModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementSubmissionModel;

interface PlafondDisbursementRepositoryInterface
{
    /**
     * @param array $request
     * @return PlafondDisbursementModel
     */
    public function createDisbursement(array $request): PlafondDisbursementModel;

    /**
     * @param array $request
     * @return PlafondDisbursementSubmissionModel
     */
    public function createSubmission(array $request): PlafondDisbursementSubmissionModel;

    /**
     * @param array $request
     * @return PlafondDisbursementAllocationModel
     */
    public function createAllocation(array $request): PlafondDisbursementAllocationModel;

    /**
     * @param array $request
     * @return PlafondDisbursementInvoiceModel
     */
    public function createInvoice(array $request): PlafondDisbursementInvoiceModel;

    /**
     * @param array $request
     * @return PlafondDisbursementInvoicePhotoModel
     */
    public function createInvoicePhoto(array $request): PlafondDisbursementInvoicePhotoModel;

    /**
     * @param array $request
     * @return PlafondDisbursementDocumentModel
     */
    public function createDocument(array $request): PlafondDisbursementDocumentModel;

    /**
     * @return int
     */
    public function countInMonth($dateTime): int;
}
