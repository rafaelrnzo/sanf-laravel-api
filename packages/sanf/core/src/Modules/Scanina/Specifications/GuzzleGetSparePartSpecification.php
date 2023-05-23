<?php

namespace Sanf\Core\Modules\Scanina\Specifications;

use Sanf\Core\Modules\Scanina\Dtos\BrowseProductSparePartRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\ScaninaProductSparePartFilterDto;
use Sanf\Integration\Modules\Scanina\ScaninaApiClient;

class GuzzleGetSparePartSpecification
{
    private BrowseProductSparePartRequestDto $parameter;

    /**
     * @param BrowseProductSparePartRequestDto $parameter
     */
    public function __construct(BrowseProductSparePartRequestDto $parameter)
    {
        $this->parameter = $parameter;
    }

    public function send(ScaninaApiClient $client)
    {
        $queryParam = $this->parameter->toArray();

        unset($queryParam['userId']);

        return $client->getSparePart(new ScaninaProductSparePartFilterDto($queryParam));
    }
}
