<?php

namespace Sanf\Api\Modules\PdcHold\Transformers;

use League\Fractal\TransformerAbstract;
use Sanf\Core\Modules\PdcHold\Enums\PdcHoldTypeEnum;

final class PdcHoldSubmissionTypeTransformer extends TransformerAbstract
{
    /**
     * @param PdcHoldTypeEnum $dto
     */
    public function transform($dto)
    {
        return [
            'id' => $dto->getValue(),
            'name' => $dto->getLabel(),
        ];
    }
}
