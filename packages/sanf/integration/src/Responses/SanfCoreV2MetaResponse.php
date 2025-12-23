<?php

namespace Sanf\Integration\Responses;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class SanfCoreV2MetaResponse extends FlexibleDataTransferObject
{
    public int $current_page;
    public int $per_page;
    public int $total;
    public int $last_page;

    // TODO: temporary solved
    public function __construct(array $parameters = [])
    {
        $parameters = [
            'current_page' => (int) $parameters['current_page'],
            'per_page' => (int) $parameters['per_page'],
            'total' => (int) $parameters['total'],
            'last_page' => (int) $parameters['last_page'],
        ];

        parent::__construct($parameters);
    }
}
