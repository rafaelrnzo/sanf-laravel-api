<?php

namespace Sanf\Core\Modules\Scanina\Repositories;

interface ScaninaProductRepositoryInterface
{
    public function get($specification);
    public function post($specification);
}
