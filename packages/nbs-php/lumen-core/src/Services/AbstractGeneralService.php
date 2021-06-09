<?php

namespace NbsPhp\Core\Services;

abstract class AbstractGeneralService
{
    protected function sendAsObject($data)
    {
        return json_decode(json_encode($data, true));
    }
}
