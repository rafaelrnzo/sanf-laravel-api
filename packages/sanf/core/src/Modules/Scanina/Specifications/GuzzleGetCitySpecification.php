<?php

namespace Sanf\Core\Modules\Scanina\Specifications;

use Sanf\Core\Modules\Scanina\Dtos\BrowseProductBuyRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\ScaninaProductBuyFilterDto;
use Sanf\Integration\Modules\Scanina\ScaninaApiClient;

class GuzzleGetCitySpecification
{
    private object $parameter;

    /**
     * @param object $parameter
     */
    public function __construct(object $parameter)
    {
        $this->parameter = $parameter;
    }

    public function send(ScaninaApiClient $client)
    {
        return $client->getCity((object) $this->parameter);
    }
}
