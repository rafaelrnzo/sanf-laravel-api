<?php


namespace NbsPhp\Core\Repositories;


abstract class BaseEloquentRepository
{
    protected function stripEloquentModel($data)
    {
        return json_decode(json_encode($data, JSON_THROW_ON_ERROR), false, 512, JSON_THROW_ON_ERROR);
    }
}
