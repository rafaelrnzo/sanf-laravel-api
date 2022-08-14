<?php

namespace Sanf\Core\Modules\Faq\Repositories;

interface FaqRepositoryInterface
{
    public function list($limit, $offset, $orderBy, $orderDirection, $search);
}
