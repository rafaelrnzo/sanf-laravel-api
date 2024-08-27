<?php

namespace Sanf\Core\Modules\Scanina\Specifications;

use NbsPhp\Core\Exceptions\UndefinedSwitchCaseException;
use Sanf\Core\Modules\Scanina\Dtos\BrowseProductFilterRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\ScaninaProductFilterDto;
use Sanf\Core\Modules\Scanina\Enums\ScaninaProductTypeEnum;
use Sanf\Integration\Modules\Scanina\ScaninaApiClient;

class GuzzleGetFilterCategorySpecification
{
    private BrowseProductFilterRequestDto $parameter;

    /**
     * @param BrowseProductFilterRequestDto $parameter
     */
    public function __construct(BrowseProductFilterRequestDto $parameter)
    {
        $this->parameter = $parameter;
    }

    public function send(ScaninaApiClient $client)
    {
        $queryParam = $this->parameter->toArray();
        switch ($queryParam['type']) {
            case ScaninaProductTypeEnum::SPARE_PART:
                $type = 'spare-part';
                break;
            case ScaninaProductTypeEnum::SERVICE:
                $type = 'service';
                break;
            case ScaninaProductTypeEnum::RENT:
                $type = 'rent';
                break;
            case ScaninaProductTypeEnum::BUY:
                $type = 'buy';
                break;
            default:
                throw new UndefinedSwitchCaseException();
                break;
        }

        $queryParam['type'] = $type;
        unset($queryParam['userId']);

        return $client->getFilterCategory(new ScaninaProductFilterDto($queryParam));
    }
}
