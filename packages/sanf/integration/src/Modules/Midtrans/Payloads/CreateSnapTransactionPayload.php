<?php

namespace Sanf\Integration\Modules\Midtrans\Payloads;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class CreateSnapTransactionPayload extends FlexibleDataTransferObject
{
    public SnapTransactionDetailsPayload $transaction_details;
    /**
     * @var array|SnapItemDetailPayload[]|null
     */
    public ?array $item_details;
    public ?SnapCustomerDetailsPayload $customer_details;
    public ?array $enabled_payments;
    public ?SnapCallbacksPayload $callbacks;
    public ?SnapExpiryPayload $expiry;
}
