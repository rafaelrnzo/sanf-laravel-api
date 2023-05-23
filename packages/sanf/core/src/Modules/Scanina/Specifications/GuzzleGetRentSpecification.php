<?php

namespace Sanf\Core\Modules\Scanina\Specifications;

use Sanf\Core\Modules\Scanina\Dtos\BrowseProductRentRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\ScaninaProductRentFilterDto;
use Sanf\Integration\Modules\Scanina\ScaninaApiClient;

class GuzzleGetRentSpecification
{
    private BrowseProductRentRequestDto $parameter;

    /**
     * @param BrowseProductRentRequestDto $parameter
     */
    public function __construct(BrowseProductRentRequestDto $parameter)
    {
        $this->parameter = $parameter;
    }

    public function send(ScaninaApiClient $client)
    {
        $queryParam = $this->parameter->toArray();
        $queryParam['startRentalDate'] = $queryParam['startDate'];
        $queryParam['endRentalDate'] = $queryParam['endDate'];
        $queryParam['assurance'] = $queryParam['hasAssurance'];
        $queryParam['scanQualified'] = $queryParam['isScanQualified'];

        unset($queryParam['userId']);
        unset($queryParam['startDate']);
        unset($queryParam['endDate']);
        unset($queryParam['hasAssurance']);
        unset($queryParam['isScanQualified']);

        return $client->getRent(new ScaninaProductRentFilterDto($queryParam));
    }
}
