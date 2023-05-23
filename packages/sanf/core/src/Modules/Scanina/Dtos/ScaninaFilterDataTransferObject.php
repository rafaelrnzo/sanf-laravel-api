<?php

namespace Sanf\Core\Modules\Scanina\Dtos;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class ScaninaFilterDataTransferObject extends FlexibleDataTransferObject
{

    public function __construct(array $parameters = [])
    {
        $validators = $this->getFieldValidators();
        $item = [];
        foreach ($parameters as $key => $value) {
            $item[$key] = $value;

            $field = $validators[$key];
            $typeData = $field->allowedTypes[0] ?? null;
            if ($typeData) {
                $castValue = $value;
                settype($castValue, $typeData);
                $item[$key] = $castValue;
            }
        }
        parent::__construct($item);
    }
}
