<?php

namespace Sanf\Core\Modules\Scanina\Specifications;

use Sanf\Core\Modules\Scanina\Dtos\BrowseProductBuyRequestDto;
use Sanf\Core\Modules\Scanina\Dtos\ScaninaProductBuyFilterDto;
use Sanf\Integration\Modules\Scanina\ScaninaApiClient;

class GuzzleGetCartSpecification
{
    private string $email;

    /**
     * @param string $email
     * @param string $module
     */
    public function __construct(string $email, string $module)
    {
        $this->email = $email;
        $this->module = $module;
    }

    public function send(ScaninaApiClient $client)
    {
        return $client->getCart($this->email, $this->module);
    }
}
