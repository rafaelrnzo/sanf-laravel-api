<?php

namespace Sanf\Core\Modules\Scanina\Specifications;

use Sanf\Core\Modules\Scanina\Dtos\BrowseProductServiceRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\ScaninaProductServiceFilterDto;
use Sanf\Integration\Modules\Scanina\ScaninaApiClient;

class GuzzleGetServiceSpecification
{
    private BrowseProductServiceRequestDto $parameter;

    /**
     * @param BrowseProductServiceRequestDto $parameter
     */
    public function __construct(BrowseProductServiceRequestDto $parameter)
    {
        $this->parameter = $parameter;
    }

    public function send(ScaninaApiClient $client)
    {
        $queryParam = $this->parameter->toArray();

        unset($queryParam['userId']);

        return $client->getService(new ScaninaProductServiceFilterDto($queryParam));
    }
}
