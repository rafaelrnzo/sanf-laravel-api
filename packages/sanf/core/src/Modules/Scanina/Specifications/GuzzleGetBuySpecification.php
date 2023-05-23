<?php

namespace Sanf\Core\Modules\Scanina\Specifications;

use Sanf\Core\Modules\Scanina\Dtos\BrowseProductBuyRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\ScaninaProductBuyFilterDto;
use Sanf\Integration\Modules\Scanina\ScaninaApiClient;

class GuzzleGetBuySpecification
{
    private BrowseProductBuyRequestDto $parameter;

    /**
     * @param BrowseProductBuyRequestDto $parameter
     */
    public function __construct(BrowseProductBuyRequestDto $parameter)
    {
        $this->parameter = $parameter;
    }

    public function send(ScaninaApiClient $client)
    {
        $queryParam = $this->parameter->toArray();
        $queryParam['assurance'] = $queryParam['hasAssurance'];
        $queryParam['scanQualified'] = $queryParam['isScanQualified'];

        unset($queryParam['userId']);
        unset($queryParam['hasAssurance']);
        unset($queryParam['isScanQualified']);

        return $client->getBuy(new ScaninaProductBuyFilterDto($queryParam));
    }
}
