<?php

namespace Sanf\Core\Modules\Plafond\Repositories;

use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementAllocationEncryptedModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementAllocationModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementDocumentModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementInvoiceModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementInvoicePhotoModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementSubmissionEncryptedModel;
use Sanf\Core\Modules\Plafond\Models\PlafondDisbursementSubmissionModel;

interface PlafondDisbursementRepositoryInterface
{
    public function query($builder);

    public function count($builder): int;

    /**
     * @param array $request
     * @return PlafondDisbursementModel
     */
    public function createDisbursement(array $request): PlafondDisbursementModel;

    /**
     * @param array $request
     * @return PlafondDisbursementModel
     */
    public function updateDisbursement(int $id, array $request): PlafondDisbursementModel;

    /**
     * @param array $request
     * @return PlafondDisbursementSubmissionModel|PlafondDisbursementSubmissionEncryptedModel
     */
    public function createSubmission(array $request);

    /**
     * @param array $request
     * @return PlafondDisbursementSubmissionModel|PlafondDisbursementSubmissionEncryptedModel
     */
    public function updateSubmission(int $id, array $request);

    /**
     * @param array $request
     * @return PlafondDisbursementAllocationModel|PlafondDisbursementAllocationEncryptedModel
     */
    public function createAllocation(array $request);

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
