<?php

namespace NbsPhp\Core\Controllers;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

abstract class AbstractController extends BaseController
{
    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validatedInput = parent::validate($request, $rules, $messages, $customAttributes);

        return $this->castValidatedInput($validatedInput, $rules);
    }

    protected function castValidatedInput(array $input, array $rules)
    {
        return collect($input)->map(function ($value, $key) use ($rules) {
            if(!isset($rules[$key])){
                return $value;
            }
            if (!is_string($rules[$key]) && !is_array($rules[$key])) {
                return $value;
            }
            $inputRules = is_string($rules[$key]) ? explode('|', $rules[$key]) : $rules[$key];
            if (in_array('integer', $inputRules, true)) {
                return (int) $value;
            }

            return $value;
        })->toArray();
    }
}
