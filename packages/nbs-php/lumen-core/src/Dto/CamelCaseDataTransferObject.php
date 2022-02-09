<?php


namespace NbsPhp\Core\Dto;

use Illuminate\Support\Str;
use Spatie\DataTransferObject\FlexibleDataTransferObject;

class CamelCaseDataTransferObject extends FlexibleDataTransferObject
{
    public function __construct(array $parameters = [])
    {
        $camelCasedParameters = [];
        foreach ($parameters as $key => $value) {
            $camelCasedParameters[Str::camel($key)] = $value;
        }
        parent::__construct($camelCasedParameters);
    }
}
