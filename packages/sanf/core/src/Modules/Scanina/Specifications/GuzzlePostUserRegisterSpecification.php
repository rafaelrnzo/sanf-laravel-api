<?php

namespace Sanf\Core\Modules\Scanina\Specifications;

use Sanf\Core\Modules\Scanina\Dtos\ScaninaUserRegisterRequestDto;
use Sanf\Integration\Modules\Scanina\ScaninaApiClient;

class GuzzlePostUserRegisterSpecification
{
    private ScaninaUserRegisterRequestDto $parameter;

    /**
     * @param ScaninaUserRegisterRequestDto $parameter
     */
    public function __construct(ScaninaUserRegisterRequestDto $parameter)
    {
        $this->parameter = $parameter;
    }

    public function send(ScaninaApiClient $client)
    {
        return $client->register($this->parameter);
    }
}
