<?php


namespace NbsPhp\Core;


use NbsPhp\Core\Models\AbstractModel;

interface SpecificationInterface
{
    public function buildQuery(AbstractModel $model);
}
