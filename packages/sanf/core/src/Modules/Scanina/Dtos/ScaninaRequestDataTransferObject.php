<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

use Illuminate\Support\Str;
use Spatie\DataTransferObject\FlexibleDataTransferObject;

class ScaninaRequestDataTransferObject extends FlexibleDataTransferObject
{
    public function __construct(array $parameters = [])
    {
        $validators = $this->getFieldValidators();
        $camelCasedParameters = [];
        foreach ($parameters as $key => $value) {
            $camelCaseKey = Str::camel($key);
            if (!isset($validators[$camelCaseKey])) {
                continue;
            }

            $camelCasedParameters[$camelCaseKey] = $value;

            $field = $validators[$camelCaseKey];
            $typeData = $field->allowedTypes[0] ?? null;
            if ($typeData) {
                $castValue = $value;
                settype($castValue, $typeData);
                $camelCasedParameters[$camelCaseKey] = $castValue;
            }
        }
        parent::__construct($camelCasedParameters);
    }
}
