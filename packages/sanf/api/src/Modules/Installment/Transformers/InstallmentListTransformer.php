<?php

namespace Sanf\Api\Modules\Installment\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\Installment\Responses\InstallmentItemResponse;
use Sanf\Integration\Modules\SanfCore\SanfCoreApiClientV2;

final class InstallmentListTransformer extends TransformerAbstract
{
    public function transform(InstallmentItemResponse $installment)
    {
        return [
            'contract_no' => $installment->contractNo,
            'due_date' => Carbon::parse($installment->dueDate, SanfCoreApiClientV2::DEFAULT_TIMEZONE)->timestamp,
            'total_amount' => $installment->totalAmount,
            'financing_type_id' => $installment->financingTypeId,
            'financing_type_desc' => $installment->financingTypeDescription,
            'status' => $installment->status,
            'payment_xid' => $installment->paymentXid,
            'sequence_no' => $installment->sequenceNumber,
            'sequence_total' => $installment->sequenceTotal,
        ];
    }
}
