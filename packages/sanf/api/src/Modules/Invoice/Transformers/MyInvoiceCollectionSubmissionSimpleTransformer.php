<?php

namespace Sanf\Api\Modules\Invoice\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

final class MyInvoiceCollectionSubmissionSimpleTransformer extends TransformerAbstract
{
    public function transform($dto)
    {
        return [
            'xid' => $dto->xid,
            'contract_no' => $dto->contractNo,
            'serial_no' => $dto->serialNo,
            'pickup_date' => Carbon::createFromImmutable($dto->pickupDate)->format('Y-m-d'),
            'brand_type_model' => $dto->brandTypeModel,
            'year' => $dto->year,
            'status' => fractal($dto->status, new InvoiceCollectionSubmissionStatusTransformer()),
            'created_at' => unix_timestamp($dto->createdAt),
            'updated_at' => unix_timestamp($dto->updatedAt),
        ];
    }
}
