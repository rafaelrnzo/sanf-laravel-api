<?php

namespace Sanf\Integration\Modules\SanfCore\Payloads;

use Spatie\DataTransferObject\DataTransferObject;

class SanfCoreSubmitSparePartFinancingDocumentPayload extends DataTransferObject
{
    public string $DOC_ID;
    public string $FILE_PATH;
    public string $FILE_NAME;
}
