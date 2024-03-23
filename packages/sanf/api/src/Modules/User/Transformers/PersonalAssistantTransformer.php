<?php

namespace Sanf\Api\Modules\User\Transformers;

use League\Fractal\TransformerAbstract;

class PersonalAssistantTransformer extends TransformerAbstract
{
    public function transform($item)
    {
        return [
            'msisdn' => (string) preg_replace('/^0/', '+62', $item->msisdn),
            'has_contract' => $item->has_contract,
            'initial_message' => (string) $item->message,
        ];
    }
}
